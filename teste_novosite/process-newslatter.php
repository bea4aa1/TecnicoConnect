<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Aqui você poderia salvar no banco de dados. 
        // Por enquanto, vamos apenas simular um sucesso:
        echo "<script>alert('E-mail $email cadastrado com sucesso!'); window.location.href='index.php';</script>";
    } else {
        echo "E-mail inválido.";
    }
}
?>