<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FarmaciaDashboardController extends Controller
{
    public function index()
    {

        $estoqueBaixo = DB::table('medicamentos')
            ->select('id', 'nome', 'estoque', 'estoque_minimo')
            ->whereColumn('estoque', '<', 'estoque_minimo')
            ->orderBy('estoque', 'asc')
            ->get();


        $proximosVencimento = DB::table('medicamentos')
            ->select(
                'id',
                'nome',
                'data_validade',
                DB::raw("DATE_PART('day', data_validade - NOW()) as dias_restantes")
            )
            ->where('data_validade', '<=', now()->addDays(30))
            ->orderBy('data_validade')
            ->get();


        $curvaConsumo = DB::table('medicamentos as m')
            ->select(
                'm.id',
                'm.nome',
                DB::raw("COALESCE(SUM(pi.quantidade),0) as quantidade_prescrita"),
                DB::raw("COALESCE(SUM(ci.quantidade),0) as quantidade_comprada")
            )
            ->leftJoin('prescricao_itens as pi', function($join) {
                $join->on('pi.medicamento_id', '=', 'm.id')
                     ->leftJoin('prescricoes as p', 'pi.prescricao_id', '=', 'p.id')
                     ->whereBetween('p.data_prescricao', ['2023-11-01', '2025-01-31']);
            })
            ->leftJoin('compra_itens as ci', 'ci.medicamento_id', '=', 'm.id')
            ->leftJoin('compras_medicamentos as cm', function($join) {
                $join->on('ci.compra_id', '=', 'cm.id')
                     ->whereBetween('cm.data_compra', ['2023-11-01', '2025-01-31']);
            })
            ->groupBy('m.id', 'm.nome')
            ->orderByDesc(DB::raw("COALESCE(SUM(pi.quantidade),0) + COALESCE(SUM(ci.quantidade),0)"))
            ->limit(5)
            ->get();

        return response()->json([
            'estoqueBaixo' => $estoqueBaixo,
            'proximosVencimento' => $proximosVencimento,
            'curvaConsumo' => $curvaConsumo
        ]);
    }
}
