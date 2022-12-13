<?php

use App\Http\Controllers\CostosVentasController;
use App\Http\Controllers\GastosNoOperacionalesController;
use App\Http\Controllers\GastosOperacionalesController;
use App\Http\Controllers\PartiteController;
use App\Http\Controllers\ReporteTotalController;
use App\Http\Controllers\ToneladasController;
use App\Http\Controllers\VentasNetasController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('ventasTotales', [VentasNetasController::class, 'total_sales']);
    Route::post('ventasfilter', [VentasNetasController::class, 'total_sales']);
    Route::get('costosTable', [CostosVentasController::class, 'total_costs']);
    Route::post('costosfilter', [CostosVentasController::class, 'total_costs']);
    Route::get('gastosOperacionales', [GastosOperacionalesController::class, 'operational_expenses']);
    Route::post('gastosfilter', [GastosOperacionalesController::class, 'operational_expenses']);
    Route::get('gastosNoOper', [GastosNoOperacionalesController::class, 'nonOperatinals']);
    Route::post('gastosNofilter', [GastosNoOperacionalesController::class, 'nonOperatinals']);
    Route::get('venToneladas', [ToneladasController::class, 'tons']);
    Route::post('tonsfilter', [ToneladasController::class, 'tons']);
    Route::get('ventasTotUnit', [VentasNetasController::class, 'unit_sales']);
    Route::post('ventasfilterUnitS', [VentasNetasController::class, 'unit_sales']);
    Route::get('costUnit', [CostosVentasController::class, 'unit_sales_costs']);
    Route::post('costUfilter', [CostosVentasController::class, 'unit_sales_costs']);
    Route::get('gastosOperUnit', [GastosOperacionalesController::class, 'unit_operational_expenses']);
    Route::post('gastosUfilter', [GastosOperacionalesController::class, 'unit_operational_expenses']);
    Route::get('gastosNoOperUnit', [GastosNoOperacionalesController::class, 'unit_nonOperatinals']);
    Route::post('gastosNofilterU', [GastosNoOperacionalesController::class, 'unit_nonOperatinals']);
    Route::get('repoTot', [ReporteTotalController::class, 'reporte']);
    Route::post('reportFilter', [ReporteTotalController::class, 'reporte']);
    Route::post('download', [ReporteTotalController::class, 'report_total_sales']);
    Route::get('listPartidas',[PartiteController::class, 'index']);
    Route::post('partiteFilter',[PartiteController::class, 'index']);
    Route::post('editDate',[PartiteController::class, 'update']);
});

