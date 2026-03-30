<?php
include 'includes/conexao.php';

// 1. Geramos o hash oficial que o PHP entende
$senha_limpa = '123456';
$novo_hash = password_hash($senha_limpa, PASSWORD_DEFAULT);

// 2. Atualizamos o banco de dados
$sql = "UPDATE usuarios SET senha = '$novo_hash' WHERE email = 'teste@tech.com'";

if ($conn->query($sql)) {
    echo "<h2>✅ Sucesso!</h2>";
    echo "O usuário <b>teste@tech.com</b> agora tem a senha <b>123456</b> cadastrada corretamente.<br>";
    echo "O hash salvo no banco agora é: <small>$novo_hash</small><br><br>";
    echo "<a href='login.php'>Ir para o Login</a>";
} else {
    echo "Erro ao atualizar: " . $conn->error;
}
?>