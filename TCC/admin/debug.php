<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Relatório de Diagnóstico Técnico</h2>";

if (!file_exists('includes/conexao.php')) {
    die("<p style='color:red'>❌ ERRO: O arquivo 'conexao.php' não foi encontrado na pasta!</p>");
}

include 'includes/conexao.php';
echo "<p style='color:green'>✅ Arquivo 'conexao.php' carregado.</p>";


if (!isset($conn) || $conn === null) {
    die("<p style='color:red'>❌ ERRO: A variável \$conn é nula. Verifique se você a definiu no conexao.php</p>");
}

if ($conn->connect_error) {
    die("<p style='color:red'>❌ ERRO de Conexão MySQL: " . $conn->connect_error . "</p>");
}
echo "<p style='color:green'>✅ Conexão com o MySQL Workbench estabelecida!</p>";

$check_table = $conn->query("SHOW TABLES LIKE 'usuarios'");
if ($check_table->num_rows == 0) {
    die("<p style='color:orange'>⚠️ AVISO: A tabela 'usuarios' não existe no banco '$banco'.</p>");
}
echo "<p style='color:green'>✅ Tabela 'usuarios' encontrada.</p>";

$res = $conn->query("SELECT * FROM usuarios WHERE email = 'teste@tech.com'");
if ($res->num_rows == 0) {
    echo "<p style='color:orange'>⚠️ O usuário 'teste@tech.com' não existe. Criando agora para você...</p>";
    $hash = password_hash('123456', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO usuarios (nome, email, senha, tipo) VALUES ('Teste', 'teste@tech.com', '$hash', 'tech')");
    echo "<p style='color:green'>✅ Usuário criado! Tente logar com: teste@tech.com / 123456</p>";
} else {
    $user = $res->fetch_assoc();
    echo "<p style='color:green'>✅ Usuário de teste localizado no banco!</p>";

    $teste_senha = password_verify('123456', $user['senha']);
    if ($teste_senha) {
        echo "<p style='color:green'>⭐ SUCESSO: A senha '123456' funciona perfeitamente com este banco.</p>";
    } else {
        echo "<p style='color:red'>❌ ERRO: A senha '123456' NÃO bate com o que está no banco.</p>";
        echo "Hash atual no banco: " . $user['senha'];
    }
}
?>