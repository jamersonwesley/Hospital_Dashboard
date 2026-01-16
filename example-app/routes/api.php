<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OcupacaoApiController;
use App\Http\Controllers\Api\FinanceiroController;
use App\Http\Controllers\Api\FarmaciaDashboardController;

Route::get('/ocupacao', [OcupacaoApiController::class, 'index']);
Route::get('/financeiro', [FinanceiroController::class, 'dashboard']);
Route::get('/farmacia', [FarmaciaDashboardController::class, 'index']);
