<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <div class="chart">
            <h3>Chamados por ano</h3>
            <canvas id="myChartAno"></canvas>
        </div>
     

<script>
const ctxAno = document.getElementById('myChartAno');

    new Chart(ctxAno, {
        type: 'line',
        data: {
        labels: <?php echo json_encode($labelsAno);?>,
        datasets: [
            {
                label: 'Abertos',
                data: <?php echo json_encode($abertosAno); ?>,
                borderColor: 'rgb(41, 155, 99)',
                backgroundColor: 'rgba(55, 55, 55, 1)',
                borderWidth: 1
            },
            {
                label: 'Fechado',
                data: <?php echo json_encode($fechadosAno); ?>,
                borderColor: 'rgb(41, 155,99)',
                backgroundColor: 'rgba(155, 255, 155, 1)',
                borderWidth: 1
            },
            {
                label: 'Atrasados',
                data: <?php echo json_encode($atrasadosAno); ?>,
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