<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
       
    <div class="chart" id="doughnut-chart">
        <h3>Chamados por semana</h3>
        <canvas id="myChartSemana"></canvas>
    </div>

<script>
        const ctxSemana = document.getElementById('myChartSemana');

        new Chart(ctxSemana, {
            type: 'bar',
            data: {
            labels: <?php echo json_encode($labelsSemana);?>,
            datasets: [
                {
                    label: 'Abertos',
                    data: <?php echo json_encode($abertosSemana); ?>,
                    borderColor: 'rgb(41, 155, 99)',
                    backgroundColor: 'rgba(55, 55, 55, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Fechado',
                    data: <?php echo json_encode($fechadosSemana); ?>,
                    borderColor: 'rgb(41, 155,99)',
                    backgroundColor: 'rgba(155, 255, 155, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Atrasados',
                    data: <?php echo json_encode($atrasadosSemana); ?>,
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