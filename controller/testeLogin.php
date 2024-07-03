<?php
session_start();

if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    include_once('../config/conexao.php');
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    $stmt = $conexao->prepare('SELECT senha FROM usuario WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($senhaHash);
    $stmt->fetch();
    
    if ($senhaHash) {
        if (password_verify($senha, $senhaHash)) {
            $_SESSION['email'] = $email;
            header('Location: ../index.php');
            exit();
        } else {
            header('Location: ../login.php?error=invalid_password');
            exit();
        }
    } else {
        header('Location: ../login.php?error=email_not_found');
        exit();
    }
    
    $stmt->close();
} else {
    header('Location: ../login.php?error=empty_fields');
    exit();
}
?>
