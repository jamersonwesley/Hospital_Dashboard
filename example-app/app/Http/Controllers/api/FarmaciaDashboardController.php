<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmaciaDashboardController extends Controller
{
    public function index()
    {
     
        $estoqueBaixo = DB::table('medicamentos')
            ->select(
                'id',
                'nome',
                'estoque',
                'estoque_minimo'
            )
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
            ->where('data_validade', '<=', now()->addDays(5)) 
            ->orderBy('data_validade')
            ->get();


        $topMedicamentos = DB::table('medicamentos')
            ->select(
                'nome',
                'estoque',
                'estoque_minimo'
            )
            ->orderBy('estoque_minimo', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'estoqueBaixo' => $estoqueBaixo,
            'proximosVencimento' => $proximosVencimento,
            'curvaConsumo' => $topMedicamentos
        ]);
    }
}
