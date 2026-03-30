<?php
session_start();
// Se o usuário já estiver logado, manda direto para o dashboard
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
    <title>Acesso Restrito | Técnico Connect</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8A05BE;
            --company-color: #1c1c1e;
        }

        body {
            background: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            transition: background 0.5s ease;
            margin: 0;
        }

        body.mode-company { background: #e2e8f0; }

        .login-card {
            background: white;
            padding: 45px;
            border-radius: 32px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .tabs-auth {
            display: flex;
            background: #f1f1f1;
            padding: 5px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.8rem;
            transition: 0.3s;
        }

        .tab-btn.active {
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .auth-input {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 12px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .btn-entrar {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: white;
            font-weight: 800;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body id="mainBody">

<div class="login-card" id="card">
    <div class="tabs-auth">
        <button class="tab-btn active" id="tabTech" onclick="toggleMode('tech')">SOU TÉCNICO</button>
        <button class="tab-btn" id="tabComp" onclick="toggleMode('company')">SOU EMPRESA</button>
    </div>

    <h2 id="welcomeText" style="margin-bottom: 10px;">Área do Técnico Especialista</h2>
    <p style="color: #666; margin-bottom: 30px; font-size: 0.9rem;">Entre com suas credenciais industriais</p>

    <form action="autenticar.php" method="POST">
        <input type="hidden" name="tipo" id="user_type_input" value="tech">
        
        <input type="email" name="email" placeholder="E-mail técnico" class="auth-input" required>
        <input type="password" name="senha" placeholder="Senha" class="auth-input" required>
        
        <button type="submit" class="btn-entrar">ENTRAR NO PAINEL</button>
    </form>

    <div style="margin-top: 25px;">
        <a href="index.php" style="color:#8A05BE; text-decoration:none; font-size:0.8rem; font-weight:600;">← Voltar ao Início</a>
    </div>
</div>

<script>
    function toggleMode(mode) {
        const body = document.getElementById('mainBody');
        const welcome = document.getElementById('welcomeText');
        const btnTech = document.getElementById('tabTech');
        const btnComp = document.getElementById('tabComp');
        const typeInput = document.getElementById('user_type_input');

        typeInput.value = mode; // Atualiza o valor para o PHP saber quem está logando

        if (mode === 'company') {
            body.classList.add('mode-company');
            welcome.innerText = "Painel de Recrutamento";
            btnComp.classList.add('active');
            btnTech.classList.remove('active');
        } else {
            body.classList.remove('mode-company');
            welcome.innerText = "Área do Técnico Especialista";
            btnTech.classList.add('active');
            btnComp.classList.remove('active');
        }
    }
</script>

</body>
</html>