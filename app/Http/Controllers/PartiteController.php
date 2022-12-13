<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartiteController extends Controller
{
    public function index(Request $request){
        if($request->month == null ){
            $infoPartis = DB::connection('sqlsrv2')->table('TBL_RPARTIDURAS_JUNTA_DUQ')->select('PAR_NID','PAR_D_FECHA_REGISTRO','PAR_CPARTIDURA','PAR_CCANTIDAD')->where('PAR_CESTADO', 1)->where('PAR_DMES', Carbon::now()->format('m'))->where('PAR_DANIO', Carbon::now()->format('Y'))->get();
        }else{
            $infoPartis = DB::connection('sqlsrv2')->table('TBL_RPARTIDURAS_JUNTA_DUQ')->select('PAR_NID','PAR_D_FECHA_REGISTRO','PAR_CPARTIDURA','PAR_CCANTIDAD')->where('PAR_CESTADO', 1)->where('PAR_CMES', $request->month)->where('PAR_DANIO', Carbon::now()->format('Y'))->get();
        }
        return view('listPartites', ['dates' => $infoPartis]);
    }


    public function update(Request $request){
        date_default_timezone_set('America/Bogota');
        DB::connection('sqlsrv2')->table('TBL_RPARTIDURAS_JUNTA_DUQ')->where('PAR_NID',$request->Id)->update(['PAR_CESTADO'=>0,'PAR_D_FECHA_REGISTRO_UPDATE'=>Carbon::now()->format('Y-m-d'),
        'PAR_DHORA_REGISTRO_UPDATE'=>Carbon::now()->toTimeString(),'PAR_CUSUARIO_UPDATE'=>Auth::user()->name]);
        DB::connection('sqlsrv2')->table('TBL_RPARTIDURAS_JUNTA_DUQ')->insert(['PAR_CPARTIDURA'=>$request->nombre,'PAR_CCANTIDAD'=>$request->Cantidad, 'PAR_D_FECHA_REGISTRO'=>Carbon::now()->format('Y-m-d'),'PAR_DHORA_REGISTRO'=>Carbon::now()->toTimeString(),
        'PAR_DANIO'=>Carbon::now()->format('Y'),'PAR_DMES'=>Carbon::now()->format('m'),'PAR_DFECHA'=>Carbon::now()->format('Y').'-'.Carbon::now()->format('m').'-'.'01', 'PAR_CUSUARIO_REGISTRO'=>Auth::user()->name,'PAR_CESTADO'=>1,'PAR_D_FECHA_REGISTRO_UPDATE'=>null,
        'PAR_DHORA_REGISTRO_UPDATE'=>null,'PAR_CUSUARIO_UPDATE'=>null,'PAR_CMES'=>Carbon::now()->format('Y').'-'.Carbon::now()->format('m'),'PAR_CDETALLE2'=>null,'PAR_CDETALLE3'=>null,'PAR_CDETALLE4'=>null]);
        return redirect('admin/listPartidas')->with(['message' => "Informacion actualizada correctamente", 'alert-type' => 'success']);
    }
}
