<?php

namespace App\Http\Traits;

use DateTime;
use Illuminate\Support\Facades\DB;

trait GastosOperUnitTraitC
{
 use CostosUnitTraitC;
 use VentasNetasUnitTrait;
 use GastosOperTrait;
 use VentasToneladasTrait;
 use GastosOperTraitC;
 
    public function tablaGastosOperacionalesUnitC()
    {
        $infoGastosUn = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
        $infoGastosUn = $infoGastosUn->toArray();
        $infoTonsUn = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->orderBy('INF_D_FECHAS', 'asc')->get();
        $infoTonsUn = $infoTonsUn->toArray();
        

        $headers = [
            'GASTOS DE ADMINISTRACION', 'PORCENTAJE GASTOS DE ADMINISTRACION', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'HONORARIOS', 'PORCENTAJE HONORARIOS',
            'SERVICIOS', 'PORCENTAJE SERVICIOS', 'OTROS', 'PORCENTAJE OTROS', 'GASTOS DE VENTAS', 'PORCENTAJE GASTOS DE VENTAS', 'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'POLIZA CARTERA',
            'PORCENTAJE POLIZA CARTERA', 'FLETES', 'PORCENTAJE FLETES', 'SERVICIO LOGISTICO', 'PORCENTAJE SERVICIO LOGISTICO', 'ESTRATEGIA COMERCIAL', 'PORCENTAJE ESTRATEGIA COMERCIAL', 'IMPUESTOS',
            'PORCENTAJE IMPUESTOS', 'DESCUENTOS PRONTO PAGO', 'PORCENTAJE DESCUENTOS PRONTO PAGO', 'OTROS', 'PORCENTAJE OTROS', 'DEPRECIACIONES Y AMORTIZACIONES', 'PORCENTAJE DEPRECIACIONES Y AMORTIZACIONES',
            'TOTAL GASTOS OPERACIONALES', 'PORCENTAJE TOTAL GASTOS OPERACIONALES', 'UTILIDAD OPERACIONAL', 'PORCENTAJE UTILIDAD OPERACIONAL'
        ];
        $mes = [];
        $data1 = [];
        $ven = [];
        $gasOp = [];
        $arrTotCosVen = [];
        foreach ($infoGastosUn as $info1) {
            $totCosVen = round($info1->ACEITES2, 5) + round($info1->MARGARINAS2, 5) + round($info1->SOLIDOS_CREMOSOS2, 5) + round($info1->INDUSTRIALES2, 5) + round($info1->ACIDOS_GRASOS_ACIDULADO2) + round($info1->SERVICIO_MAQUILA2, 5);
            $gasAdmon = round($info1->GASTOS_ADMINISTRACION, 5);
            $gasPerson = round($info1->GASTOS_PERSONAL, 5);
            $honorarios = round($info1->HONORARIOS, 5);
            $servicios = round($info1->SERVICIOS, 5);
            $otros = $gasAdmon - $gasPerson - $honorarios - $servicios;
            $gasVentas = round($info1->GASTOS_VENTAS, 5);
            $gasPerson2 = round($info1->GASTOS_PERSONAL2, 5);
            $polizCart = round($info1->POLIZA_CARTERA, 5);
            $fletes = round($info1->FLETES, 5);
            $servicLog = round($info1->SERVICIO_LOGISTICO, 5);
            $estratComer = round($info1->ESTRATEGIA_COMERCIAL, 5);
            $impuestos = round($info1->IMPUESTOS, 5);
            $descuProntP = round($info1->DES_PRONTO_PAGO, 5);
            $otros2 = $gasVentas - $gasPerson2 - $polizCart - $fletes - $servicLog - $estratComer - $impuestos - $descuProntP;
            $depresiaci = round($info1->DEPRECIACIONES_AMORTIZACIONES, 5);
            $totVen = round($info1->ACEITES, 5) + round($info1->MARGARINAS, 5) + round($info1->SOLIDOS_CREMOSOS, 5) + round($info1->INDUSTRIALES, 5) + round($info1->ACIDOS_GRASOS_ACIDULADO, 5) + round($info1->SERVICIO_MAQUILA, 5);
            $servMqui = round($info1->SERVICIO_MAQUILA, 5);
            $totVEN = (round($info1->ACEITES, 5) + round($info1->MARGARINAS, 5) + round($info1->SOLIDOS_CREMOSOS, 5) + round($info1->INDUSTRIALES, 5) + round($info1->ACIDOS_GRASOS_ACIDULADO, 5) + round($info1->SERVICIO_MAQUILA, 5)) - round($info1->SERVICIO_MAQUILA, 5);
            $totGasOper = round($gasAdmon + $gasVentas, 5) + $depresiaci;
            $dateObject = DateTime::createFromFormat('m', $info1->INF_D_MES)->format('F');
            $infoTOT = round($info1->SOLIDOS_CREMOSOS2 + $info1->MARGARINAS2 + $info1->ACEITES2) + round($info1->INDUSTRIALES2 + $info1->ACIDOS_GRASOS_ACIDULADO2 + $info1->SERVICIO_MAQUILA2);
            array_push($data1, [
                $gasAdmon, $totVEN, $gasPerson, $honorarios, $servicios, $otros, $gasVentas, $gasPerson2, $polizCart, $fletes, $servicLog, $estratComer, $impuestos, $descuProntP, $otros2, $depresiaci, $totGasOper, $infoTOT
            ]);
            array_push($mes, ['mes' => $dateObject]);
            array_push($ven, [$servMqui, $totVen]);
            array_push($arrTotCosVen, $totCosVen);
            array_push($gasOp, [
                $gasAdmon, $gasPerson, $honorarios, $servicios, $otros, $gasVentas, $gasPerson2, $polizCart, $fletes, $servicLog, $estratComer, $impuestos, $descuProntP, $otros2, $depresiaci
            ]);
        }

        $data2 = [];
        $mes2=[];
        foreach ($infoTonsUn as $info2) {
            $venTON = round($info2->TON_ACEITES, 5) + round($info2->TON_MARGARINAS, 5) + round($info2->TON_SOLIDOS_CREMOSOS, 5) + round($info2->TON_INDUSTRIALES_OLEO, 5) + round($info2->TON_ACIDOS_GRASOS_ACIDULADO, 5);
            array_push($data2, round($venTON, 5));
            $dateObject2 = DateTime::createFromFormat('m', $info2->INF_D_MES)->format('F');
            array_push($mes2, ['mes' => $dateObject2]);
        }

        $mest = (count($mes) < count($mes2)) ? $mes : $mes2; 
        $meses=[];
        $amount = count($mest);
        $m=0;
        $h = 1;
        $formOper = [];
        for ($i = 0; $i < $amount; $i++) {
            if ($h == 3 || $h == 7 || $h == 11 || $h == 15) {
                $restaVen = round($data1[$i][1], 5);
                $sumTonel = round($data2[$i], 5);
                $totVenUnit = intval(round($restaVen / $sumTonel));
                $gasAdmonR = round($data1[$i][0], 5) / $sumTonel;
                $porceAdmonR = round($gasAdmonR*100 / $totVenUnit, 2);
                $gasPersonR = round($data1[$i][2], 5) / $sumTonel;
                $porcePersonR = round($gasPersonR*100 / $totVenUnit, 2);
                $honorariosR = intval(round($data1[$i][3] / $sumTonel));
                $porceHonorR = round($honorariosR*100 / $totVenUnit, 2);
                $serviciosR = round($data1[$i][4], 5) / $sumTonel;
                $porceServiR = round($serviciosR*100 / $totVenUnit, 2);
                $otrosR = round($data1[$i][5], 5) / $sumTonel;
                $porceOtrosR = round($otrosR*100 / $totVenUnit, 2);
                $gasVentasR = intval(round($data1[$i][6] / $sumTonel));
                $porceGasVentR = round($gasVentasR*100/ $totVenUnit, 2);
                $gasPerson2R = round($data1[$i][7], 5) / $sumTonel;
                $porcePerson2R = round($gasPerson2R*100 / $totVenUnit, 2);
                $polizCartR = round($data1[$i][8], 5) / $sumTonel;
                $porcePolizCartR = round($polizCartR*100 / $totVenUnit, 2);
                $fletesR = round($data1[$i][9], 5) / $sumTonel;
                $porceFletesR = round($fletesR*100 / $totVenUnit, 2);
                $servicLogR = round($data1[$i][10], 5) / $sumTonel;
                $porceServicLogR = round($servicLogR*100 / $totVenUnit, 2);
                $estratComerR = round($data1[$i][11], 5) / $sumTonel;
                $porceEstratComerR = round($estratComerR*100 / $totVenUnit, 2);
                $impuestosR = round($data1[$i][12], 5) / $sumTonel;
                $porceImpuestosR = round($impuestosR*100 / $totVenUnit, 2);
                $descuentProntPR = round($data1[$i][13], 5) / $sumTonel;
                $porceDescuentProntPR = round($descuentProntPR*100 / $totVenUnit, 2);
                $otros2R = intval(round($data1[$i][14] / $sumTonel));
                $porceOtros2R = round($otros2R*100 / $totVenUnit, 2);
                $depreciaciR = round($data1[$i][15] / $sumTonel);
                $porceDepreciaR = round($depreciaciR*100 / $totVenUnit, 2);
                $totGasOperR = round($data1[$i][16] / $sumTonel);
                $porceTotGasOperR = round($totGasOperR*100 / $totVenUnit, 2);
                $TOTVEN = round($data1[$i][1]) / round($data2[$i]);
                $totCosVen = $data1[$i][17] / $data2[$i];
                $utilBrut = intval($TOTVEN - $totCosVen);
                $utilOper = +$utilBrut - intval(round($totGasOperR));
                $porceUtilBrR = round($utilOper*100 / $totVenUnit, 2);
                array_push($meses,$mes[$m]);
                $m++;
                array_push($formOper, [
                    $gasAdmonR, $porceAdmonR . '%', intval($gasPersonR), $porcePersonR . '%',
                    intval($honorariosR), $porceHonorR . '%', intval($serviciosR), $porceServiR . '%', intval($otrosR), $porceOtrosR . '%', intval($gasVentasR), $porceGasVentR . '%', intval($gasPerson2R), $porcePerson2R . '%', intval($polizCartR), $porcePolizCartR . '%', intval($fletesR), $porceFletesR . '%', intval($servicLogR), $porceServicLogR . '%', intval($estratComerR), $porceEstratComerR . '%', intval($impuestosR), $porceImpuestosR . '%', intval($descuentProntPR), $porceDescuentProntPR . '%', intval($otros2R), $porceOtros2R . '%', intval($depreciaciR), $porceDepreciaR . '%', intval($totGasOperR), $porceTotGasOperR . '%', intval($utilOper), $porceUtilBrR . '%'
                ]);
                $h++;
                switch ($h) {
                    case $h == 4:
                        $gastosOperacionalesTabla = $this->tablaGastosoperacionalesC();
                        $gastosOperacionalesTabla = array_slice($gastosOperacionalesTabla, 3, 1);
                        $cuenCos = count($gastosOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosOperacionalesTabla = array_values($gastosOperacionalesTabla[0]);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 3, 1);
                        $ventasNetasUnitariasTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla = array_slice($ventasNetasUnitariasTabla, 3, 1);
                        $costosVentasUnitariosTabla = $this->TablaCostosUnitC();
                        $costosVentasUnitariosTabla = array_slice($costosVentasUnitariosTabla, 3, 1);
                        $trimestre = [];
                        for ($s = 0; $s < count($gastosOperacionalesTabla) - 2; $s++) {
                            $gas = round($gastosOperacionalesTabla[$s] / $toneladasTabla[0][0]);
                            array_push($trimestre, $gas);
                            array_push($trimestre, round($gas * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        }
                        $totGasOpera = $trimestre[0] + $trimestre[10] + $trimestre[28];
                        array_push($trimestre, $totGasOpera);
                        $porceTotaGasOpera = round($totGasOpera * 100 / $ventasNetasUnitariasTabla[0][7]);
                        array_push($trimestre, round($porceTotaGasOpera, 2) . '%');
                        $utilopera = $costosVentasUnitariosTabla[0][14]-$totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        $h++;
                        
                        break;
                    case $h == 8:
                        $gastosOperacionalesTabla = $this->tablaGastosoperacionalesC();
                        $gastosOperacionalesTabla = array_slice($gastosOperacionalesTabla, 3, 1);
                        $cuenCos = count($gastosOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosOperacionalesTabla = array_values($gastosOperacionalesTabla[0]);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 3, 1);
                        $ventasNetasUnitariasTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla = array_slice($ventasNetasUnitariasTabla, 3, 1);
                        $costosVentasUnitariosTabla = $this->TablaCostosUnitC();
                        $costosVentasUnitariosTabla = array_slice($costosVentasUnitariosTabla, 3, 1);
                        $trimestre = [];
                        for ($s = 0; $s < count($gastosOperacionalesTabla) - 2; $s++) {
                            $gas = round($gastosOperacionalesTabla[$s] / $toneladasTabla[0][0]);
                            array_push($trimestre, $gas);
                            array_push($trimestre, round($gas * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        }
                        $totGasOpera = $trimestre[0] + $trimestre[10] + $trimestre[28];
                        array_push($trimestre, $totGasOpera);
                        $porceTotaGasOpera = round($totGasOpera * 100 / $ventasNetasUnitariasTabla[0][7]);
                        array_push($trimestre, round($porceTotaGasOpera, 2) . '%');
                        $utilopera = $costosVentasUnitariosTabla[0][14]-$totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        $h++;
                        
                        break;
                    case $h == 12:
                        $gastosOperacionalesTabla = $this->tablaGastosoperacionalesC();
                        $gastosOperacionalesTabla = array_slice($gastosOperacionalesTabla, 3, 1);
                        $cuenCos = count($gastosOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosOperacionalesTabla = array_values($gastosOperacionalesTabla[0]);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 3, 1);
                        $ventasNetasUnitariasTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla = array_slice($ventasNetasUnitariasTabla, 3, 1);
                        $costosVentasUnitariosTabla = $this->TablaCostosUnitC();
                        $costosVentasUnitariosTabla = array_slice($costosVentasUnitariosTabla, 3, 1);
                        $trimestre = [];
                        for ($s = 0; $s < count($gastosOperacionalesTabla) - 2; $s++) {
                            $gas = round($gastosOperacionalesTabla[$s] / $toneladasTabla[0][0]);
                            array_push($trimestre, $gas);
                            array_push($trimestre, round($gas * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        }
                        $totGasOpera = $trimestre[0] + $trimestre[10] + $trimestre[28];
                        array_push($trimestre, $totGasOpera);
                        $porceTotaGasOpera = round($totGasOpera * 100 / $ventasNetasUnitariasTabla[0][7]);
                        array_push($trimestre, round($porceTotaGasOpera, 2) . '%');
                        $utilopera = $costosVentasUnitariosTabla[0][14]-$totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        $h++;
                        break;
                    case $h == 16:
                        $gastosOperacionalesTabla = $this->tablaGastosoperacionalesC();
                        $gastosOperacionalesTabla = array_slice($gastosOperacionalesTabla, 3, 1);
                        $cuenCos = count($gastosOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosOperacionalesTabla = array_values($gastosOperacionalesTabla[0]);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 3, 1);
                        $ventasNetasUnitariasTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla = array_slice($ventasNetasUnitariasTabla, 3, 1);
                        $costosVentasUnitariosTabla = $this->TablaCostosUnitC();
                        $costosVentasUnitariosTabla = array_slice($costosVentasUnitariosTabla, 3, 1);
                        $trimestre = [];
                        for ($s = 0; $s < count($gastosOperacionalesTabla) - 2; $s++) {
                            $gas = round($gastosOperacionalesTabla[$s] / $toneladasTabla[0][0]);
                            array_push($trimestre, $gas);
                            array_push($trimestre, round($gas * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        }
                        $totGasOpera = $trimestre[0] + $trimestre[10] + $trimestre[28];
                        array_push($trimestre, $totGasOpera);
                        $porceTotaGasOpera = round($totGasOpera * 100 / $ventasNetasUnitariasTabla[0][7]);
                        array_push($trimestre, round($porceTotaGasOpera, 2) . '%');
                        $utilopera = $costosVentasUnitariosTabla[0][14]-$totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        $h++;
                        break;
                }
            } else {
                $restaVen = round($data1[$i][1], 5);
                $sumTonel = round($data2[$i], 5);
                $totVenUnit = intval(round($restaVen / $sumTonel));
                $gasAdmonR = round($data1[$i][0], 5) / $sumTonel;
                $porceAdmonR = round($gasAdmonR*100 / $totVenUnit, 2);
                $gasPersonR = round($data1[$i][2], 5) / $sumTonel;
                $porcePersonR = round($gasPersonR*100 / $totVenUnit, 2);
                $honorariosR = intval(round($data1[$i][3] / $sumTonel));
                $porceHonorR = round($honorariosR*100 / $totVenUnit, 2);
                $serviciosR = round($data1[$i][4], 5) / $sumTonel;
                $porceServiR = round($serviciosR*100 / $totVenUnit, 2);
                $otrosR = round($data1[$i][5], 5) / $sumTonel;
                $porceOtrosR = round($otrosR*100 / $totVenUnit, 2);
                $gasVentasR = intval(round($data1[$i][6] / $sumTonel));
                $porceGasVentR = round($gasVentasR*100/ $totVenUnit, 2);
                $gasPerson2R = round($data1[$i][7], 5) / $sumTonel;
                $porcePerson2R = round($gasPerson2R*100 / $totVenUnit, 2);
                $polizCartR = round($data1[$i][8], 5) / $sumTonel;
                $porcePolizCartR = round($polizCartR*100 / $totVenUnit, 2);
                $fletesR = round($data1[$i][9], 5) / $sumTonel;
                $porceFletesR = round($fletesR*100 / $totVenUnit, 2);
                $servicLogR = round($data1[$i][10], 5) / $sumTonel;
                $porceServicLogR = round($servicLogR*100 / $totVenUnit, 2);
                $estratComerR = round($data1[$i][11], 5) / $sumTonel;
                $porceEstratComerR = round($estratComerR*100 / $totVenUnit, 2);
                $impuestosR = round($data1[$i][12], 5) / $sumTonel;
                $porceImpuestosR = round($impuestosR*100 / $totVenUnit, 2);
                $descuentProntPR = round($data1[$i][13], 5) / $sumTonel;
                $porceDescuentProntPR = round($descuentProntPR*100 / $totVenUnit, 2);
                $otros2R = intval(round($data1[$i][14] / $sumTonel));
                $porceOtros2R = round($otros2R*100 / $totVenUnit, 2);
                $depreciaciR = round($data1[$i][15] / $sumTonel);
                $porceDepreciaR = round($depreciaciR*100 / $totVenUnit, 2);
                $totGasOperR = round($data1[$i][16] / $sumTonel);
                $porceTotGasOperR = round($totGasOperR*100 / $totVenUnit, 2);
                $TOTVEN = round($data1[$i][1]) / round($data2[$i]);
                $totCosVen = $data1[$i][17] / $data2[$i];
                $utilBrut = intval($TOTVEN - $totCosVen);
                $utilOper = +$utilBrut - intval(round($totGasOperR));
                $porceUtilBrR = round($utilOper*100 / $totVenUnit, 2);
                    array_push($formOper, [
                        $gasAdmonR, $porceAdmonR . '%', intval($gasPersonR), $porcePersonR . '%',
                        intval($honorariosR), $porceHonorR . '%', intval($serviciosR), $porceServiR . '%', intval($otrosR), $porceOtrosR . '%', intval($gasVentasR), $porceGasVentR . '%', intval($gasPerson2R), $porcePerson2R . '%', intval($polizCartR), $porcePolizCartR . '%', intval($fletesR), $porceFletesR . '%', intval($servicLogR), $porceServicLogR . '%', intval($estratComerR), $porceEstratComerR . '%', intval($impuestosR), $porceImpuestosR . '%', intval($descuentProntPR), $porceDescuentProntPR . '%', intval($otros2R), $porceOtros2R . '%', intval($depreciaciR), $porceDepreciaR . '%', intval($totGasOperR), $porceTotGasOperR . '%', intval($utilOper), $porceUtilBrR . '%'
                    ]);
                    if($m<=count($mes)){
                        array_push($meses,$mes[$m]);
                    }
                    $m++;
                    $h++;
            }
        }
        array_push($meses, ['mes' => 'ACUMULADO']);
        array_push($meses, ['mes' => 'PROMEDIO']);
        $fechaIni = null;
        $fechaFin = null;

        //Inicio acumulados
        $acumGasOper = [];
        for ($i = 0; $i < count($gasOp[0]); $i++) {
            $suma = 0;
            foreach ($gasOp as $gasO) {
                $suma += $gasO[$i];
            }
            array_push($acumGasOper, intval(round($suma)));
        }
        $suma = 0;
        for ($i = 0; $i < count($data2); $i++) {
            $suma += intval(round($data2[$i]));
            $acumTonel = $suma;
        }
        $costosVentasUnitarios = $this->TablaCostosUnitC();
        $costosVentasUnitarios= array_slice($costosVentasUnitarios,-2,2);
        $ventasNetasUnitarios = $this->TablaVentasUnitC();
        $ventasNetasUnitarios = array_slice($ventasNetasUnitarios,-2,2);
        $gastosOperacionales = $this->tablaGastosoperacionalesC();
        $gastosOperacionales= array_slice($gastosOperacionales,-2,2);
        $ventastoneladas = $this->TablaVentasToneladasC();
        $ventastoneladas= array_slice($ventastoneladas,-2,2);
        
        $acumulados = [];
        for ($i = 0; $i < count($acumGasOper); $i++) {
            $entero = intval(round($acumGasOper[$i] / $acumTonel));
            $porcentaje = round($entero * 100 / $ventasNetasUnitarios[0][7]);
            array_push($acumulados, $entero);
            array_push($acumulados, round($porcentaje, 2) . '%');
        }
        $acumTotGasOperUnit = $acumulados[0] + $acumulados[10];
        $porceAcumGasOperUnit = $acumTotGasOperUnit * 100 / $ventasNetasUnitarios[0][7];
        array_push($acumulados, $acumTotGasOperUnit);
        array_push($acumulados, round($porceAcumGasOperUnit, 2) . '%');
        $acumUtilOperUnit = $acumTotGasOperUnit - $costosVentasUnitarios[0][14];
        $porceAcumUtilOperUnit = $acumUtilOperUnit * 100 / $ventasNetasUnitarios[0][7];
        array_push($acumulados, $acumUtilOperUnit);
        array_push($acumulados, round($porceAcumUtilOperUnit, 2) . '%');
        //fin acumulados



        $promedios = [];
        for ($i = 0; $i < count($gastosOperacionales[0]) - 4; $i++) {
            if ($i % 2 == 0) {
                $promGasAdmin = intval(round($gastosOperacionales[1][$i] / $ventastoneladas[1][0]));
                $porcePromGas = round($promGasAdmin * 100 / $ventasNetasUnitarios[1][7], 2) . '%';
                array_push($promedios, $promGasAdmin);
                array_push($promedios, $porcePromGas);
            }
        }
        $promTotalGasOper = $promedios[0] + $promedios[10] + $promedios[28];
        $promPorceTotalGasOper = round($promTotalGasOper * 100 / $ventasNetasUnitarios[1][7], 2) . '%';
        $promUtilOperUnit = $costosVentasUnitarios[1][14] - $promTotalGasOper;
        $promPorceUtilOperUnit = round($promUtilOperUnit * 100 / $ventasNetasUnitarios[1][7], 2) . '%';
        array_push($promedios, $promTotalGasOper);
        array_push($promedios, $promPorceTotalGasOper);
        array_push($promedios, $promUtilOperUnit);
        array_push($promedios, $promPorceUtilOperUnit);


        array_push($formOper, $acumulados);
        array_push($formOper, $promedios);
        return $formOper;
    }

}