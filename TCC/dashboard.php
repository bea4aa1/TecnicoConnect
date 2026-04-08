<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$nomeUsuario = isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : "Usuário";
$tipoUsuario = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : "tech";
$scoreUsuario = isset($_SESSION['user_score']) ? $_SESSION['user_score'] : 0; 

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel | Técnico Connect</title>
    <link rel="stylesheet" href="styles/styledash.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="display: flex; height: 100vh; overflow: hidden; background: #f0f2f5;">

    <?php include 'includes/sidebar.php'; ?>

    <main class="app-content" style="flex: 1; padding: 40px; overflow-y: auto;">
        
        <?php if ($page == 'home'): ?>
            <header class="header-app">
                <h1>Bem-vindo de volta!</h1>
                <p>Aqui está o resumo da sua carreira técnica hoje.</p>
            </header>

            <div class="match-banner" style="background: linear-gradient(135deg, #8A05BE, #4a0063); color: white; padding: 30px; border-radius: 20px; margin-top: 20px;">
                <h2>Seu Score Técnico: 850</h2>
                <p>Você está no top 10% dos especialistas da sua região.</p>
            </div>

        <?php elseif ($page == 'academy'): ?>
            <h1>Central de Falhas (Troubleshooting)</h1>
            <p>Consulte códigos de erro de máquinas em tempo real.</p>
            <div id="academy-list" style="margin-top: 20px;">
                <div class="card" style="background: white; padding: 20px; border-radius: 15px; margin-bottom: 10px;">
                    <strong>F001 - Inversor Weg</strong>
                    <p>Sobretensão no Link DC. Verifique a rede elétrica.</p>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <script src="app-core.js"></script>
</body>
</html>