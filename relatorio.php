<?php 
session_start();
include_once('./config/conexao.php');
if ((!isset($_SESSION['email']) == true) && (!isset($_SESSION['senha']) == true)) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: login.php');
}
$logado = $_SESSION['email'];

// Consulta por Ano
$sqlAno = "SELECT YEAR(data_criacao) AS ano,
    SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
    SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
    SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
    FROM tickets 
    GROUP BY YEAR(data_criacao)
    ORDER BY ano";

$resultAno = $conexao->query($sqlAno);

if (!$resultAno) {
    die("Erro na consulta: " . $conexao->error);
}

$labelsAno = [];
$abertosAno = [];
$fechadosAno = [];
$atrasadosAno = [];

while ($row = mysqli_fetch_assoc($resultAno)) {
    $labelsAno[] = $row['ano'];
    $abertosAno[] = $row['abertos'];
    $fechadosAno[] = $row['fechados'];
    $atrasadosAno[] = $row['atrasados'];
}

$anoAtual = date('Y');
$mesAtual = date('m');

// Consulta por Mês
$sqlMes = "SELECT DATE_FORMAT(data_criacao, '%Y-%m') AS mes,
    SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
    SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
    SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
    FROM tickets 
    WHERE YEAR(data_criacao) = ?
    GROUP BY YEAR(data_criacao), MONTH(data_criacao)
    ORDER BY mes";

$stmtMes = $conexao->prepare($sqlMes);
if (!$stmtMes) {
    die("Erro na preparação da consulta: " . $conexao->error);
}
$stmtMes->bind_param("i", $anoAtual);
$stmtMes->execute();
$resultMes = $stmtMes->get_result();

if (!$resultMes) {
    die("Erro na execução da consulta: " . $conexao->error);
}

$labelsMes = [];
$abertosMes = [];
$fechadosMes = [];
$atrasadosMes = [];

while ($rowMes = mysqli_fetch_assoc($resultMes)) {
    $labelsMes[] = $rowMes['mes'];
    $abertosMes[] = $rowMes['abertos'];
    $fechadosMes[] = $rowMes['fechados'];
    $atrasadosMes[] = $rowMes['atrasados'];
}

// Consulta por Semana
$sqlSemana = "SELECT YEAR(data_criacao) AS ano,
    WEEK(data_criacao) AS semana,
    SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
    SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
    SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
    FROM tickets 
    WHERE YEAR(data_criacao) = ? AND MONTH(data_criacao) = ?
    GROUP BY YEAR(data_criacao), WEEK(data_criacao)
    ORDER BY ano, semana";

$stmtSemana = $conexao->prepare($sqlSemana);
if (!$stmtSemana) {
    die("Erro na preparação da consulta: " . $conexao->error);
}
$stmtSemana->bind_param("ii", $anoAtual, $mesAtual);
$stmtSemana->execute();
$resultSemana = $stmtSemana->get_result();

if (!$resultSemana) {
    die("Erro na execução da consulta: " . $conexao->error);
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

// Paginação e Consulta Geral
$pagina = 1;
if (isset($_GET['pagina'])) {
    $pagina = filter_input(INPUT_GET, "pagina", FILTER_VALIDATE_INT);
}
if (!$pagina) {
    $pagina = 1;
}
$limite = 3;
$inicio = ($pagina * $limite) - $limite;

$sql = "SELECT * FROM tickets ORDER BY id DESC LIMIT ?, ?";
$stmt = $conexao->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conexao->error);
}
$stmt->bind_param("ii", $inicio, $limite);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Erro na execução da consulta: " . $conexao->error);
}

$registros = "SELECT COUNT(id) as count FROM tickets";
$resultRegistros = $conexao->query($registros);

if (!$resultRegistros) {
    die("Erro na contagem de registros: " . $conexao->error);
}

$totalRegistros = mysqli_fetch_assoc($resultRegistros)['count'];
$paginas = ceil($totalRegistros / $limite);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./src/css/sidebar.css">
    <title>Sistema</title>
</head>
<body>
    <?php include('./view/navbar.php'); ?>
    
    <main>
        <div id="filter">
            <form id="filterForm">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado">
                    <option value="">Todos</option>
                    <option value="aberto">Aberto</option>
                    <option value="fechado">Fechado</option>
                    <option value="atrasado">Atrasado</option>
                </select>
                
                <label for="prioridade">Prioridade:</label>
                <select name="prioridade" id="prioridade">
                    <option value="">Todas</option>
                    <option value="alta">Alta</option>
                    <option value="media">Média</option>
                    <option value="baixa">Baixa</option>
                </select>

                <label for="dataInicio">Data de Início:</label>
                <input type="date" id="dataInicio" name="dataInicio">
                
                <label for="dataFim">Data de Fim:</label>
                <input type="date" id="dataFim" name="dataFim">

                <button type="submit">Filtrar</button>
            </form>
            <a href="./controller/exportar.php" class="btn_exportar">Exportar</a>
        </div>
        
        <br>

        <div class="ChartBoxR">
            <?php include('./view/chart1.php'); ?>
            <?php include('./view/chart2.php'); ?>
            <?php include('./view/chart3.php'); ?>
        </div>

        <div class="container">
            <h1>Tabela de acompanhamento</h1>
            <br>
            <table class="content-table">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>titulo</td>
                        <td>descricao</td>
                        <td>estado</td>
                        <td>prioridade</td>
                        <td>local</td>
                        <td>requisitante</td>
                        <td>data de criação</td>
                        <td>data de entrega</td>
                        <td>ações</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        while ($user_data = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $user_data['id'] . "</td>";
                            echo "<td>" . $user_data['titulo'] . "</td>";
                            echo "<td>" . $user_data['descricao'] . "</td>";
                            echo "<td>" . $user_data['estado'] . "</td>";
                            echo "<td>" . $user_data['prioridade'] . "</td>";
                            echo "<td>" . $user_data['local'] . "</td>";
                            echo "<td>" . $user_data['email_usuario'] . "</td>";
                            echo "<td>" . $user_data['data_criacao'] . "</td>";
                            if($user_data['estado'] == 'fechado'){
                                echo "<td>concluido</td>";
                            }else{
                                echo "<td>" . $user_data['data_entrega'] . "</td>";
                            }
                            echo "<td> <a href='./controller/updateController.php?id=$user_data[id]'>atualizar</a>
                                <a href='./controller/deleteController.php?id=$user_data[id]'>deletar</a> 
                             </td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <div class="pagination">
                <a href="?pagina=1">Primeira</a>
                <?php if ($pagina > 1): ?>
                    <a href="?pagina=<?php echo $pagina-1; ?>">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                <?php echo $pagina; ?>
                <?php if ($pagina < $paginas): ?>
                    <a href="?pagina=<?php echo $pagina+1; ?>">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
                <a href="?pagina=<?php echo $paginas; ?>">Última</a>
            </div>
        </div>
      
    </main>
    <script src="./src/js/script.js"></script>
    <script>
        document.getElementById('filterForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const estado = document.getElementById('estado').value;
    const prioridade = document.getElementById('prioridade').value;
    const dataInicio = document.getElementById('dataInicio').value;
    const dataFim = document.getElementById('dataFim').value;

    const formData = new FormData();
    formData.append('estado', estado);
    formData.append('prioridade', prioridade);
    formData.append('dataInicio', dataInicio);
    formData.append('dataFim', dataFim);

    fetch('./controller/filtragemController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.tabela && data.graficos && data.paginacao) {
            atualizarTabela(data.tabela);
            atualizarGraficos(data.graficos);
            atualizarPaginacao(data.paginacao);
        } else {
            console.error('Dados retornados estão em um formato inesperado', data);
        }
    })
    .catch(error => console.error('Erro ao filtrar dados:', error));
});


    function atualizarTabela(dadosTabela) {
        const tbody = document.querySelector('.content-table tbody');
        tbody.innerHTML = '';

        dadosTabela.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.id}</td>
                <td>${row.titulo}</td>
                <td>${row.descricao}</td>
                <td>${row.estado}</td>
                <td>${row.prioridade}</td>
                <td>${row.email_usuario}</td>
                <td>${row.data_criacao}</td>
                <td>${row.data_entrega}</td>
                <td>
                    <a href='./controller/updateController.php?id=${row.id}'>atualizar</a>
                    <a href='./controller/deleteController.php?id=${row.id}'>deletar</a>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function atualizarGraficos(dadosGraficos) {
    if (!dadosGraficos) {
        console.error('dadosGraficos está undefined');
        return;
    }

    if (dadosGraficos.labelsAno && dadosGraficos.abertosAno && dadosGraficos.fechadosAno && dadosGraficos.atrasadosAno) {
        myChartAno.data.labels = dadosGraficos.labelsAno;
        myChartAno.data.datasets[0].data = dadosGraficos.abertosAno;
        myChartAno.data.datasets[1].data = dadosGraficos.fechadosAno;
        myChartAno.data.datasets[2].data = dadosGraficos.atrasadosAno;
        myChartAno.update();
    } else {
        console.error('Um ou mais elementos em dadosGraficos.Ano estão undefined');
    }

    if (dadosGraficos.labelsMes && dadosGraficos.abertosMes && dadosGraficos.fechadosMes && dadosGraficos.atrasadosMes) {
        myChartMes.data.labels = dadosGraficos.labelsMes;
        myChartMes.data.datasets[0].data = dadosGraficos.abertosMes;
        myChartMes.data.datasets[1].data = dadosGraficos.fechadosMes;
        myChartMes.data.datasets[2].data = dadosGraficos.atrasadosMes;
        myChartMes.update();
    } else {
        console.error('Um ou mais elementos em dadosGraficos.Mes estão undefined');
    }


    if (dadosGraficos.labelsSemana && dadosGraficos.abertosSemana && dadosGraficos.fechadosSemana && dadosGraficos.atrasadosSemana) {
        myChartSemana.data.labels = dadosGraficos.labelsSemana;
        myChartSemana.data.datasets[0].data = dadosGraficos.abertosSemana;
        myChartSemana.data.datasets[1].data = dadosGraficos.fechadosSemana;
        myChartSemana.data.datasets[2].data = dadosGraficos.atrasadosSemana;
        myChartSemana.update();
    } else {
        console.error('Um ou mais elementos em dadosGraficos.Semana estão undefined');
    }
}


        function atualizarPaginacao(dataPaginacao) {
    const paginacaoDiv = document.querySelector('.paginacao');
    paginacaoDiv.innerHTML = '';

    for (let i = 1; i <= dataPaginacao.paginas; i++) {
        const link = document.createElement('a');
        link.href = `?pagina=${i}`;
        link.textContent = i;
        paginacaoDiv.appendChild(link);

        if (i < dataPaginacao.paginas) {
            paginacaoDiv.appendChild(document.createTextNode(' | '));
        }
    }
}
    </script>
</body>
</html>
