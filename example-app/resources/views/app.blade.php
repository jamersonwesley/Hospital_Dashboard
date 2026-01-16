<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Ocupação</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Painel de Ocupação do Hospital</h1>

    <p>Total de ocupação: <span id="occupancyRate"></span>%</p>

    <h3>Ocupação por Tipo de Quarto</h3>
    <ul id="occupancyByType"></ul>

    <h3>Tempo Médio de Internação por Especialidade</h3>
    <ul id="avgStay"></ul>

    <h3>Histórico de Internações vs Altas</h3>
    <canvas id="historicalChart" width="600" height="400"></canvas>

    <script>
        async function loadData() {
            const res = await fetch('/api/occupancy');
            const data = await res.json();

            document.getElementById('occupancyRate').innerText = data.occupancyRate;

            const typeList = document.getElementById('occupancyByType');
            data.occupancyByType.forEach(t => {
                const li = document.createElement('li');
                li.innerText = `${t.type}: ${t.occupied}/${t.total} (${t.rate}%)`;
                typeList.appendChild(li);
            });

            const avgList = document.getElementById('avgStay');
            data.avgStay.forEach(s => {
                const li = document.createElement('li');
                li.innerText = `${s.specialty}: ${parseFloat(s.avg_days).toFixed(2)} dias`;
                avgList.appendChild(li);
            });

            // Gráfico
            const ctx = document.getElementById('historicalChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.historical.map(h => h.month),
                    datasets: [
                        {
                            label: 'Internações',
                            data: data.historical.map(h => h.admissions),
                            backgroundColor: 'rgba(54, 162, 235, 0.6)'
                        },
                        {
                            label: 'Altas',
                            data: data.historical.map(h => h.discharges),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        loadData();
    </script>
</body>
</html>
