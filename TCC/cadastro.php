<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
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
        <button class="tab-btn active" id="tabTech" onclick="toggleMode('tech')">SOU TÉCNICO</button>
        <button class="tab-btn" id="tabComp" onclick="toggleMode('company')">SOU EMPRESA</button>
    </div>

    <h2 id="welcomeText" style="margin-bottom: 10px;">Cadastro de Técnico Especialista</h2>
    <p style="color: #666; margin-bottom: 30px; font-size: 0.9rem;">Preencha os dados para criar sua conta</p>

    <!-- FORMULÁRIO TÉCNICO -->
    <form action="processar_cadastro.php" method="POST" id="formTech" class="form-auth">
        <input type="hidden" name="tipo" value="tech">
        
        <div class="form-group">
            <label for="nome_tech">Nome Completo *</label>
            <input type="text" id="nome_tech" name="nome" placeholder="Digite seu nome completo" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="email_tech">E-mail *</label>
            <input type="email" id="email_tech" name="email" placeholder="seu.email@exemplo.com" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="cpf_tech">CPF *</label>
            <input type="text" id="cpf_tech" name="cpf" placeholder="000.000.000-00" class="auth-input" required maxlength="14">
        </div>

        <div class="form-group">
            <label for="telefone_tech">Telefone *</label>
            <input type="tel" id="telefone_tech" name="telefone" placeholder="(11) 99999-9999" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="especialidade_tech">Especialidade *</label>
            <select id="especialidade_tech" name="especialidade" class="auth-input" required>
                <option value="">Selecione sua especialidade</option>
                <option value="eletricista">Eletricista</option>
                <option value="encanador">Encanador</option>
                <option value="mecanico">Mecânico</option>
                <option value="carpinteiro">Carpinteiro</option>
                <option value="pintor">Pintor</option>
                <option value="vidraceiro">Vidraceiro</option>
                <option value="eletronico">Eletrônico</option>
                <option value="outro">Outro</option>
            </select>
        </div>

        <div class="form-group">
            <label for="experiencia_tech">Anos de Experiência *</label>
            <input type="number" id="experiencia_tech" name="experiencia" placeholder="Ex: 5" class="auth-input" min="0" max="70" required>
        </div>

        <div class="form-group">
            <label for="localizacao_tech">Cidade/Estado *</label>
            <input type="text" id="localizacao_tech" name="localizacao" placeholder="São Paulo, SP" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="senha_tech">Senha *</label>
            <input type="password" id="senha_tech" name="senha" placeholder="Mínimo 8 caracteres" class="auth-input" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirmar_senha_tech">Confirmar Senha *</label>
            <input type="password" id="confirmar_senha_tech" name="confirmar_senha" placeholder="Confirme sua senha" class="auth-input" required minlength="8">
        </div>

        <div class="form-group checkbox">
            <input type="checkbox" id="termos_tech" name="termos" required>
            <label for="termos_tech" style="margin: 0; font-size: 0.85rem;">Concordo com os <a href="#" style="color: #8A05BE;">Termos de Serviço</a> e <a href="#" style="color: #8A05BE;">Política de Privacidade</a></label>
        </div>

        <button type="submit" class="btn-entrar">CRIAR CONTA COMO TÉCNICO</button>
    </form>

    <!-- FORMULÁRIO EMPRESA -->
    <form action="processar_cadastro.php" method="POST" id="formCompany" class="form-auth" style="display:none;">
        <input type="hidden" name="tipo" value="company">
        
        <div class="form-group">
            <label for="razao_social">Razão Social *</label>
            <input type="text" id="razao_social" name="razao_social" placeholder="Nome da empresa" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="cnpj">CNPJ *</label>
            <input type="text" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00" class="auth-input" required maxlength="18">
        </div>

        <div class="form-group">
            <label for="email_empresa">E-mail Corporativo *</label>
            <input type="email" id="email_empresa" name="email" placeholder="contato@empresa.com" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="telefone_empresa">Telefone *</label>
            <input type="tel" id="telefone_empresa" name="telefone" placeholder="(11) 3333-3333" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="nome_responsavel">Nome do Responsável *</label>
            <input type="text" id="nome_responsavel" name="nome_responsavel" placeholder="Nome completo" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="ramo_atividade">Ramo de Atividade *</label>
            <input type="text" id="ramo_atividade" name="ramo_atividade" placeholder="Ex: Construção Civil" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="endereco_empresa">Endereço Comercial *</label>
            <input type="text" id="endereco_empresa" name="endereco" placeholder="Rua, número, complemento" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="cidade_empresa">Cidade/Estado *</label>
            <input type="text" id="cidade_empresa" name="cidade" placeholder="São Paulo, SP" class="auth-input" required>
        </div>

        <div class="form-group">
            <label for="cep_empresa">CEP *</label>
            <input type="text" id="cep_empresa" name="cep" placeholder="00000-000" class="auth-input" required maxlength="9">
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
            <label for="termos_empresa" style="margin: 0; font-size: 0.85rem;">Concordo com os <a href="#" style="color: #8A05BE;">Termos de Serviço</a> e <a href="#" style="color: #8A05BE;">Política de Privacidade</a></label>
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
        const btnTech = document.getElementById('tabTech');
        const btnComp = document.getElementById('tabComp');
        const formTech = document.getElementById('formTech');
        const formCompany = document.getElementById('formCompany');

        if (mode === 'company') {
            body.classList.add('mode-company');
            welcome.innerText = "Cadastro de Empresa Contratante";
            btnComp.classList.add('active');
            btnTech.classList.remove('active');
            formTech.style.display = 'none';
            formCompany.style.display = 'block';
        } else {
            body.classList.remove('mode-company');
            welcome.innerText = "Cadastro de Técnico Especialista";
            btnTech.classList.add('active');
            btnComp.classList.remove('active');
            formTech.style.display = 'block';
            formCompany.style.display = 'none';
        }
    }

    // Validação de senhas
    document.getElementById('formTech').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha_tech').value;
        const confirmar = document.getElementById('confirmar_senha_tech').value;
        
        if (senha !== confirmar) {
            e.preventDefault();
            alert('As senhas não coincidem!');
        }
    });

    document.getElementById('formCompany').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha_empresa').value;
        const confirmar = document.getElementById('confirmar_senha_empresa').value;
        
        if (senha !== confirmar) {
            e.preventDefault();
            alert('As senhas não coincidem!');
        }
    });

    // Máscaras de entrada
    document.getElementById('cpf_tech').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });

    document.getElementById('telefone_tech').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        e.target.value = value;
    });

    document.getElementById('telefone_empresa').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) value = value.slice(0, 10);
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
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

    document.getElementById('cep_empresa').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) value = value.slice(0, 8);
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = value;
    });
</script>

</body>
</html>