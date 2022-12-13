<?php

namespace App\Http\Controllers;

use App\Http\Traits\GastosNoOperTrait;
use App\Http\Traits\GastosNoOperTraitC;
use App\Http\Traits\GastosOperUnitTrait;
use App\Http\Traits\GastosOperUnitTraitC;
use App\Http\Traits\VentasNetasTrait;
use App\Http\Traits\VentasNetasUnitTraitC;
use App\Http\Traits\VentasToneladasTrait;
use App\Http\Traits\VentasToneladasTraitC;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastosNoOperacionalesController extends Controller
{
    use GastosNoOperTrait;
    use VentasToneladasTrait;
    use GastosOperUnitTrait;
    use VentasNetasUnitTraitC;
    use VentasNetasTrait;
    use GastosNoOperTraitC;
    use GastosOperUnitTraitC;
    use VentasToneladasTraitC;
    public function nonOperatinals(Request $request)
    {
        
        if ($request->filter1 != null) {
            if($request->filter1 > $request->filter2){
                return redirect('admin/gastosNo/NoOper')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoNoOpe = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoNoOpe = $infoNoOpe->toArray();
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoNoOpe = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoNoOpe = $infoNoOpe->toArray();
        }

        $headers = [
            'FINANCIEROS', 'PORCENTAJE FINANCIEROS', 'RETIRO DE ACTIVOS (LEASE BACK - AJUSTE INVENTARIOS)',
            'PORCENTAJE RETIRO DE ACTIVOS', 'GRAVAMEN MOVIMIENTO FINANCIERO (4*1000)', 'PORCENTAJE GRAVAMEN MOVIMIENTO', 'OTROS',
            'PORCENTAJE OTROS', 'TOTAL NO OPERACIONALES', 'PORCENTAJE TOTAL NO OPERACIONALES', 'UTILIDAD ANTES DE IMPUESTOS',
            'PORCENTAJE UTILIDAD ANTES DE IMPUESTOS', 'EBITDA', 'PORCENTAJE EBITDA'
        ];
        $fomDates = [];
        $mes = [];
        $c = 1;
        foreach ($infoNoOpe as $data) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
                $c71= intval($data->DEPRECIACIONES_AMORTIZACIONES);
                $TOTALV = $data->ACEITES+$data->MARGARINAS+$data->SOLIDOS_CREMOSOS+$data->INDUSTRIALES+$data->ACIDOS_GRASOS_ACIDULADO+$data->SERVICIO_MAQUILA;
                $infoTOTP = intval(round($data->SOLIDOS_CREMOSOS2 + $data->MARGARINAS2 + $data->ACEITES2));
                $TOTSUMOTR = round($data->ACEITES2+$data->MARGARINAS2+$data->SOLIDOS_CREMOSOS2+$data->INDUSTRIALES2+$data->ACIDOS_GRASOS_ACIDULADO2+$data->SERVICIO_MAQUILA2);
                $totGasOper = $data->DEPRECIACIONES_AMORTIZACIONES+$data->GASTOS_VENTAS+$data->GASTOS_ADMINISTRACION;
                $UTLBRUTA = +$TOTALV - $TOTSUMOTR;
                $UtilOper = $UTLBRUTA - $totGasOper;
                $finan = intval(round($data->FINANCIEROS));
                $porceFinan = round($finan * 100 / $TOTALV, 2) . '%';
                $retActiv = intval(round($data->RETIRO_ACTIVOS));
                $porceActiv = round($retActiv * 100 / $TOTALV, 2) . '%';
                $gravFinan = intval(round($data->GRAVA_MOV_FINANCIERO));
                $porceGravFin = round($gravFinan * 100 / $TOTALV, 2) . '%';
                $otros = intval(round($data->OTROS));
                $porceOtros = round($otros * 100 / $TOTALV, 2) . '%';
                $totlNoOp = $finan + $retActiv + $gravFinan + $otros;
                $porceTotlNoOp = round($totlNoOp * 100 / $TOTALV, 2) . '%';
                $utilAntImp =   $UtilOper - $totlNoOp;
                $porceUtilAntImp = round($utilAntImp * 100 / $TOTALV, 2) . '%';
                $ebitda = intval($c71+$totlNoOp+$utilAntImp+$data->EBITDA);
                $porceEbtida = round($ebitda * 100 / $TOTALV, 2) . '%';
                $dateObject = DateTime::createFromFormat('!m', $data->INF_D_MES)->format('F');
                array_push($fomDates, [
                    $finan, $porceFinan, $retActiv, $porceActiv, $gravFinan, $porceGravFin, $otros, $porceOtros,
                    $totlNoOp, $porceTotlNoOp, $utilAntImp, $porceUtilAntImp, $ebitda, $porceEbtida
                ]);
                array_push($mes, ['mes' => __($dateObject)]);
                array_push($mes, ['mes' => 'TRIMESTRE']);
                switch ($c) {
                    case $c <= 3:
                        $ventasNetasTabla= $this->TablaVentas($fechaIni,$fechaFin);
                        $ventasNetasTabla= array_slice($ventasNetasTabla,0,3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }
        
                        $formEdit= $fomDates;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit as $prom) {
                                if($i%2==0){
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta= count($sumaCostos);
                        for($i=0;$i<$cuenta;$i++){
                            if($i%2==0){
                            }else{
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals=[];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i]*100/$sumaVentas[8],2).'%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                    case $c >3 && $c < 8:
                        $formEdit1 = array_slice($fomDates,4,3);
                        $ventasNetasTabla= $this->TablaVentas($fechaIni,$fechaFin);
                        $ventasNetasTabla= array_slice($ventasNetasTabla,3,3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }
        
                        $formEdit= $fomDates;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit1[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit1 as $prom) {
                                if($i%2==0){
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta= count($sumaCostos);
                        for($i=0;$i<$cuenta;$i++){
                            if($i%2==0){
                            }else{
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals=[];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i]*100/$sumaVentas[8],2).'%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                    case $c > 7 && $c <= 11:
                        $formEdit2 = array_slice($fomDates,8,3);
                        $ventasNetasTabla= $this->TablaVentas($fechaIni,$fechaFin);
                        $ventasNetasTabla= array_slice($ventasNetasTabla,6,3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }
        
                        $formEdit= $fomDates;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit2[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit2 as $prom) {
                                if($i%2==0){
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta= count($sumaCostos);
                        for($i=0;$i<$cuenta;$i++){
                            if($i%2==0){
                            }else{
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals=[];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i]*100/$sumaVentas[8],2).'%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                    case $c > 11 :
                        $formEdit2 = array_slice($fomDates,12,3);
                        $ventasNetasTabla= $this->TablaVentas($fechaIni,$fechaFin);
                        $ventasNetasTabla= array_slice($ventasNetasTabla,9,3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }
        
                        $formEdit= $fomDates;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit2[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit2 as $prom) {
                                if($i%2==0){
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta= count($sumaCostos);
                        for($i=0;$i<$cuenta;$i++){
                            if($i%2==0){
                            }else{
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals=[];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i]*100/$sumaVentas[8],2).'%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                }
                $c++;
            } else {
                $c71= intval($data->DEPRECIACIONES_AMORTIZACIONES);
                $TOTALV = $data->ACEITES+$data->MARGARINAS+$data->SOLIDOS_CREMOSOS+$data->INDUSTRIALES+$data->ACIDOS_GRASOS_ACIDULADO+$data->SERVICIO_MAQUILA;
                $infoTOTP = intval(round($data->SOLIDOS_CREMOSOS2 + $data->MARGARINAS2 + $data->ACEITES2));
                $TOTSUMOTR = round($data->ACEITES2+$data->MARGARINAS2+$data->SOLIDOS_CREMOSOS2+$data->INDUSTRIALES2+$data->ACIDOS_GRASOS_ACIDULADO2+$data->SERVICIO_MAQUILA2);
                $totGasOper = $data->DEPRECIACIONES_AMORTIZACIONES+$data->GASTOS_VENTAS+$data->GASTOS_ADMINISTRACION;
                $UTLBRUTA = +$TOTALV - $TOTSUMOTR;
                $UtilOper = $UTLBRUTA - $totGasOper;
                $finan = intval(round($data->FINANCIEROS));
                $porceFinan = round($finan * 100 / $TOTALV, 2) . '%';
                $retActiv = intval(round($data->RETIRO_ACTIVOS));
                $porceActiv = round($retActiv * 100 / $TOTALV, 2) . '%';
                $gravFinan = intval(round($data->GRAVA_MOV_FINANCIERO));
                $porceGravFin = round($gravFinan * 100 / $TOTALV, 2) . '%';
                $otros = intval(round($data->OTROS));
                $porceOtros = round($otros * 100 / $TOTALV, 2) . '%';
                $totlNoOp = $finan + $retActiv + $gravFinan + $otros;
                $porceTotlNoOp = round($totlNoOp * 100 / $TOTALV, 2) . '%';
                $utilAntImp =   $UtilOper - $totlNoOp;
                $porceUtilAntImp = round($utilAntImp * 100 / $TOTALV, 2) . '%';
                $ebitda = intval($c71+$totlNoOp+$utilAntImp+$data->EBITDA);
                $porceEbtida = round($ebitda * 100 / $TOTALV, 2) . '%';
                $dateObject = DateTime::createFromFormat('!m', $data->INF_D_MES)->format('F');
                array_push($fomDates, [
                    $finan, $porceFinan, $retActiv, $porceActiv, $gravFinan, $porceGravFin, $otros, $porceOtros,
                    $totlNoOp, $porceTotlNoOp, $utilAntImp, $porceUtilAntImp, $ebitda, $porceEbtida
                ]);
                array_push($mes, ['mes' => __($dateObject)]);
                $c++;
            }
        }
        array_push($mes, ['mes' => 'ACUMULADO']);
        array_push($mes, ['mes' => 'PROMEDIO']);


        $sumados = [];
        $ventTotales = [];
        foreach ($infoNoOpe as $infoOperations) {
            $infoACEITES =  round($infoOperations->ACEITES, 5);
            $infoMARGARINAS =  round($infoOperations->MARGARINAS, 5);
            $infoSOLIDOS_CREMOSOS =  round($infoOperations->SOLIDOS_CREMOSOS, 5);
            $infoINDUSTRIALES =  round($infoOperations->INDUSTRIALES, 5);
            $infoOTROS =  round($infoOperations->ACIDOS_GRASOS_ACIDULADO, 5);
            $infoSERVICIO_MAQUILA =  round($infoOperations->SERVICIO_MAQUILA, 5);
            $TOTALP = intval($infoACEITES + $infoMARGARINAS + $infoSOLIDOS_CREMOSOS);
            $TOTALO = intval($infoINDUSTRIALES + $infoOTROS + $infoSERVICIO_MAQUILA);
            $TOTALV = intval($TOTALP + $TOTALO);
            $infoTOTP = intval(round($infoOperations->SOLIDOS_CREMOSOS2 + $infoOperations->MARGARINAS2 + $infoOperations->ACEITES2));
            $depreAmorti = round($infoOperations->DEPRECIACIONES_AMORTIZACIONES);
            $gasVentas = round($infoOperations->GASTOS_VENTAS, 2);
            $gastAdmin = round($infoOperations->GASTOS_ADMINISTRACION, 5);
            $TOTSUMOTR = $TOTALO + $infoTOTP;
            $totGasOper = +$gastAdmin + $gasVentas + $depreAmorti;
            $UTLBRUTA = +$TOTALV - $TOTSUMOTR;
            $UtilOper = $UTLBRUTA - $totGasOper;
            $finanN = round($infoOperations->FINANCIEROS, 5);
            $retActivN = round($infoOperations->RETIRO_ACTIVOS, 5);
            $gravFinanN = round($infoOperations->GRAVA_MOV_FINANCIERO, 5);
            $otrosN = round($infoOperations->OTROS, 5);
            $totlNoOpN = $finanN + $retActivN + $gravFinanN + $otrosN;
            $utilAntImpN =   $UtilOper - $totlNoOpN;
            $ebitdaN = round($infoOperations->EBITDA, 5);
            array_push($sumados, [$finanN, $retActivN, $gravFinanN, $otrosN, $totlNoOpN, $utilAntImpN, $ebitdaN]);
            array_push($ventTotales, $TOTALV);
        }

        $sumatorias = [];
        $promedios = [];
        for ($i = 0; $i < count($sumados[0]); $i++) {
            $suma = 0;
            foreach ($sumados as $sum) {
                $suma += $sum[$i];
            }
            array_push($sumatorias, intval(round($suma)));
            array_push($promedios, intval(round($suma / count($infoNoOpe))));
        }

        for ($i = 0; $i < count($infoNoOpe); $i++) {
            $suma = 0;
            foreach ($ventTotales as $tot) {
                $suma += $tot;
            }
        }
        $sumtot = $suma;

        $acumulados = [];
        for ($i = 0; $i < count($sumados[0]); $i++) {
            $sumNo = $sumatorias[$i];
            $porceNo = $sumNo / $sumtot;
            array_push($acumulados, $sumNo);
            array_push($acumulados, round($porceNo, 2) . '%');
        }

        array_push($fomDates, $acumulados);


        $promediosNo = [];
        for ($i = 0; $i < count($sumados[0]); $i++) {
            $promNo = $promedios[$i];
            $porceNo = $promNo / ($sumtot / count($infoNoOpe));
            array_push($promediosNo, $promNo);
            array_push($promediosNo, round($porceNo, 2) . '%');
        }
        array_push($fomDates, $promediosNo);



        $form = 0;
        foreach ($fomDates as $form) {
            $form = count($form);
        }
        return view('NonOperatingExpenses\list_non_operating_expenses', ['headers' => $headers, 'dates' => $fomDates, 'mes' => $mes, 'contador' => $form]);
    }

    public function unit_nonOperatinals(Request $request)
    {
        if ($request->filter1 != null) {
            if($request->filter1 > $request->filter2){
                return redirect('admin/gastos/NoOperUnit')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoNoOpe = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoNoOpe = $infoNoOpe->toArray();
            $infoTons = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTons = $infoTons->toArray();
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoNoOpe = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoNoOpe = $infoNoOpe->toArray();
            $infoTons = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTons = $infoTons->toArray();
        }

        $headers = [
            'FINANCIEROS', 'PORCENTAJE FINANCIEROS', 'RETIRO DE ACTIVOS (LEASE BACK - AJUSTE INVENTARIOS)',
            'PORCENTAJE RETIRO DE ACTIVOS', 'GRAVAMEN MOVIMIENTO FINANCIERO (4*1000)', 'PORCENTAJE GRAVAMEN MOVIMIENTO', 'OTROS',
            'PORCENTAJE OTROS', 'TOTAL NO OPERACIONALES', 'PORCENTAJE TOTAL NO OPERACIONALES', 'UTILIDAD ANTES DE IMPUESTOS',
            'PORCENTAJE UTILIDAD ANTES DE IMPUESTOS', 'EBITDA', 'PORCENTAJE EBITDA'
        ];
        $data1 = [];
        $mes = [];
        foreach ($infoNoOpe as $info) {
            $c71= intval($info->DEPRECIACIONES_AMORTIZACIONES);
                $TOTALV = $info->ACEITES+$info->MARGARINAS+$info->SOLIDOS_CREMOSOS+$info->INDUSTRIALES+$info->ACIDOS_GRASOS_ACIDULADO+$info->SERVICIO_MAQUILA;
                $infoTOTP = intval(round($info->SOLIDOS_CREMOSOS2 + $info->MARGARINAS2 + $info->ACEITES2));
                $TOTSUMOTR = round($info->ACEITES2+$info->MARGARINAS2+$info->SOLIDOS_CREMOSOS2+$info->INDUSTRIALES2+$info->ACIDOS_GRASOS_ACIDULADO2+$info->SERVICIO_MAQUILA2);
                $totGasOper = $info->DEPRECIACIONES_AMORTIZACIONES+$info->GASTOS_VENTAS+$info->GASTOS_ADMINISTRACION;
                $UTLBRUTA = +$TOTALV - $TOTSUMOTR;
                $UtilOper = $UTLBRUTA - $totGasOper;
                $finan = intval(round($info->FINANCIEROS));
                $porceFinan = round($finan * 100 / $TOTALV, 2) . '%';
                $retActiv = intval(round($info->RETIRO_ACTIVOS));
                $porceActiv = round($retActiv * 100 / $TOTALV, 2) . '%';
                $gravFinan = intval(round($info->GRAVA_MOV_FINANCIERO));
                $porceGravFin = round($gravFinan * 100 / $TOTALV, 2) . '%';
                $otros = intval(round($info->OTROS));
                $porceOtros = round($otros * 100 / $TOTALV, 2) . '%';
                $totlNoOp = $finan + $retActiv + $gravFinan + $otros;
                $porceTotlNoOp = round($totlNoOp * 100 / $TOTALV, 2) . '%';
                $utilAntImp =   $UtilOper - $totlNoOp;
                $porceUtilAntImp = round($utilAntImp * 100 / $TOTALV, 2) . '%';
            $financiero = $info->FINANCIEROS;
            $totVEN1 = (round($info->ACEITES) + round($info->MARGARINAS) + round($info->SOLIDOS_CREMOSOS) + round($info->INDUSTRIALES) + round($info->ACIDOS_GRASOS_ACIDULADO) + round($info->SERVICIO_MAQUILA) - round($info->SERVICIO_MAQUILA));
            $retActiv1 = $info->RETIRO_ACTIVOS;
            $gravMov1 = $info->GRAVA_MOV_FINANCIERO;
            $otros1 = $info->OTROS;
            $gasAdmon1 = $info->GASTOS_ADMINISTRACION;
            $gasVentas1 = $info->GASTOS_VENTAS;
            $depresiaci1 = $info->DEPRECIACIONES_AMORTIZACIONES;
            $totGasOper1 = $gasAdmon1 + $gasVentas1 + $depresiaci1;
            $infoTOT1 = round($info->SOLIDOS_CREMOSOS2 + $info->MARGARINAS2 + $info->ACEITES2) + round($info->INDUSTRIALES2 + $info->ACIDOS_GRASOS_ACIDULADO2 + $info->SERVICIO_MAQUILA2);
            $ebitda = intval($c71+$totlNoOp+$utilAntImp+$info->EBITDA);
            array_push($data1, [$financiero, $totVEN1, $retActiv1, $gravMov1, $otros1, $totGasOper1, $infoTOT1, $ebitda]);
        }
        $data2 = [];
        foreach ($infoTons as $info2) {
            $venTON = round($info2->TON_ACEITES + $info2->TON_MARGARINAS + $info2->TON_SOLIDOS_CREMOSOS + $info2->TON_INDUSTRIALES_OLEO + $info2->TON_ACIDOS_GRASOS_ACIDULADO);
            array_push($data2, [$venTON]);
        }
        $amount = count($data2) - 1;
        $formOper = [];
        $c=1;
        for ($i = 0; $i <= $amount; $i++) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
                $financieroV = round($data1[$i][0], 5) / round($data2[$i][0], 5);
                $porceFinanc = round($financieroV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $retActV = round($data1[$i][2], 5) / round($data2[$i][0], 5);
                $porceRerAc = round($retActV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $gravMoV = round($data1[$i][3], 5) / round($data2[$i][0], 5);
                $porceGravMov = round($gravMoV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $otrosV = round($data1[$i][4], 5) / round($data2[$i][0], 5);
                $porceOtros = round($otrosV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $totNoOp = $financieroV + $retActV + $gravMoV + $otrosV;
                $porceTotNoOp = $totNoOp * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $totGasOperR = $data1[$i][5] / $data2[$i][0];
                $TOTVEN = round($data1[$i][1]) / round($data2[$i][0]);
                $totCosVen = $data1[$i][6] / $data2[$i][0];
                $utilBrut = intval($TOTVEN - $totCosVen);
                $utilOper = intval(+$utilBrut - intval($totGasOperR));
                $utilAntImp = (+$utilOper - intval(round($totNoOp)));
                $porceUtilAntImp =  $utilAntImp * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $ebtidaV = $data1[$i][7] / $data2[$i][0];
                $porceEbtida = round($ebtidaV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $dateObject = DateTime::createFromFormat('!m', $infoNoOpe[$i]->INF_D_MES)->format('F');
                array_push($mes, ['mes' => __($dateObject)]);
                array_push($formOper, [
                    intval(round($financieroV)), round($porceFinanc, 2) . '%', intval(round($retActV)), round($porceRerAc, 2) . '%', intval(round($gravMoV)), round($porceGravMov, 2) . '%', intval(round($otrosV)), round($porceOtros, 2) . '%', intval(round($totNoOp)), round($porceTotNoOp, 2) . '%', intval(round($utilAntImp)), round($porceUtilAntImp, 2) . '%', intval(round($ebtidaV)),
                    round($porceEbtida, 2) . '%'
                ]);
                $c++;
                switch ($c) {
                    case $c == 4:
                        $gastosNoOperacionalesTabla= $this->tablaGastosNoOperacionalesC();
                        $gastosNoOperacionalesTabla= array_slice($gastosNoOperacionalesTabla,3,1);
                        $cuenCos = count($gastosNoOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosNoOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosNoOperacionalesTabla = array_values($gastosNoOperacionalesTabla[0]);
                        $ventasToneladasTabla= $this->TablaVentasToneladasC();
                        $ventasToneladasTabla= array_slice($ventasToneladasTabla,3,1);
                        $ventasNetasUnitariasTabla= $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla= array_slice($ventasNetasUnitariasTabla,3,1);
                        $gastosOperacionalesUnitariosTabla= $this->tablaGastosOperacionalesUnitC();
                        $gastosOperacionalesUnitariosTabla= array_slice($gastosOperacionalesUnitariosTabla,3,1);
                        $trimestre = [];
                        for ($i = 0; $i < count($gastosNoOperacionalesTabla)-3; $i++) {
                            $val= round($gastosNoOperacionalesTabla[$i]/$ventasToneladasTabla[0][0]);
                            array_push($trimestre, $val);
                            array_push($trimestre, round($val*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        }
                        $totalNoOper= $trimestre[0]+$trimestre[2]+$trimestre[4]+$trimestre[6];
                        array_push($trimestre, $totalNoOper);
                        array_push($trimestre, round($totalNoOper*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $utilAntesOp= $totalNoOper-$gastosOperacionalesUnitariosTabla[0][32];
                        array_push($trimestre, $utilAntesOp);
                        array_push($trimestre, round($utilAntesOp*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $ebtida= round($gastosNoOperacionalesTabla[6]/$ventasToneladasTabla[0][0]);
                        array_push($trimestre, $ebtida);
                        array_push($trimestre, round($ebtida*100/$ventasNetasUnitariasTabla[0][7],2).'%');      
                        $c++;
                        array_push($formOper,$trimestre);
                        array_push($mes, ['mes' => 'TRIMESTRE']);
                        break;
                    case $c == 8:
                        $gastosNoOperacionalesTabla= $this->tablaGastosNoOperacionalesC();
                        $gastosNoOperacionalesTabla= array_slice($gastosNoOperacionalesTabla,7,1);
                        $cuenCos = count($gastosNoOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosNoOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosNoOperacionalesTabla = array_values($gastosNoOperacionalesTabla[0]);
                        $ventasToneladasTabla= $this->TablaVentasToneladasC();
                        $ventasToneladasTabla= array_slice($ventasToneladasTabla,7,1);
                        $ventasNetasUnitariasTabla= $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla= array_slice($ventasNetasUnitariasTabla,7,1);
                        $gastosOperacionalesUnitariosTabla= $this->tablaGastosOperacionalesUnitC();
                        $gastosOperacionalesUnitariosTabla= array_slice($gastosOperacionalesUnitariosTabla,7,1);
                        $trimestre = [];
                        for ($i = 0; $i < count($gastosNoOperacionalesTabla)-3; $i++) {
                            $val= round($gastosNoOperacionalesTabla[$i]/$ventasToneladasTabla[0][0]);
                            array_push($trimestre, $val);
                            array_push($trimestre, round($val*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        }
                        $totalNoOper= $trimestre[0]+$trimestre[2]+$trimestre[4]+$trimestre[6];
                        array_push($trimestre, $totalNoOper);
                        array_push($trimestre, round($totalNoOper*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $utilAntesOp= $totalNoOper-$gastosOperacionalesUnitariosTabla[0][32];
                        array_push($trimestre, $utilAntesOp);
                        array_push($trimestre, round($utilAntesOp*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $ebtida= round($gastosNoOperacionalesTabla[6]/$ventasToneladasTabla[0][0]);
                        array_push($trimestre, $ebtida);
                        array_push($trimestre, round($ebtida*100/$ventasNetasUnitariasTabla[0][7],2).'%');      
                        $c++;
                        array_push($formOper,$trimestre);
                        array_push($mes, ['mes' => 'TRIMESTRE']);
                        break;
                    case $c == 12:
                        $gastosNoOperacionalesTabla= $this->tablaGastosNoOperacionalesC();
                        $gastosNoOperacionalesTabla= array_slice($gastosNoOperacionalesTabla,11,1);
                        $cuenCos = count($gastosNoOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosNoOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosNoOperacionalesTabla = array_values($gastosNoOperacionalesTabla[0]);
                        $ventasToneladasTabla= $this->TablaVentasToneladasC();
                        $ventasToneladasTabla= array_slice($ventasToneladasTabla,11,1);
                        $ventasNetasUnitariasTabla= $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla= array_slice($ventasNetasUnitariasTabla,11,1);
                        $gastosOperacionalesUnitariosTabla= $this->tablaGastosOperacionalesUnitC();
                        $gastosOperacionalesUnitariosTabla= array_slice($gastosOperacionalesUnitariosTabla,11,1);
                        $trimestre = [];
                        for ($i = 0; $i < count($gastosNoOperacionalesTabla)-3; $i++) {
                            $val= round($gastosNoOperacionalesTabla[$i]/$ventasToneladasTabla[0][0]);
                            array_push($trimestre, $val);
                            array_push($trimestre, round($val*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        }
                        $totalNoOper= $trimestre[0]+$trimestre[2]+$trimestre[4]+$trimestre[6];
                        array_push($trimestre, $totalNoOper);
                        array_push($trimestre, round($totalNoOper*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $utilAntesOp= $totalNoOper-$gastosOperacionalesUnitariosTabla[0][32];
                        array_push($trimestre, $utilAntesOp);
                        array_push($trimestre, round($utilAntesOp*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $ebtida= round($gastosNoOperacionalesTabla[6]/$ventasToneladasTabla[0][0]);
                        array_push($trimestre, $ebtida);
                        array_push($trimestre, round($ebtida*100/$ventasNetasUnitariasTabla[0][7],2).'%');      
                        $c++;
                        array_push($formOper,$trimestre);
                        array_push($mes, ['mes' => 'TRIMESTRE']);
                        break;
                    case $c == 15 :
                        $gastosNoOperacionalesTabla= $this->tablaGastosNoOperacionalesC();
                        $gastosNoOperacionalesTabla= array_slice($gastosNoOperacionalesTabla,15,1);
                        $cuenCos = count($gastosNoOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosNoOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosNoOperacionalesTabla = array_values($gastosNoOperacionalesTabla[0]);
                        $ventasToneladasTabla= $this->TablaVentasToneladasC();
                        $ventasToneladasTabla= array_slice($ventasToneladasTabla,15,1);
                        $ventasNetasUnitariasTabla= $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla= array_slice($ventasNetasUnitariasTabla,15,1);
                        $gastosOperacionalesUnitariosTabla= $this->tablaGastosOperacionalesUnitC();
                        $gastosOperacionalesUnitariosTabla= array_slice($gastosOperacionalesUnitariosTabla,15,1);
                        $trimestre = [];
                        for ($i = 0; $i < count($gastosNoOperacionalesTabla)-3; $i++) {
                            $val= round($gastosNoOperacionalesTabla[$i]/$ventasToneladasTabla[0][0]);
                            array_push($trimestre, $val);
                            array_push($trimestre, round($val*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        }
                        $totalNoOper= $trimestre[0]+$trimestre[2]+$trimestre[4]+$trimestre[6];
                        array_push($trimestre, $totalNoOper);
                        array_push($trimestre, round($totalNoOper*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $utilAntesOp= $totalNoOper-$gastosOperacionalesUnitariosTabla[0][32];
                        array_push($trimestre, $utilAntesOp);
                        array_push($trimestre, round($utilAntesOp*100/$ventasNetasUnitariasTabla[0][7],2).'%');
                        $ebtida= round($gastosNoOperacionalesTabla[6]/$ventasToneladasTabla[0][0]);
                        array_push($trimestre, $ebtida);
                        array_push($trimestre, round($ebtida*100/$ventasNetasUnitariasTabla[0][7],2).'%');      
                        $c++;
                        array_push($formOper,$trimestre);
                        array_push($mes, ['mes' => 'TRIMESTRE']);
                        break;
                }

            }else{
                $UtilOperGasOper= $this->tablaGastosOperacionalesUnitC();
                $UtilOperGasOper= array_slice($UtilOperGasOper,$i,1);
                $financieroV = round($data1[$i][0], 5) / round($data2[$i][0], 5);
                $porceFinanc = round($financieroV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $retActV = round($data1[$i][2], 5) / round($data2[$i][0], 5);
                $porceRerAc = round($retActV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $gravMoV = round($data1[$i][3], 5) / round($data2[$i][0], 5);
                $porceGravMov = round($gravMoV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $otrosV = round($data1[$i][4], 5) / round($data2[$i][0], 5);
                $porceOtros = round($otrosV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $totNoOp = $financieroV + $retActV + $gravMoV + $otrosV;
                $porceTotNoOp = $totNoOp * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $totGasOperR = $data1[$i][5] / $data2[$i][0];
                $TOTVEN = round($data1[$i][1]) / round($data2[$i][0]);
                $totCosVen = $data1[$i][6] / $data2[$i][0];
                $utilBrut = intval($TOTVEN - $totCosVen);
                $utilOper = intval(+$utilBrut - intval($totGasOperR));
                $utilAntImp = ($utilOper - intval(round($totNoOp)));
                $porceUtilAntImp =  $utilAntImp * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $ebtidaV = $data1[$i][7] / $data2[$i][0];
                $porceEbtida = round($ebtidaV, 2) * 100 / intval($data1[$i][1] / $data2[$i][0]);
                $dateObject = DateTime::createFromFormat('!m', $infoNoOpe[$i]->INF_D_MES)->format('F');
                array_push($mes, ['mes' => __($dateObject)]);
                array_push($formOper, [
                    intval(round($financieroV)), round($porceFinanc, 2) . '%', intval(round($retActV)), round($porceRerAc, 2) . '%', intval(round($gravMoV)), round($porceGravMov, 2) . '%', intval(round($otrosV)), round($porceOtros, 2) . '%', intval(round($totNoOp)), round($porceTotNoOp, 2) . '%', intval(round($utilAntImp)), round($porceUtilAntImp, 2) . '%', intval(round($ebtidaV)),
                    round($porceEbtida, 2) . '%'
                ]);
                $c++;
            }
        }
        array_push($mes, ['mes' => 'ACUMULADO']);
        array_push($mes, ['mes' => 'PROMEDIO']);

        $noOperacionalesUnit = $this->tablaGastosNoOperacionales($fechaIni, $fechaFin);
        $noOperacionalesUnit = array_slice($noOperacionalesUnit,-2 , 2);
        $ventasToneladas = $this->TablaVentasToneladas($fechaIni, $fechaFin);
        $gasOperacionalesUnit = $this->tablaGastosOperacionalesUnit($fechaIni, $fechaFin);
        $ventasNetasUnitarias = $this->TablaVentasUnit($fechaIni, $fechaFin);


        $acumulados = [];
        for ($i = 0; $i < count($noOperacionalesUnit[0]) - 7; $i++) {
            if ($i % 2 == 0) {
                $ac = intval(round($noOperacionalesUnit[0][$i] / $ventasToneladas[11][0]));
                $pr = round($ac / $ventasNetasUnitarias[11][7], 2) . '%';
                array_push($acumulados, $ac);
                array_push($acumulados, $pr);
            }
        }
        $acumTotNoOper = $acumulados[0] + $acumulados[2] + $acumulados[4] + $acumulados[6];
        $porceAcumTotNoOper = round($acumTotNoOper / $ventasNetasUnitarias[11][7], 2) . '%';
        $acumUtilAntImp = $gasOperacionalesUnit[11][32] - $acumTotNoOper;
        $porceAcumUtilAntImp = round($acumUtilAntImp / $ventasNetasUnitarias[11][7], 2) . '%';
        $acumEbtidaUnit = round($noOperacionalesUnit[0][12] / $ventasToneladas[11][0], 2);
        $porceEbtidaUnit = round($acumEbtidaUnit / $ventasNetasUnitarias[11][7], 2) . '%';
        array_push($acumulados, $acumTotNoOper);
        array_push($acumulados, $porceAcumTotNoOper);
        array_push($acumulados, $acumUtilAntImp);
        array_push($acumulados, $porceAcumUtilAntImp);
        array_push($acumulados, $acumEbtidaUnit);
        array_push($acumulados, $porceEbtidaUnit);


        $promedios = [];
        for ($i = 0; $i < count($noOperacionalesUnit[1]) - 7; $i++) {
            if ($i % 2 == 0) {
                $prom = intval(round($noOperacionalesUnit[1][$i] / $ventasToneladas[12][0]));
                $porce = round($prom / $ventasNetasUnitarias[12][7], 2) . '%';
                array_push($promedios, $prom);
                array_push($promedios, $porce);
            }
        }

        $promTotNoOper = $promedios[0] + $promedios[2] + $promedios[4] + $promedios[6];
        $porcePromTotNoOper = round($promTotNoOper / $ventasNetasUnitarias[12][7], 2) . '%';
        array_push($promedios, $promTotNoOper);
        array_push($promedios, $porcePromTotNoOper);
        $promUtilAntImp = $gasOperacionalesUnit[12][32] - $promTotNoOper;
        $porcePromUtilAntImp = round($promUtilAntImp / $ventasNetasUnitarias[12][7], 2) . '%';
        array_push($promedios, $promUtilAntImp);
        array_push($promedios, $porcePromUtilAntImp);
        $promEbtidaUnit = round($noOperacionalesUnit[1][12] / $ventasToneladas[12][0]);
        $porcePromEbtidaUnit = round($promEbtidaUnit / $ventasNetasUnitarias[12][7], 2) . '%';
        array_push($promedios, $promEbtidaUnit);
        array_push($promedios, $porcePromEbtidaUnit);



        array_push($formOper, $acumulados);
        array_push($formOper, $promedios);
        return view('NonOperatingExpenses\list_non_operating_expensesUnit', ['headers' => $headers, 'dates' => $formOper, 'mes' => $mes, 'contador' => count($formOper[0])]);
    }
}
