<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="chart">
        <h3>Chamados por mês</h3>
        <canvas id="myChartMes"></canvas>
    </div>

<script>
    const ctxMes = document.getElementById('myChartMes');

    new Chart(ctxMes, {
        type: 'bar',
        data: {
        labels: <?php echo json_encode($labelsMes);?>,
        datasets: [
            {
                label: 'Abertos',
                data: <?php echo json_encode($abertosMes); ?>,
                borderColor: 'rgb(41, 155, 99)',
                backgroundColor: 'rgba(55, 55, 55, 1)',
                borderWidth: 1
            },
            {
                label: 'Fechado',
                data: <?php echo json_encode($fechadosMes); ?>,
                borderColor: 'rgb(41, 155,99)',
                backgroundColor: 'rgba(155, 255, 155, 1)',
                borderWidth: 1
            },{
                label: 'Concluido',
                data: <?php echo json_encode($concluidosMes); ?>,
                borderColor: 'rgb(41, 155,99)',
                backgroundColor: 'rgba(55, 55, 255, 1)',
                borderWidth: 1
            },
            {
                label: 'Atrasados',
                data: <?php echo json_encode($atrasadosMes); ?>,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(139, 0, 139, 1)',
                borderWidth: 1
            }
        ]
        },
        options: {    
            responsive : true
        }
    });
</script>