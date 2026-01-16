<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>MedCentre</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1, h2 { text-align: center; }
        .card { background: #fff; padding: 20px; margin: 20px auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 90%; max-width: 900px; }
        canvas { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #eee; }
        form input { margin: 5px; padding: 5px; width: 200px; }
        form button { padding: 5px 10px; }
    </style>
</head>
<body>

<h1>MedCentre</h1>

@if(isset($error))
<p style="color:red;">Erro: {{ $error }}</p>
@endif

{{-- Gráfico e tabela de Ocupação --}}
<div class="card">
    <h2>Taxa de Ocupação Total: {{ $taxaOcupacaoTotal }}%</h2>
    <canvas id="ocupacaoChart"></canvas>
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Total</th>
                <th>Ocupados</th>
                <th>Disponíveis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quartos as $q)
                <tr>
                    <td>{{ $q->tipo }}</td>
                    <td>{{ $q->total }}</td>
                    <td>{{ $q->ocupados }}</td>
                    <td>{{ $q->total - $q->ocupados }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Gráfico e tabela Tempo Médio de Internação --}}
<div class="card">
    <h2>Tempo Médio de Internação por Especialidade</h2>
    <canvas id="tempoChart"></canvas>
    <table>
        <thead>
            <tr>
                <th>Especialidade</th>
                <th>Tempo Médio (dias)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tempoMedioInternacao as $item)
                <tr>
                    <td>{{ $item->especialidade }}</td>
                    <td>{{ round($item->media_dias,2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Gráfico Histórico Últimos 6 Meses --}}
<div class="card">
    <h2>Histórico de Internações X Altas (Últimos 6 Meses)</h2>
    <canvas id="historicoChart"></canvas>
</div>

{{-- Filtro e histórico paciente/médico --}}
<div class="card">
    <h2>Histórico do Paciente / Médico</h2>

    <form method="GET" action="{{ url('/') }}">
        <label>Nome do paciente:</label>
        <input type="text" name="paciente_nome" value="{{ $nomePaciente ?? '' }}" placeholder="">
        
        <label>Nome do médico:</label>
        <input type="text" name="medico_nome" value="{{ $nomeMedico ?? '' }}" placeholder="">
        
        <button type="submit">Buscar</button>
    </form>

    @if(count($historicoPaciente) === 0)
        <p>Não há histórico com os filtros informados.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Médico</th>
                    <th>Diagnóstico</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historicoPaciente as $item)
                    <tr>
                        <td>{{ $item->paciente }}</td>
                        <td>{{ $item->tipo }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->data)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->medico }}</td>
                        <td>{{ $item->diagnostico }}</td>
                        <td>{{ $item->observacoes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>

    const ctxOcupacao = document.getElementById('ocupacaoChart').getContext('2d');
    new Chart(ctxOcupacao, {
        type: 'bar',
        data: {
            labels: {!! json_encode($quartos->pluck('tipo')) !!},
            datasets: [
                {
                    label: 'Ocupados',
                    data: {!! json_encode($quartos->pluck('ocupados')) !!},
                    backgroundColor: 'rgba(255,99,132,0.7)'
                },
                {
                    label: 'Disponíveis',
                    data: {!! json_encode($quartos->pluck('total')->map(fn($t,$i)=> $t - $quartos[$i]->ocupados)) !!},
                    backgroundColor: 'rgba(75,192,192,0.7)'
                }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });

    const ctxTempo = document.getElementById('tempoChart').getContext('2d');
    new Chart(ctxTempo, {
        type: 'bar',
        data: {
            labels: {!! json_encode($tempoMedioInternacao->pluck('especialidade')) !!},
            datasets: [{
                label: 'Tempo médio (dias)',
                data: {!! json_encode($tempoMedioInternacao->pluck('media_dias')->map(fn($v)=> round($v,2))) !!},
                backgroundColor: 'rgba(54,162,235,0.7)'
            }]
        },
        options: { responsive: true }
    });

    // --- Gráfico Histórico Últimos 6 Meses ---
    const ctxHistorico = document.getElementById('historicoChart').getContext('2d');
    new Chart(ctxHistorico, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($historico, 'mes')) !!},
            datasets: [
                {
                    label: 'Internações',
                    data: {!! json_encode(array_column($historico, 'internacoes')) !!},
                    borderColor: 'rgba(255,99,132,1)',
                    backgroundColor: 'rgba(255,99,132,0.2)',
                    tension: 0.3
                },
                {
                    label: 'Altas',
                    data: {!! json_encode(array_column($historico, 'altas')) !!},
                    borderColor: 'rgba(75,192,192,1)',
                    backgroundColor: 'rgba(75,192,192,0.2)',
                    tension: 0.3
                }
            ]
        },
        options: { responsive: true }
    });
</script>

</body>
</html>
