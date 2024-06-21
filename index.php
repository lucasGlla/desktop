<?php 
    session_start();
    include_once('./config/conexao.php');
    if((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true)){
        unset($_SESSION['email']);
        unset($_SESSION['senha']);
        header('Location: login.php');
    }
    $logado = $_SESSION['email'];

    $sql_total_tickets = "SELECT COUNT(*) AS total_tickets FROM tickets";
    $result_total_tickets = $conexao->query($sql_total_tickets);
    $row_total_tickets = mysqli_fetch_assoc($result_total_tickets);
    $total_tickets = $row_total_tickets['total_tickets'];

    $sql = "SELECT * FROM tickets ORDER BY id DESC";

    $result = $conexao->query($sql);
    $data_atual = date('Y-m-d');
    $abertos = 0;
    $fechados = 0;
    $atrasados = 0;

    while($user_data = mysqli_fetch_assoc($result)){
        if($user_data['estado'] == 'aberto'){
            $abertos++;
        } else{
            $fechados++;
        }

        if($user_data['estado'] == 'aberto' && $user_data['data_entrega'] < $data_atual){
            $atrasados++;
        }
    }

    $sqlAno = "SELECT YEAR(data_criacao) AS ano,
        SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
        SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
        SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
        FROM tickets 
        GROUP BY YEAR(data_criacao)
        ORDER BY ano";

    $resultAno = $conexao->query($sqlAno);

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
    $mesAtual = date('m');

    $sqlMes = "SELECT DATE_FORMAT(data_criacao, '%Y-%m') AS mes,
            SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
            SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
            SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
            FROM tickets 
            WHERE YEAR(data_criacao) = $anoAtual
            GROUP BY YEAR(data_criacao), MONTH(data_criacao)
            ORDER BY mes";

    $resultMes = $conexao->query($sqlMes);

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

    $sqlSemana = "SELECT YEAR(data_criacao) AS ano,
    WEEK(data_criacao) AS semana,
    SUM(CASE WHEN estado = 'aberto' THEN 1 ELSE 0 END) AS abertos,
    SUM(CASE WHEN estado = 'fechado' THEN 1 ELSE 0 END) AS fechados,
    SUM(CASE WHEN estado = 'aberto' AND data_entrega < CURDATE() THEN 1 ELSE 0 END) AS atrasados
    FROM tickets 
    WHERE YEAR(data_criacao) = $anoAtual AND MONTH(data_criacao) = $mesAtual
    GROUP BY YEAR(data_criacao), WEEK(data_criacao)
    ORDER BY ano, semana;";

    $resultSemana = $conexao->query($sqlSemana);

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
        
        <div class="cardBox">
            <div class="card">
                <div>
                    <div class="numbers"><?php echo $total_tickets; ?> </div>
                    <div class="cardName">Chamados</div>
                </div>
                <div class="iconBox">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?php echo $abertos; ?> </div>
                    <div class="cardName">Chamados Abertos</div>
                </div>
                <div class="iconBox">
                <i class="fa-solid fa-exclamation" aria-hidden="true"></i>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?php echo $fechados; ?> </div>
                    <div class="cardName">Chamados fechados</div>
                </div>
                <div class="iconBox">
                <i class="fa-solid fa-box" aria-hidden="true"></i>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?php echo $atrasados; ?> </div>
                    <div class="cardName">Chamados atrasados</div>
                </div>
                <div class="iconBox">
                <i class="fa-solid fa-clock" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="ChartBoxI">
            <?php include('./view/chart1.php'); ?>
            <?php include('./view/chart2.php'); ?>
        </div>

    </main>
    <script src="./src/js/script.js"></script>
</body>
</html>