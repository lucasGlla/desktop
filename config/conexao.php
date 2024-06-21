<?php 

$dbHost = 'LocalHost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'formulario';

$conexao = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);

if($conexao->connect_error){
    die("Falha ao conectar: " . $conexao->connect_error);
}
    //echo "Conexão efetuada com sucesso";



?>