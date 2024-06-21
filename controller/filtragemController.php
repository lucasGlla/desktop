<?php
include_once('../config/conexao.php');

// Função para retornar um JSON de erro e encerrar o script
function returnError($message) {
    echo json_encode(['error' => $message]);
    exit;
}

// Verifique a conexão com o banco de dados
if ($conexao->connect_error) {
    returnError("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}

$estado = isset($_POST['estado']) ? $_POST['estado'] : '';
$prioridade = isset($_POST['prioridade']) ? $_POST['prioridade'] : '';
$dataInicio = isset($_POST['dataInicio']) ? $_POST['dataInicio'] : '';
$dataFim = isset($_POST['dataFim']) ? $_POST['dataFim'] : '';
$pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;

$limite = 5;
$inicio = ($pagina - 1) * $limite;

$whereClauses = [];

if ($estado) {
    $whereClauses[] = "estado = '" . $conexao->real_escape_string($estado) . "'";
}

if ($prioridade) {
    $whereClauses[] = "prioridade = '" . $conexao->real_escape_string($prioridade) . "'";
}

if ($dataInicio) {
    $whereClauses[] = "data_criacao >= '" . $conexao->real_escape_string($dataInicio) . "'";
}

if ($dataFim) {
    $whereClauses[] = "data_criacao <= '" . $conexao->real_escape_string($dataFim) . "'";
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Consulta para obter os registros com limite de paginação
$sqlTabela = "SELECT * FROM tickets $whereSql ORDER BY id DESC LIMIT $inicio, $limite";
$resultTabela = $conexao->query($sqlTabela);

if (!$resultTabela) {
    returnError("Erro na consulta de registros: " . $conexao->error);
}

$tabela = [];
while ($row = mysqli_fetch_assoc($resultTabela)) {
    $tabela[] = $row;
}

// Consulta para obter o total de registros
$sqlTotal = "SELECT COUNT(*) as total FROM tickets $whereSql";
$resultTotal = $conexao->query($sqlTotal);

if (!$resultTotal) {
    returnError("Erro na consulta de contagem total: " . $conexao->error);
}

$totalRegistros = $resultTotal->fetch_assoc()['total'];

// Consultas para os gráficos
$sqlAno = "SELECT YEAR(data_criacao) AS ano,
    SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
    SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
    SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
    FROM tickets 
    $whereSql
    GROUP BY YEAR(data_criacao)
    ORDER BY ano";
$resultAno = $conexao->query($sqlAno);

if (!$resultAno) {
    returnError("Erro na consulta de gráficos por ano: " . $conexao->error);
}

$labelsAno = [];
$abertosAno = [];
$fechadosAno = [];
$atrasadosAno = [];

while($row = mysqli_fetch_assoc($resultAno)){
    $labelsAno[] = $row['ano'];
    $abertosAno[] = $row['abertos'];
    $fechadosAno[] = $row['fechados'];
    $atrasadosAno[] = $row['atrasados'];
}

$anoAtual = date('Y');

$sqlMes = "SELECT DATE_FORMAT(data_criacao, '%Y-%m') AS mes,
        SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
        SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
        SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
        FROM tickets 
        $whereSql AND YEAR(data_criacao) = $anoAtual
        GROUP BY YEAR(data_criacao), MONTH(data_criacao)
        ORDER BY mes";
$resultMes = $conexao->query($sqlMes);

if (!$resultMes) {
    returnError("Erro na consulta de gráficos por mês: " . $conexao->error);
}

$labelsMes = [];
$abertosMes = [];
$fechadosMes = [];
$atrasadosMes = [];

while($rowMes = mysqli_fetch_assoc($resultMes)){
    $labelsMes[] = $rowMes['mes'];
    $abertosMes[] = $rowMes['abertos'];
    $fechadosMes[] = $rowMes['fechados'];
    $atrasadosMes[] = $rowMes['atrasados'];
}

$mesAtual = date('m');

$sqlSemana = "SELECT YEAR(data_criacao) AS ano,
    WEEK(data_criacao) AS semana,
    SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
    SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
    SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
    FROM tickets 
    $whereSql AND YEAR(data_criacao) = $anoAtual AND MONTH(data_criacao) = $mesAtual
    GROUP BY YEAR(data_criacao), WEEK(data_criacao)
    ORDER BY ano, semana";
$resultSemana = $conexao->query($sqlSemana);

if (!$resultSemana) {
    returnError("Erro na consulta de gráficos por semana: " . $conexao->error);
}

$labelsSemana = [];
$abertosSemana = [];
$fechadosSemana = [];
$atrasadosSemana = [];

while ($row = mysqli_fetch_assoc($resultSemana)) {
    $labelsSemana[] = "Semana " . $row['semana'] . " - " . $row['ano'];
    $abertosSemana[] = $row['abertos'];
    $fechadosSemana[] = $row['fechados'];
    $atrasadosSemana[] = $row['atrasados'];
}

$response = [
    'tabela' => $tabela,
    'graficos' => [
        'labelsAno' => $labelsAno,
        'abertosAno' => $abertosAno,
        'fechadosAno' => $fechadosAno,
        'atrasadosAno' => $atrasadosAno,
        'labelsMes' => $labelsMes,
        'abertosMes' => $abertosMes,
        'fechadosMes' => $fechadosMes,
        'atrasadosMes' => $atrasadosMes,
        'labelsSemana' => $labelsSemana,
        'abertosSemana' => $abertosSemana,
        'fechadosSemana' => $fechadosSemana,
        'atrasadosSemana' => $atrasadosSemana,
    ],
    'paginacao' => [
        'paginas' => ceil($totalRegistros / $limite),
        'paginaAtual' => $pagina
    ]
];

echo json_encode($response);
?>
