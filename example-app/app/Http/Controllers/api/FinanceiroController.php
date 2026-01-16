<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceiroController extends Controller
{
    public function dashboard()
    {

        $receitaProcedimentos = DB::table('agendamentos_procedimentos as ap')
            ->join('procedimentos as p', 'p.id', '=', 'ap.procedimento_id')
            ->selectRaw('COALESCE(SUM(p.valor), 0) as total')
            ->value('total');

        /*Receita estimada por DIÁRIAS */
        $receitaDiarias = DB::table('internacoes as i')
            ->join('quartos as q', 'q.id', '=', 'i.quarto_id')
            ->selectRaw("
                COALESCE(
                    SUM(
                        GREATEST(
                            DATE_PART('day', COALESCE(i.data_saida, NOW()) - i.data_entrada),
                            1
                        ) * q.valor_diaria
                    ), 0
                ) as total
            ")
            ->value('total');

        /*  Top 5 Médicos por PROCEDIMENTOS*/
        $topMedicosProcedimentos = DB::table('agendamentos_procedimentos as ap')
            ->join('medicos as m', 'm.id', '=', 'ap.medico_id')
            ->join('funcionarios as f', 'f.id', '=', 'm.funcionario_id')
            ->select(
                'f.nome as medico',
                DB::raw('COUNT(ap.id) as quantidade')
            )
            ->groupBy('f.nome')
            ->orderByDesc('quantidade')
            ->limit(5)
            ->get();

        $faturamentoConvenio = DB::table('internacoes as i')
            ->join('quartos as q', 'q.id', '=', 'i.quarto_id')
            ->join('unidades as u', 'u.id', '=', 'i.quarto_id')
            ->select(
                'u.natureza as convenio',
                DB::raw("
                    COALESCE(
                        SUM(
                            GREATEST(
                                DATE_PART('day', COALESCE(i.data_saida, NOW()) - i.data_entrada),
                                1
                            ) * q.valor_diaria
                        ), 0
                    ) as total
                ")
            )
            ->groupBy('u.natureza')
            ->get();

        return response()->json([
            'receitaProcedimentos'    => round($receitaProcedimentos, 2),
            'receitaDiarias'          => round($receitaDiarias, 2),
            'topMedicosProcedimentos' => $topMedicosProcedimentos,
            'faturamentoConvenio'     => $faturamentoConvenio
        ]);
    }
}
