<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}


$erro = $_SESSION['erro'] ?? null;
$sucesso = $_SESSION['sucesso'] ?? null;
unset($_SESSION['erro'], $_SESSION['sucesso']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta | Técnico Connect</title>
    <link rel="stylesheet" href="styles/loginstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
</head>
<body id="mainBody">

<div class="login-card" id="card">
    <div class="tabs-auth">
        <button class="tab-btn active" id="tabCliente" onclick="toggleMode('cliente')">SOU CLIENTE</button>
        <button class="tab-btn" id="tabEmpresa" onclick="toggleMode('empresa')">SOU EMPRESA</button>
    </div>

    <h2 id="welcomeText" style="margin-bottom: 10px;">Cadastro de Cliente</h2>
    <p style="color: #666; margin-bottom: 30px; font-size: 0.9rem;">Preencha os dados para criar sua conta</p>

    <?php if ($erro): ?>
        <div class="error-message" style="margin-bottom: 20px; padding: 12px; background: #fde6e6; border-left: 4px solid #e74c3c; border-radius: 8px; color: #e74c3c; font-size: 0.9rem;">
             <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="success-message">
            ✓ <?php echo htmlspecialchars($sucesso); ?>
        </div>
    <?php endif; ?>



    <form action="processar_cadastro.php" method="POST" id="formCliente" class="form-auth">
        <input type="hidden" name="tipo_usuario" value="CLIENTE">
        
        <div class="form-group">
            <label for="nome_cliente">Nome Completo *</label>
            <input type="text" id="nome_cliente" name="nome_completo" placeholder="Digite seu nome completo" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="cpf_cliente">CPF *</label>
            <input type="text" id="cpf_cliente" name="cpf" placeholder="000.000.000-00" class="auth-input" required maxlength="14" inputmode="numeric">
        </div>

        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento *</label>
            <input type="date" id="data_nascimento" name="data_nascimento" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="estado_civil">Estado Civil *</label>
            <select id="estado_civil" name="estado_civil" class="auth-input" required>
                <option value="">Selecione seu estado civil</option>
                <option value="Solteiro(a)">Solteiro(a)</option>
                <option value="Casado(a)">Casado(a)</option>
                <option value="Divorciado(a)">Divorciado(a)</option>
                <option value="Viúvo(a)">Viúvo(a)</option>
                <option value="Separado(a)">Separado(a)</option>
                <option value="União estável">União estável</option>
            </select>
        </div>

        <div class="form-group">
            <label for="endereco_cliente">Endereço Completo *</label>
            <input type="text" id="endereco_cliente" name="endereco" placeholder="Rua, número, complemento, cidade, estado" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="email_cliente">E-mail *</label>
            <input type="email" id="email_cliente" name="email" placeholder="seu.email@exemplo.com" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="senha_cliente">Senha *</label>
            <input type="password" id="senha_cliente" name="senha" placeholder="Mínimo 8 caracteres" class="auth-input" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirmar_senha_cliente">Confirmar Senha *</label>
            <input type="password" id="confirmar_senha_cliente" name="confirmar_senha" placeholder="Confirme sua senha" class="auth-input" required minlength="8">
        </div>

        <div class="form-group checkbox">
            <input type="checkbox" id="termos_cliente" name="termos" required>
            <label for="termos_cliente">Concordo com os <a href="#" target="_blank">Termos de Serviço</a> e <a href="#" target="_blank">Política de Privacidade</a></label>
        </div>

        <button type="submit" class="btn-entrar">CRIAR CONTA COMO CLIENTE</button>
    </form>




    <form action="processar_cadastro.php" method="POST" id="formEmpresa" class="form-auth" style="display:none;">
        <input type="hidden" name="tipo_usuario" value="EMPRESA">
        
        <div class="form-group">
            <label for="nome_empresa">Nome da Empresa *</label>
            <input type="text" id="nome_empresa" name="nome_empresas" placeholder="Nome completo da empresa" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="cnpj">CNPJ *</label>
            <input type="text" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00" class="auth-input" required maxlength="18" inputmode="numeric">
        </div>

        <div class="form-group">
            <label for="endereco_empresa">Endereço Comercial *</label>
            <input type="text" id="endereco_empresa" name="endereco" placeholder="Rua, número, complemento, cidade, estado, CEP" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="email_empresa">E-mail Corporativo *</label>
            <input type="email" id="email_empresa" name="email" placeholder="contato@empresa.com" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="senha_empresa">Senha *</label>
            <input type="password" id="senha_empresa" name="senha" placeholder="Mínimo 8 caracteres" class="auth-input" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirmar_senha_empresa">Confirmar Senha *</label>
            <input type="password" id="confirmar_senha_empresa" name="confirmar_senha" placeholder="Confirme sua senha" class="auth-input" required minlength="8">
        </div>

        <div class="form-group checkbox">
            <input type="checkbox" id="termos_empresa" name="termos" required>
            <label for="termos_empresa">Concordo com os <a href="#" target="_blank">Termos de Serviço</a> e <a href="#" target="_blank">Política de Privacidade</a></label>
        </div>

        <button type="submit" class="btn-entrar">CRIAR CONTA COMO EMPRESA</button>
    </form>

    <div style="margin-top: 25px; text-align: center;">
        <p style="color: #666; font-size: 0.9rem;">Já possui conta? <a href="login.php" style="color:#8A05BE; text-decoration:none; font-weight:600;">Faça login</a></p>
        <a href="index.php" style="color:#8A05BE; text-decoration:none; font-size:0.8rem; font-weight:600;">← Voltar ao Início</a>
    </div>
</div>




<script>
    function toggleMode(mode) {
        const body = document.getElementById('mainBody');
        const welcome = document.getElementById('welcomeText');
        const btnCliente = document.getElementById('tabCliente');
        const btnEmpresa = document.getElementById('tabEmpresa');
        const formCliente = document.getElementById('formCliente');
        const formEmpresa = document.getElementById('formEmpresa');

        if (mode === 'empresa') {
            body.classList.add('mode-company');
            welcome.innerText = "Cadastro de Empresa";
            btnEmpresa.classList.add('active');
            btnCliente.classList.remove('active');
            formCliente.style.display = 'none';
            formEmpresa.style.display = 'flex';
        } else {
            body.classList.remove('mode-company');
            welcome.innerText = "Cadastro de Cliente";
            btnCliente.classList.add('active');
            btnEmpresa.classList.remove('active');
            formCliente.style.display = 'flex';
            formEmpresa.style.display = 'none';
        }
    }

    // Validação de senhas - Cliente
    document.getElementById('formCliente').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha_cliente').value;
        const confirmar = document.getElementById('confirmar_senha_cliente').value;
        
        if (senha !== confirmar) {
            e.preventDefault();
            alert('As senhas não coincidem!');
        }
    });

    // Validação de senhas - Empresa
    document.getElementById('formEmpresa').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha_empresa').value;
        const confirmar = document.getElementById('confirmar_senha_empresa').value;
        
        if (senha !== confirmar) {
            e.preventDefault();
            alert('As senhas não coincidem!');
        }
    });

    document.getElementById('cpf_cliente').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });

    document.getElementById('cnpj').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 14) value = value.slice(0, 14);
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        e.target.value = value;
    });
</script>

</body>
</html>