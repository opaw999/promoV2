<script>
    var ctx = document.getElementById('chart4').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [<?= $stores ?>],
            datasets: [{
                data: [<?= $num ?>],
                backgroundColor: [<?= "'" . implode("', '", array_values($color)) . "'" ?>],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    display: false,
                    labels: {
                        generateLabels: (chart) => chart.data.labels.map((label, index) => ({
                            text: `${label}: ${chart.data.datasets[0].data[index]}`,
                            fillStyle: chart.data.datasets[0].backgroundColor[index],
                            hidden: isNaN(chart.data.datasets[0].data[index]),
                            index: index
                        })),

                    }
                }
            }
        }
    });
</script>