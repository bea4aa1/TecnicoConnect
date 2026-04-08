<?php
session_start();
include 'includes/conexao.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $conn->real_escape_string($_POST['email']);
    $senha_digitada = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        

        if (password_verify($senha_digitada, $usuario['senha'])) {
           $_SESSION['user_id'] = $usuario['id'];
           $_SESSION['user_nome'] = $usuario['nome']; 
           $_SESSION['user_type'] = $usuario['tipo'];
           $_SESSION['user_score'] = $usuario['score'];    
            
            header("Location: dashboard.php"); 
            exit();
        } else {
            echo "<script>alert('Senha incorreta!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado!'); window.location.href='login.php';</script>";
    }
}
?>