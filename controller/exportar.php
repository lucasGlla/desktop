<?php
session_start();
include_once('../config/conexao.php');

// Verificar se o usuário está logado
if ((!isset($_SESSION['email']) == true) && (!isset($_SESSION['senha']) == true)) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: ../login.php');
    exit;
}

// Define o nome do arquivo
$filename = "dados_tickets.csv";

// Enviar os cabeçalhos para forçar o download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="'. $filename .'"');
header('Cache-Control: max-age=0');

// Abre a saída para escrever
$output = fopen('php://output', 'w');

// Escreve o cabeçalho do CSV para os dados da tabela
fputcsv($output, ['ID', 'titulo', 'Desc', 'Estado', 'Prioridade', 'Requisitante', 'Data de criacao', 'Data de Entrega']);

// Consulta para obter os dados da tabela
$sql = "SELECT * FROM tickets ORDER BY id DESC";
$result = $conexao->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conexao->error);
}

// Escreve os dados da tabela no CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['titulo'],
        $row['descricao'],
        $row['estado'],
        $row['prioridade'],
        $row['email_usuario'],
        $row['data_criacao'],
        $row['estado'] == 'fechado' ? 'concluido' : $row['data_entrega']
    ]);
}

// Fecha a saída
fclose($output);
exit;
?>