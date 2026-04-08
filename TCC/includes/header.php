<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo : "Técnico Connect"; ?></title>
    <link rel="stylesheet" href="styles/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<nav>
    <a href="index.php" class="logo">Técnico Connect</a>
    <div class="nav-links">
        <a href="sobre.php">Sobre</a>
        <a href="vagas.php">Vagas</a>
        <a href="guia-curriculo.php">Currículo</a>
        <a href="quiz.php" class="btn-roxo btn-padding-quiz">Fazer Quiz</a>
        <a href="login.php" class="btn-login-nav">Entrar</a>
    </div>
</nav>