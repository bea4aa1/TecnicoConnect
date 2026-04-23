<?php
// Verifica se há sessão ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Registra o logout no banco de dados
if (!empty($_SESSION['user_id'])) {
    require_once 'includes/conexao.php';
    
    $user_id = $_SESSION['user_id'];
    $conn->query("UPDATE Usuarios SET ultimo_logout = NOW() WHERE id = $user_id");
}

// Limpa as variáveis de autenticação
unset($_SESSION['user_id']);
unset($_SESSION['email']);
unset($_SESSION['tipo_usuario']);
unset($_SESSION['logged_in']);

// Redireciona para login
header("Location: login.php");
exit();
?>