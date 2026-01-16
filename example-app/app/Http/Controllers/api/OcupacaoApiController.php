<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OcupacaoApiController extends Controller
{
    public function index(Request $request)
    {

        $quartos = DB::table('quartos')
            ->select(
                'tipo',
                DB::raw('SUM(capacidade) as total'),
                DB::raw('SUM(capacidade - CAST(disponivel AS integer)) as ocupados')
            )
            ->groupBy('tipo')
            ->get();

        $totalQuartos = $quartos->sum('total') ?? 0;
        $totalOcupados = $quartos->sum('ocupados') ?? 0;

        $taxaOcupacaoTotal = $totalQuartos > 0
            ? round(($totalOcupados / $totalQuartos) * 100, 2)
            : 0;

    
        $tempoMedioInternacao = DB::table('internacoes as i')
            ->join('medicos as m', 'i.medico_responsavel_id', '=', 'm.id')
            ->join('especialidades as e', 'm.especialidade_id', '=', 'e.id')
            ->select(
                'e.nome as especialidade',
                DB::raw(
                    'AVG(EXTRACT(DAY FROM (COALESCE(i.data_saida, NOW()) - i.data_entrada))) as media_dias'
                )
            )
            ->groupBy('e.nome')
            ->get();

  
        $inicio = '2023-09-01';
        $fim    = '2024-01-31 23:59:59';

        $meses = [
            '2023-09' => 'Set/2023',
            '2023-10' => 'Out/2023',
            '2023-11' => 'Nov/2023',
            '2023-12' => 'Dez/2023',
            '2024-01' => 'Jan/2024',
        ];

        $historico = [];

        foreach ($meses as $mes => $label) {
            $internacoes = DB::table('internacoes')
                ->whereBetween('data_entrada', [
                    "$mes-01",
                    date('Y-m-t', strtotime("$mes-01"))
                ])
                ->count();

            $altas = DB::table('internacoes')
                ->whereNotNull('data_saida')
                ->whereBetween('data_saida', [
                    "$mes-01",
                    date('Y-m-t', strtotime("$mes-01"))
                ])
                ->count();

            $historico[] = [
                'mes' => $label,
                'internacoes' => $internacoes,
                'altas' => $altas,
            ];
        }

   
        $nomePaciente = $request->query('paciente_nome');
        $nomeMedico   = $request->query('medico_nome');

        $historicoPaciente = DB::table('internacoes as i')
            ->join('pacientes as p', 'i.paciente_id', '=', 'p.id')
            ->join('medicos as m', 'i.medico_responsavel_id', '=', 'm.id')
            ->join('funcionarios as f', 'm.funcionario_id', '=', 'f.id')
            ->select(
                'p.nome as paciente',
                'i.data_entrada as data',
                'f.nome as medico',
                'i.diagnostico',
                'i.observacoes',
                'i.status'
            )
            ->whereBetween('i.data_entrada', [$inicio, $fim]);

        if ($nomePaciente) {
            $historicoPaciente->where('p.nome', 'ILIKE', "%{$nomePaciente}%");
        }

        if ($nomeMedico) {
            $historicoPaciente->where('f.nome', 'ILIKE', "%{$nomeMedico}%");
        }

        $historicoPaciente = $historicoPaciente
            ->orderBy('i.data_entrada', 'desc')
            ->get();


        return response()->json([
            'quartos' => $quartos,
            'taxaOcupacaoTotal' => $taxaOcupacaoTotal,
            'tempoMedioInternacao' => $tempoMedioInternacao,
            'historico' => $historico,
            'historicoPaciente' => $historicoPaciente,
        ]);
    }
}
