<?php
session_start();

// Validações básicas
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastro.php");
    exit();
}

// Conectar ao banco de dados
require_once 'config/database.php';

$tipo = $_POST['tipo'] ?? null;
$erros = [];

// Validações gerais
if (empty($_POST['email'])) {
    $erros[] = "E-mail é obrigatório";
}

if (empty($_POST['senha']) || strlen($_POST['senha']) < 8) {
    $erros[] = "Senha deve ter no mínimo 8 caracteres";
}

if ($_POST['senha'] !== $_POST['confirmar_senha']) {
    $erros[] = "As senhas não coincidem";
}

if (empty($_POST['termos'])) {
    $erros[] = "Você deve aceitar os Termos de Serviço";
}

// Validações específicas para Técnico
if ($tipo === 'tech') {
    if (empty($_POST['nome'])) {
        $erros[] = "Nome é obrigatório";
    }
    
    if (empty($_POST['cpf'])) {
        $erros[] = "CPF é obrigatório";
    } else {
        $cpf = preg_replace('/\D/', '', $_POST['cpf']);
        if (strlen($cpf) !== 11) {
            $erros[] = "CPF inválido";
        }
    }
    
    if (empty($_POST['telefone'])) {
        $erros[] = "Telefone é obrigatório";
    }
    
    if (empty($_POST['especialidade'])) {
        $erros[] = "Especialidade é obrigatória";
    }
    
    if (empty($_POST['experiencia']) || $_POST['experiencia'] < 0) {
        $erros[] = "Anos de experiência inválido";
    }
    
    if (empty($_POST['localizacao'])) {
        $erros[] = "Cidade/Estado é obrigatório";
    }
}
// Validações específicas para Empresa
elseif ($tipo === 'company') {
    if (empty($_POST['razao_social'])) {
        $erros[] = "Razão social é obrigatória";
    }
    
    if (empty($_POST['cnpj'])) {
        $erros[] = "CNPJ é obrigatório";
    } else {
        $cnpj = preg_replace('/\D/', '', $_POST['cnpj']);
        if (strlen($cnpj) !== 14) {
            $erros[] = "CNPJ inválido";
        }
    }
    
    if (empty($_POST['nome_responsavel'])) {
        $erros[] = "Nome do responsável é obrigatório";
    }
    
    if (empty($_POST['ramo_atividade'])) {
        $erros[] = "Ramo de atividade é obrigatório";
    }
    
    if (empty($_POST['endereco'])) {
        $erros[] = "Endereço é obrigatório";
    }
    
    if (empty($_POST['cidade'])) {
        $erros[] = "Cidade é obrigatória";
    }
    
    if (empty($_POST['cep'])) {
        $erros[] = "CEP é obrigatório";
    }
} else {
    $erros[] = "Tipo de usuário inválido";
}

// Se houver erros, redirecionar com mensagem
if (!empty($erros)) {
    $_SESSION['erro'] = implode(', ', $erros);
    header("Location: cadastro.php");
    exit();
}

// Hash da senha
$senha_hash = password_hash($_POST['senha'], PASSWORD_BCRYPT);

try {
    if ($tipo === 'tech') {
        // Verificar se e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM tecnicos WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Este e-mail já está registrado");
        }
        
        // Inserir técnico
        $stmt = $pdo->prepare("
            INSERT INTO tecnicos (nome, email, cpf, telefone, especialidade, experiencia, localizacao, senha, data_cadastro, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'ativo')
        ");
        
        $stmt->execute([
            $_POST['nome'],
            $_POST['email'],
            preg_replace('/\D/', '', $_POST['cpf']),
            preg_replace('/\D/', '', $_POST['telefone']),
            $_POST['especialidade'],
            $_POST['experiencia'],
            $_POST['localizacao'],
            $senha_hash
        ]);
        
        $_SESSION['sucesso'] = "Conta de técnico criada com sucesso! Faça login para continuar.";
    } 
    elseif ($tipo === 'company') {
        // Verificar se e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM empresas WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Este e-mail já está registrado");
        }
        
        // Inserir empresa
        $stmt = $pdo->prepare("
            INSERT INTO empresas (razao_social, cnpj, email, telefone, nome_responsavel, ramo_atividade, endereco, cidade, cep, senha, data_cadastro, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'ativo')
        ");
        
        $stmt->execute([
            $_POST['razao_social'],
            preg_replace('/\D/', '', $_POST['cnpj']),
            $_POST['email'],
            preg_replace('/\D/', '', $_POST['telefone']),
            $_POST['nome_responsavel'],
            $_POST['ramo_atividade'],
            $_POST['endereco'],
            $_POST['cidade'],
            preg_replace('/\D/', '', $_POST['cep']),
            $senha_hash
        ]);
        
        $_SESSION['sucesso'] = "Conta de empresa criada com sucesso! Faça login para continuar.";
    }
    
    header("Location: login.php");
    exit();
    
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro ao criar conta. Tente novamente.";
    header("Location: cadastro.php");
    exit();
} catch (Exception $e) {
    $_SESSION['erro'] = $e->getMessage();
    header("Location: cadastro.php");
    exit();
}
?>