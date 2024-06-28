<?php

include_once('../config/conexao.php');

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $setor = $_POST['setor'];
    $nivel_acesso = $_POST['nivel_acesso'];

    $sqlUpdate = $conexao->prepare("UPDATE usuario SET email=?, setor=?, nivel_acesso=? WHERE id=?");
    $sqlUpdate->bind_param('sssi', $email, $setor, $nivel_acesso, $id);

    $result = $sqlUpdate->execute();

    if ($result) {
        header('Location: ../index.php');
    } else {
        echo "Erro ao atualizar o registro: " . $conexao->error;
    }
} else {
    echo "Formulário não enviado.";
}

?>
