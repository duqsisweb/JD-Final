<?php

namespace App\Http\Controllers;

use App\Http\Traits\CostosTrait;
use App\Http\Traits\CostosUnitTrait;
use App\Http\Traits\GastosNoOperTrait;
use App\Http\Traits\GastosNoOperUnitTrait;
use App\Http\Traits\GastosOperTrait;
use App\Http\Traits\GastosOperUnitTrait;
use App\Http\Traits\VentasNetasTrait;
use App\Http\Traits\VentasNetasUnitTrait;
use App\Http\Traits\VentasToneladasTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReporteTotalController extends Controller
{
    use VentasToneladasTrait;
    use CostosTrait;
    use CostosUnitTrait;
    use VentasNetasTrait;
    use VentasNetasUnitTrait;
    use GastosOperTrait;
    use GastosOperUnitTrait;
    use GastosNoOperTrait;
    use GastosNoOperUnitTrait;

    public function reporte(Request $request)
    {
        $mes = [];
        if ($request->filter1 != null) {
            if ($request->filter1 > $request->filter2) {
                return redirect('admin/gastosU/operUnit')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoMonth = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoMonth = $infoMonth->toArray();
            for ($i = 0; $i < count($infoMonth); $i++) {
                $dateObject = DateTime::createFromFormat('!m', $infoMonth[$i]->INF_D_MES)->format('F');
                array_push($mes, ['mes' => __($dateObject)]);
                if ($i == 2 || $i == 5 || $i == 8 || $i == 11) {
                    array_push($mes, ['mes' => 'TRIMESTRE']);
                }
            }
            array_push($mes, ['mes' => 'ACUMULADO']);
            array_push($mes, ['mes' => 'PROMEDIO']);
            $gastosNoOpUnita = $this->tablaGastosNoOperacionalesUnit($fechaIni, $fechaFin);
            $ventasnetas = $this->TablaVentas($fechaIni, $fechaFin);
            $costos = $this->TablaCostos($fechaIni, $fechaFin);
            $gastosOpera = $this->tablaGastosoperacionales($fechaIni, $fechaFin);
            $gastosNoOpera = $this->tablaGastosNoOperacionales($fechaIni, $fechaFin);
            $toneladas = $this->TablaVentasToneladas($fechaIni, $fechaFin);
            $ventasUnita = $this->TablaVentasUnit($fechaIni, $fechaFin);
            $costosUnita = $this->TablaCostosUnit($fechaIni, $fechaFin);
            $gastosOpUnita = $this->tablaGastosOperacionalesUnit($fechaIni, $fechaFin);
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoMonth = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoMonth = $infoMonth->toArray();
            for ($i = 0; $i < count($infoMonth); $i++) {
                $dateObject = DateTime::createFromFormat('!m', $infoMonth[$i]->INF_D_MES)->format('F');
                array_push($mes, ['mes' => __($dateObject)]);
                if ($i == 2 || $i == 5 || $i == 8 || $i == 11) {
                    array_push($mes, ['mes' => 'TRIMESTRE']);
                }
            }
            array_push($mes, ['mes' => 'ACUMULADO']);
            array_push($mes, ['mes' => 'PROMEDIO']);
            $gastosNoOpUnita = $this->tablaGastosNoOperacionalesUnit($fechaIni, $fechaFin);
            $ventasnetas = $this->TablaVentas($fechaIni, $fechaFin);
            $costos = $this->TablaCostos($fechaIni, $fechaFin);
            $gastosOpera = $this->tablaGastosoperacionales($fechaIni, $fechaFin);
            $gastosNoOpera = $this->tablaGastosNoOperacionales($fechaIni, $fechaFin);
            $toneladas = $this->TablaVentasToneladas($fechaIni, $fechaFin);
            $ventasUnita = $this->TablaVentasUnit($fechaIni, $fechaFin);
            $costosUnita = $this->TablaCostosUnit($fechaIni, $fechaFin);
            $gastosOpUnita = $this->tablaGastosOperacionalesUnit($fechaIni, $fechaFin);
        }


        $headers = [
            'ACEITES', 'MARGARINAS', 'SOLIDOS_CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'OTROS(AGL-ACIDULADO)', 'SERVICIO DE MAQUILA', 'TOTAL OTROS', 'TOTAL VENTAS',
            'ACEITES', 'PORCENTAJE ACEITES', 'MARGARINAS', 'PORCENTAJE MARGARINAS', 'SOLIDOS Y CREMOSOS', 'PORCENTAJE SOLIDOS Y CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'PORCENTAJE TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'PORCENTAJE INDUSTRIALES', 'OTROS PRODUCTOS', 'PORCENTAJE OTROS PRODUCTOS', 'SERVICIO DE MAQUILA', 'PORCENTAJE SERVICIO DE MAQUILA', 'TOTAL OTROS', 'PORCENTAJE TOTAL OTROS', 'TOTAL COSTOS DE VENTAS', 'PORCENTAJE TOTAL COSTOS DE VENTAS', 'UTILIDAD BRUTA', 'PORCENTAJE UTILIDAD BRUTA',
            'GASTOS DE ADMINISTRACION', 'PORCENTAJE GASTOS DE ADMINISTRACION', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'HONORARIOS', 'PORCENTAJE HONORARIOS', 'SERVICIOS', 'PORCENTAJE SERVICIOS', 'OTROS', 'PORCENTAJE OTROS', 'GASTOS DE VENTAS', 'PORCENTAJE GASTOS DE VENTAS', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'POLIZA CARTERA', 'PORCENTAJE POLIZA CARTERA', 'FLETES', 'PORCENTAJE FLETES', 'SERVICIO LOGISTICO', 'PORCENTAJE SERVICIO LOGISTICO', 'ESTRATEGIA COMERCIAL', 'PORCENTAJE ESTRATEGIA COMERCIAL', 'IMPUESTOS', 'PORCENTAJE IMPUESTOS', 'DESCUENTOS PRONTO PAGO', 'PORCENTAJE DESCUENTOS PRONTO PAGO', 'OTROS', 'PORCENTAJE OTROS', 'DEPRECIACIONES Y AMORTIZACIONES', 'PORCENTAJE DEPRECIACIONES Y AMORTIZACIONES', 'TOTAL GASTOS OPERACIONALES', 'PORCENTAJE TOTAL GASTOS OPERACIONALES', 'UTILIDAD OPERACIONAL', 'PORCENTAJE UTILIDAD OPERACIONAL',
            'FINANCIEROS', 'PORCENTAJE FINANCIEROS', 'RETIRO DE ACTIVOS (LEASE BACK - AJUSTE INVENTARIOS)', 'PORCENTAJE RETIRO DE ACTIVOS', 'GRAVAMEN MOVIMIENTO FINANCIERO (4*1000)', 'PORCENTAJE GRAVAMEN MOVIMIENTO', 'OTROS', 'PORCENTAJE OTROS', 'TOTAL NO OPERACIONALES', 'PORCENTAJE TOTAL NO OPERACIONALES', 'UTILIDAD ANTES DE IMPUESTOS', 'PORCENTAJE UTILIDAD ANTES DE IMPUESTOS', 'EBITDA', 'PORCENTAJE EBITDA',
            'VENTAS (TONELADAS)', 'ACEITES TONELADAS', 'MARGARINAS TONELADAS', 'SOLIDOS Y CREMOSOS TONELADAS', 'TOTAL PT', 'INDUSTRIALES (OLEOQUIMICOS)', 'OTROS (AGL-ACIDULADO)', 'SERVICIO MAQUILA',
            'ACEITES', 'MARGARINAS', 'SOLIDOS Y CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'OTROS (AGL-ACIDULADO)', 'SERVICIO DE MAQUILA', 'TOTAL VENTAS',
            'ACEITES', 'PORCENTAJE ACEITES', 'MARGARINAS', 'PORCENTAJE MARGARINAS', 'SOLIDOS Y CREMOSOS', 'PORCENTAJE SOLIDOS Y CREMOSOS', 'INDUSTRIALES', 'PORCENTAJE INDUSTRIALES', 'OTROS PRODUCTOS', 'PORCENTAJE OTROS PRODUCTOS', 'SERVICIO DE MAQUILA', 'PORCENTAJE SERVICIO DE MAQUILA', 'TOTAL COSTOS DE VENTAS', 'PORCENTAJE TOTAL COSTOS DE VENTAS', 'UTILIDAD BRUTA', 'PORCENTAJE UTILIDAD BRUTA',
            'GASTOS DE ADMINISTRACION', 'PORCENTAJE GASTOS DE ADMINISTRACION', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'HONORARIOS', 'PORCENTAJE HONORARIOS', 'SERVICIOS', 'PORCENTAJE SERVICIOS', 'OTROS', 'PORCENTAJE OTROS', 'GASTOS DE VENTAS', 'PORCENTAJE GASTOS DE VENTAS', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'POLIZA CARTERA', 'PORCENTAJE POLIZA CARTERA', 'FLETES', 'PORCENTAJE FLETES', 'SERVICIO LOGISTICO', 'PORCENTAJE SERVICIO LOGISTICO', 'ESTRATEGIA COMERCIAL', 'PORCENTAJE ESTRATEGIA COMERCIAL', 'IMPUESTOS', 'PORCENTAJE IMPUESTOS', 'DESCUENTOS PRONTO PAGO', 'PORCENTAJE DESCUENTOS PRONTO PAGO', 'OTROS', 'PORCENTAJE OTROS', 'DEPRECIACIONES Y AMORTIZACIONES', 'PORCENTAJE DEPRECIACIONES Y AMORTIZACIONES', 'TOTAL GASTOS OPERACIONALES', 'PORCENTAJE TOTAL GASTOS OPERACIONALES', 'UTILIDAD OPERACIONAL', 'PORCENTAJE UTILIDAD OPERACIONAL',
            'FINANCIEROS', 'PORCENTAJE FINANCIEROS', 'RETIRO DE ACTIVOS (LEASE BACK - AJUSTE INVENTARIOS)', 'PORCENTAJE RETIRO DE ACTIVOS', 'GRAVAMEN MOVIMIENTO FINANCIERO (4*1000)', 'PORCENTAJE GRAVAMEN MOVIMIENTO', 'OTROS', 'PORCENTAJE OTROS', 'TOTAL NO OPERACIONALES', 'PORCENTAJE TOTAL NO OPERACIONALES', 'UTILIDAD ANTES DE IMPUESTOS', 'PORCENTAJE UTILIDAD ANTES DE IMPUESTOS', 'EBITDA', 'PORCENTAJE EBITDA',
        ];

        $formates = [];
        for ($c = 0; $c < count($gastosNoOpUnita); $c++) {
            array_push($formates, $ventasnetas[$c]);

            for ($i = 0; $i < count($costos[$c]); $i++) {
                array_push($formates[$c], $costos[$c][$i]);
            }
            for ($i = 0; $i < count($gastosOpera[$c]); $i++) {
                array_push($formates[$c], $gastosOpera[$c][$i]);
            }
            for ($i = 0; $i < count($gastosNoOpera[$c]); $i++) {
                array_push($formates[$c], $gastosNoOpera[$c][$i]);
            }
            for ($i = 0; $i < count($toneladas[$c]); $i++) {
                array_push($formates[$c], $toneladas[$c][$i]);
            }
            for ($i = 0; $i < count($ventasUnita[$c]); $i++) {
                array_push($formates[$c], $ventasUnita[$c][$i]);
            }
            for ($i = 0; $i < count($costosUnita[0]); $i++) {
                array_push($formates[$c], $costosUnita[$c][$i]);
            }
            for ($i = 0; $i < count($gastosOpUnita[$c]); $i++) {
                array_push($formates[$c], $gastosOpUnita[$c][$i]);
            }
            for ($i = 0; $i < count($gastosNoOpUnita[$c]); $i++) {
                array_push($formates[$c], $gastosNoOpUnita[$c][$i]);
            }
        }

        return view('reporteGeneral', ['data' => $formates, 'headers' => $headers, 'mes' => $mes]);
    }


    public function report_total_sales(Request $request){
        $mes = [];
        array_push($mes,  'Concepto');
        if ($request->filter1 != null) {
            if ($request->filter1 > $request->filter2) {
                return redirect('admin/gastosU/operUnit')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoMonth = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoMonth = $infoMonth->toArray();
            for ($i = 0; $i < count($infoMonth); $i++) {
                $dateObject = DateTime::createFromFormat('!m', $infoMonth[$i]->INF_D_MES)->format('F');
                
                array_push($mes,  __($dateObject));
                if ($i == 2 || $i == 5 || $i == 8 || $i == 11) {
                    array_push($mes, 'TRIMESTRE');
                }
            }
            array_push($mes, 'ACUMULADO');
            array_push($mes, 'PROMEDIO');
            $gastosNoOpUnita = $this->tablaGastosNoOperacionalesUnit($fechaIni, $fechaFin);
            $ventasnetas = $this->TablaVentas($fechaIni, $fechaFin);
            $costos = $this->TablaCostos($fechaIni, $fechaFin);
            $gastosOpera = $this->tablaGastosoperacionales($fechaIni, $fechaFin);
            $gastosNoOpera = $this->tablaGastosNoOperacionales($fechaIni, $fechaFin);
            $toneladas = $this->TablaVentasToneladas($fechaIni, $fechaFin);
            $ventasUnita = $this->TablaVentasUnit($fechaIni, $fechaFin);
            $costosUnita = $this->TablaCostosUnit($fechaIni, $fechaFin);
            $gastosOpUnita = $this->tablaGastosOperacionalesUnit($fechaIni, $fechaFin);
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoMonth = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoMonth = $infoMonth->toArray();
            for ($i = 0; $i < count($infoMonth); $i++) {
                $dateObject = DateTime::createFromFormat('!m', $infoMonth[$i]->INF_D_MES)->format('F');
                array_push($mes,  __($dateObject));
                if ($i == 2 || $i == 5 || $i == 8 || $i == 11) {
                    array_push($mes, 'TRIMESTRE');
                }
            }
            array_push($mes, 'ACUMULADO');
            array_push($mes, 'PROMEDIO');
            $gastosNoOpUnita = $this->tablaGastosNoOperacionalesUnit($fechaIni, $fechaFin);
            $ventasnetas = $this->TablaVentas($fechaIni, $fechaFin);
            $costos = $this->TablaCostos($fechaIni, $fechaFin);
            $gastosOpera = $this->tablaGastosoperacionales($fechaIni, $fechaFin);
            $gastosNoOpera = $this->tablaGastosNoOperacionales($fechaIni, $fechaFin);
            $toneladas = $this->TablaVentasToneladas($fechaIni, $fechaFin);
            $ventasUnita = $this->TablaVentasUnit($fechaIni, $fechaFin);
            $costosUnita = $this->TablaCostosUnit($fechaIni, $fechaFin);
            $gastosOpUnita = $this->tablaGastosOperacionalesUnit($fechaIni, $fechaFin);
        }

        $formates = [];
        
        for ($c = 0; $c < count($gastosNoOpUnita); $c++) {
            array_push($formates, $ventasnetas[$c]);

            for ($i = 0; $i < count($costos[$c]); $i++) {
                array_push($formates[$c], $costos[$c][$i]);
            }
            for ($i = 0; $i < count($gastosOpera[$c]); $i++) {
                array_push($formates[$c], $gastosOpera[$c][$i]);
            }
            for ($i = 0; $i < count($gastosNoOpera[$c]); $i++) {
                array_push($formates[$c], $gastosNoOpera[$c][$i]);
            }
            for ($i = 0; $i < count($toneladas[$c]); $i++) {
                array_push($formates[$c], $toneladas[$c][$i]);
            }
            for ($i = 0; $i < count($ventasUnita[$c]); $i++) {
                array_push($formates[$c], $ventasUnita[$c][$i]);
            }
            for ($i = 0; $i < count($costosUnita[0]); $i++) {
                array_push($formates[$c], $costosUnita[$c][$i]);
            }
            for ($i = 0; $i < count($gastosOpUnita[$c]); $i++) {
                array_push($formates[$c], $gastosOpUnita[$c][$i]);
            }
            for ($i = 0; $i < count($gastosNoOpUnita[$c]); $i++) {
                array_push($formates[$c], $gastosNoOpUnita[$c][$i]);
            }
        }
        array_unshift($formates,[
            'ACEITES', 'MARGARINAS', 'SOLIDOS_CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'OTROS(AGL-ACIDULADO)', 'SERVICIO DE MAQUILA', 'TOTAL OTROS', 'TOTAL VENTAS',
            'ACEITES', 'PORCENTAJE ACEITES', 'MARGARINAS', 'PORCENTAJE MARGARINAS', 'SOLIDOS Y CREMOSOS', 'PORCENTAJE SOLIDOS Y CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'PORCENTAJE TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'PORCENTAJE INDUSTRIALES', 'OTROS PRODUCTOS', 'PORCENTAJE OTROS PRODUCTOS', 'SERVICIO DE MAQUILA', 'PORCENTAJE SERVICIO DE MAQUILA', 'TOTAL OTROS', 'PORCENTAJE TOTAL OTROS', 'TOTAL COSTOS DE VENTAS', 'PORCENTAJE TOTAL COSTOS DE VENTAS', 'UTILIDAD BRUTA', 'PORCENTAJE UTILIDAD BRUTA',
            'GASTOS DE ADMINISTRACION', 'PORCENTAJE GASTOS DE ADMINISTRACION', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'HONORARIOS', 'PORCENTAJE HONORARIOS', 'SERVICIOS', 'PORCENTAJE SERVICIOS', 'OTROS', 'PORCENTAJE OTROS', 'GASTOS DE VENTAS', 'PORCENTAJE GASTOS DE VENTAS', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'POLIZA CARTERA', 'PORCENTAJE POLIZA CARTERA', 'FLETES', 'PORCENTAJE FLETES', 'SERVICIO LOGISTICO', 'PORCENTAJE SERVICIO LOGISTICO', 'ESTRATEGIA COMERCIAL', 'PORCENTAJE ESTRATEGIA COMERCIAL', 'IMPUESTOS', 'PORCENTAJE IMPUESTOS', 'DESCUENTOS PRONTO PAGO', 'PORCENTAJE DESCUENTOS PRONTO PAGO', 'OTROS', 'PORCENTAJE OTROS', 'DEPRECIACIONES Y AMORTIZACIONES', 'PORCENTAJE DEPRECIACIONES Y AMORTIZACIONES', 'TOTAL GASTOS OPERACIONALES', 'PORCENTAJE TOTAL GASTOS OPERACIONALES', 'UTILIDAD OPERACIONAL', 'PORCENTAJE UTILIDAD OPERACIONAL',
            'FINANCIEROS', 'PORCENTAJE FINANCIEROS', 'RETIRO DE ACTIVOS (LEASE BACK - AJUSTE INVENTARIOS)', 'PORCENTAJE RETIRO DE ACTIVOS', 'GRAVAMEN MOVIMIENTO FINANCIERO (4*1000)', 'PORCENTAJE GRAVAMEN MOVIMIENTO', 'OTROS', 'PORCENTAJE OTROS', 'TOTAL NO OPERACIONALES', 'PORCENTAJE TOTAL NO OPERACIONALES', 'UTILIDAD ANTES DE IMPUESTOS', 'PORCENTAJE UTILIDAD ANTES DE IMPUESTOS', 'EBITDA', 'PORCENTAJE EBITDA',
            'VENTAS (TONELADAS)', 'ACEITES TONELADAS', 'MARGARINAS TONELADAS', 'SOLIDOS Y CREMOSOS TONELADAS', 'TOTAL PT', 'INDUSTRIALES (OLEOQUIMICOS)', 'OTROS (AGL-ACIDULADO)', 'SERVICIO MAQUILA',
            'ACEITES', 'MARGARINAS', 'SOLIDOS Y CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'OTROS (AGL-ACIDULADO)', 'SERVICIO DE MAQUILA', 'TOTAL VENTAS',
            'ACEITES', 'PORCENTAJE ACEITES', 'MARGARINAS', 'PORCENTAJE MARGARINAS', 'SOLIDOS Y CREMOSOS', 'PORCENTAJE SOLIDOS Y CREMOSOS', 'INDUSTRIALES', 'PORCENTAJE INDUSTRIALES', 'OTROS PRODUCTOS', 'PORCENTAJE OTROS PRODUCTOS', 'SERVICIO DE MAQUILA', 'PORCENTAJE SERVICIO DE MAQUILA', 'TOTAL COSTOS DE VENTAS', 'PORCENTAJE TOTAL COSTOS DE VENTAS', 'UTILIDAD BRUTA', 'PORCENTAJE UTILIDAD BRUTA',
            'GASTOS DE ADMINISTRACION', 'PORCENTAJE GASTOS DE ADMINISTRACION', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'HONORARIOS', 'PORCENTAJE HONORARIOS', 'SERVICIOS', 'PORCENTAJE SERVICIOS', 'OTROS', 'PORCENTAJE OTROS', 'GASTOS DE VENTAS', 'PORCENTAJE GASTOS DE VENTAS', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'POLIZA CARTERA', 'PORCENTAJE POLIZA CARTERA', 'FLETES', 'PORCENTAJE FLETES', 'SERVICIO LOGISTICO', 'PORCENTAJE SERVICIO LOGISTICO', 'ESTRATEGIA COMERCIAL', 'PORCENTAJE ESTRATEGIA COMERCIAL', 'IMPUESTOS', 'PORCENTAJE IMPUESTOS', 'DESCUENTOS PRONTO PAGO', 'PORCENTAJE DESCUENTOS PRONTO PAGO', 'OTROS', 'PORCENTAJE OTROS', 'DEPRECIACIONES Y AMORTIZACIONES', 'PORCENTAJE DEPRECIACIONES Y AMORTIZACIONES', 'TOTAL GASTOS OPERACIONALES', 'PORCENTAJE TOTAL GASTOS OPERACIONALES', 'UTILIDAD OPERACIONAL', 'PORCENTAJE UTILIDAD OPERACIONAL',
            'FINANCIEROS', 'PORCENTAJE FINANCIEROS', 'RETIRO DE ACTIVOS (LEASE BACK - AJUSTE INVENTARIOS)', 'PORCENTAJE RETIRO DE ACTIVOS', 'GRAVAMEN MOVIMIENTO FINANCIERO (4*1000)', 'PORCENTAJE GRAVAMEN MOVIMIENTO', 'OTROS', 'PORCENTAJE OTROS', 'TOTAL NO OPERACIONALES', 'PORCENTAJE TOTAL NO OPERACIONALES', 'UTILIDAD ANTES DE IMPUESTOS', 'PORCENTAJE UTILIDAD ANTES DE IMPUESTOS', 'EBITDA', 'PORCENTAJE EBITDA',
        ]);
        
           $formates2=[];
           $a=0;
           foreach($formates as $form){
               for($i=0;$i<count($formates[$a]);$i++){
                if(isset($formates2[$a][0])== false){
                    array_push($formates2, [str_replace("%", "", $formates[$a][$i])]);
                }else{
                    array_push($formates2[$a], str_replace("%", "", $formates[$a][$i]));
                }
                }
                $a++;
           }
           
        
        $arr=[];
        $matrices=[];
        for($i=0;$i<count($formates2[0]);$i++){
            array_push($matrices, $arr);
        }
        
        for($i=0;$i<count($formates2[0]);$i++){
            for($c=0;$c<count($formates2);$c++){
                array_push($matrices[$i],$formates2[$c][$i]);
            }
       }
   
       return Excel::download(new ReportExport($matrices,$mes), 'Informe-JD.xlsx');
    }
}
