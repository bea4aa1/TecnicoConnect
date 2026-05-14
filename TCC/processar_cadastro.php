<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastro.php");
    exit();
}

// Conectar ao banco de dados
require_once 'includes/conexao.php';

$tipo_usuario = $_POST['tipo_usuario'] ?? null;
$erros = [];


if (empty($_POST['email'])) {
    $erros[] = "E-mail é obrigatório";
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $erros[] = "E-mail inválido";
}

if (empty($_POST['senha'])) {
    $erros[] = "Senha é obrigatória";
} elseif (strlen($_POST['senha']) < 8) {
    $erros[] = "Senha deve ter no mínimo 8 caracteres";
}

if ($_POST['senha'] !== $_POST['confirmar_senha']) {
    $erros[] = "As senhas não coincidem";
}

if (empty($_POST['termos'])) {
    $erros[] = "Você deve aceitar os Termos de Serviço";
}

//cliente
if ($tipo_usuario === 'CLIENTE') {
    if (empty($_POST['nome_completo'])) {
        $erros[] = "Nome completo é obrigatório";
    }
    
    if (empty($_POST['cpf'])) {
        $erros[] = "CPF é obrigatório";
    } else {
        $cpf = preg_replace('/\D/', '', $_POST['cpf']);
        if (strlen($cpf) !== 11) {
            $erros[] = "CPF inválido (deve conter 11 dígitos)";
        } elseif (!validar_cpf($cpf)) {
            $erros[] = "CPF inválido";
        }
    }
    
    if (empty($_POST['data_nascimento'])) {
        $erros[] = "Data de nascimento é obrigatória";
    } else {
        $data = strtotime($_POST['data_nascimento']);
        if ($data > time()) {
            $erros[] = "Data de nascimento inválida";
        }
    }
    
    if (empty($_POST['estado_civil'])) {
        $erros[] = "Estado civil é obrigatório";
    }
    
    if (empty($_POST['endereco'])) {
        $erros[] = "Endereço é obrigatório";
    }
}


//empresa
elseif ($tipo_usuario === 'EMPRESA') {
    if (empty($_POST['nome_empresas'])) {
        $erros[] = "Nome da empresa é obrigatório";
    }
    
    if (empty($_POST['cnpj'])) {
        $erros[] = "CNPJ é obrigatório";
    } else {
        $cnpj = preg_replace('/\D/', '', $_POST['cnpj']);
        if (strlen($cnpj) !== 14) {
            $erros[] = "CNPJ inválido (deve conter 14 dígitos)";
        } elseif (!validar_cnpj($cnpj)) {
            $erros[] = "CNPJ inválido";
        }
    }
    
    if (empty($_POST['endereco'])) {
        $erros[] = "Endereço é obrigatório";
    }
} else {
    $erros[] = "Tipo de usuário inválido";
}

if (!empty($erros)) {
    $_SESSION['erro'] = implode(' | ', $erros);
    header("Location: cadastro.php");
    exit();
}

try {
    // Verificar se e-mail já existe
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT id FROM Usuarios WHERE email = '$email'");
    
    if ($result && $result->num_rows > 0) {
        throw new Exception("Este e-mail já está registrado");
    }

    // Hash da senha
    $senha_hash = password_hash($_POST['senha'], PASSWORD_BCRYPT);

    // ========== CADASTRO DE CLIENTE ==========
    if ($tipo_usuario === 'CLIENTE') {
        $cpf = preg_replace('/\D/', '', $_POST['cpf']);
        
        // Verificar se CPF já existe
        $result = $conn->query("SELECT id FROM Clientes WHERE cpf = '$cpf'");
        if ($result && $result->num_rows > 0) {
            throw new Exception("Este CPF já está registrado");
        }

        // Inserir usuário
        $sql_usuario = "INSERT INTO Usuarios (email, senha, tipo_usuario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_usuario);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $email, $senha_hash, $tipo_usuario);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao criar usuário: " . $stmt->error);
        }
        
        $usuario_id = $conn->insert_id;
        $stmt->close();

        // Inserir dados do cliente
        $nome_completo = $conn->real_escape_string($_POST['nome_completo']);
        $data_nascimento = $conn->real_escape_string($_POST['data_nascimento']);
        $estado_civil = $conn->real_escape_string($_POST['estado_civil']);
        $endereco = $conn->real_escape_string($_POST['endereco']);

        $sql_cliente = "INSERT INTO Clientes (usuario_id, nome_completo, cpf, data_nascimento, estado_civil, endereco) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_cliente);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $conn->error);
        }
        
        $stmt->bind_param("isssss", $usuario_id, $nome_completo, $cpf, $data_nascimento, $estado_civil, $endereco);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao criar cliente: " . $stmt->error);
        }
        
        $stmt->close();
        $_SESSION['sucesso'] = "Conta de cliente criada com sucesso! Faça login para continuar.";
    } 
    // ========== CADASTRO DE EMPRESA ==========
    elseif ($tipo_usuario === 'EMPRESA') {
        $cnpj = preg_replace('/\D/', '', $_POST['cnpj']);
        
        // Verificar se CNPJ já existe
        $result = $conn->query("SELECT id FROM Empresas WHERE cnpj = '$cnpj'");
        if ($result && $result->num_rows > 0) {
            throw new Exception("Este CNPJ já está registrado");
        }

        // Inserir usuário
        $sql_usuario = "INSERT INTO Usuarios (email, senha, tipo_usuario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_usuario);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $email, $senha_hash, $tipo_usuario);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao criar usuário: " . $stmt->error);
        }
        
        $usuario_id = $conn->insert_id;
        $stmt->close();

        // Inserir dados da empresa
        $nome_empresas = $conn->real_escape_string($_POST['nome_empresas']);
        $endereco = $conn->real_escape_string($_POST['endereco']);

        $sql_empresa = "INSERT INTO Empresas (usuario_id, nome_empresas, cnpj, endereco) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_empresa);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $conn->error);
        }
        
        $stmt->bind_param("isss", $usuario_id, $nome_empresas, $cnpj, $endereco);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao criar empresa: " . $stmt->error);
        }
        
        $stmt->close();
        $_SESSION['sucesso'] = "Conta de empresa criada com sucesso! Faça login para continuar.";
    }
    
    header("Location: login.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['erro'] = $e->getMessage();
    header("Location: cadastro.php");
    exit();
}

// ========== FUNÇÕES DE VALIDAÇÃO ==========

function validar_cpf($cpf) {
    if (strlen($cpf) !== 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Valida primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += intval($cpf[$i]) * (10 - $i);
    }
    
    $resto = $soma % 11;
    $digito1 = $resto < 2 ? 0 : 11 - $resto;
    
    if (intval($cpf[9]) !== $digito1) {
        return false;
    }
    
    // Valida segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += intval($cpf[$i]) * (11 - $i);
    }
    
    $resto = $soma % 11;
    $digito2 = $resto < 2 ? 0 : 11 - $resto;
    
    return intval($cpf[10]) === $digito2;
}

function validar_cnpj($cnpj) {
    if (strlen($cnpj) !== 14) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }
    
    // Valida primeiro dígito verificador
    $soma = 0;
    $multiplicador = 5;
    
    for ($i = 0; $i < 8; $i++) {
        $soma += intval($cnpj[$i]) * $multiplicador;
        $multiplicador--;
        if ($multiplicador === 1) {
            $multiplicador = 9;
        }
    }
    
    $resto = $soma % 11;
    $digito1 = $resto < 2 ? 0 : 11 - $resto;
    
    if (intval($cnpj[8]) !== $digito1) {
        return false;
    }
    
    // Valida segundo dígito verificador
    $soma = 0;
    $multiplicador = 6;
    
    for ($i = 0; $i < 9; $i++) {
        $soma += intval($cnpj[$i]) * $multiplicador;
        $multiplicador--;
        if ($multiplicador === 1) {
            $multiplicador = 9;
        }
    }
    
    $resto = $soma % 11;
    $digito2 = $resto < 2 ? 0 : 11 - $resto;
    
    return intval($cnpj[9]) === $digito2;
}
?>