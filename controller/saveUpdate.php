<?php

    include_once('../config/conexao.php');

    if(isset($_POST['update'])){
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $estado = $_POST['estado'];
        $prioridade = $_POST['prioridade'];

        $sqlUpdate = "UPDATE tickets SET titulo='$titulo',descricao='$descricao',estado='$estado',prioridade='$prioridade'
        WHERE id='$id'";

        $result = $conexao->query($sqlUpdate);
    } 
        header('Location: ../index.php');

?>