<?php
$host = "localhost";
$usuario = "root"; 
$senha = "root"; // Coloque aqui a senha que você usa para entrar no Workbench
$banco = "tecnico_connect";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Define o charset para não ter problemas com acentos
$conn->set_charset("utf8");
?>