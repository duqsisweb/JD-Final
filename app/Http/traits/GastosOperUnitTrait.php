<?php

namespace App\Http\Traits;

use DateTime;
use Illuminate\Support\Facades\DB;

trait GastosOperUnitTrait
{
 use CostosUnitTrait;
 use VentasNetasUnitTrait;
 use GastosOperTrait;
 use VentasToneladasTrait;
 use CostosUnitTraitC;
 
    public function tablaGastosOperacionalesUnit($fechaIni, $fechaFin)
    {


        if ($fechaIni != null) {
            $fechaIni = $fechaIni;
            $fechaFin = $fechaFin;
            $infoGastosUn = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoGastosUn = $infoGastosUn->toArray();
            $infoTonsUn = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTonsUn = $infoTonsUn->toArray();
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoGastosUn = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoGastosUn = $infoGastosUn->toArray();
            $infoTonsUn = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTonsUn = $infoTonsUn->toArray();
        }

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
            $totCosVen = $info1->ACEITES2 + $info1->MARGARINAS2 + $info1->SOLIDOS_CREMOSOS2 + $info1->INDUSTRIALES2 + $info1->ACIDOS_GRASOS_ACIDULADO2 + $info1->SERVICIO_MAQUILA2;
            $gasAdmon = $info1->GASTOS_ADMINISTRACION;
            $gasPerson = $info1->GASTOS_PERSONAL;
            $honorarios = $info1->HONORARIOS;
            $servicios = $info1->SERVICIOS;
            $otros = $gasAdmon - $gasPerson - $honorarios - $servicios;
            $gasVentas = $info1->GASTOS_VENTAS;
            $gasPerson2 = $info1->GASTOS_PERSONAL2;
            $polizCart = $info1->POLIZA_CARTERA;
            $fletes = $info1->FLETES;
            $servicLog = $info1->SERVICIO_LOGISTICO;
            $estratComer = $info1->ESTRATEGIA_COMERCIAL;
            $impuestos = $info1->IMPUESTOS;
            $descuProntP = $info1->DES_PRONTO_PAGO;
            $otros2 = $gasVentas - $gasPerson2 - $polizCart - $fletes - $servicLog - $estratComer - $impuestos - $descuProntP;
            $depresiaci = $info1->DEPRECIACIONES_AMORTIZACIONES;
            $totVen = $info1->ACEITES + $info1->MARGARINAS + $info1->SOLIDOS_CREMOSOS + $info1->INDUSTRIALES + $info1->ACIDOS_GRASOS_ACIDULADO + $info1->SERVICIO_MAQUILA;
            $servMqui = $info1->SERVICIO_MAQUILA;
            $totVEN = ($info1->ACEITES + $info1->MARGARINAS + $info1->SOLIDOS_CREMOSOS + $info1->INDUSTRIALES + $info1->ACIDOS_GRASOS_ACIDULADO + $info1->SERVICIO_MAQUILA) - $info1->SERVICIO_MAQUILA;
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
                $restaVen = $data1[$i][1];
                $sumTonel = $data2[$i];
                $totVenUnit = intval(round($restaVen / $sumTonel));
                $gasAdmonR = $data1[$i][0] / $sumTonel;
                $porceAdmonR = round($gasAdmonR*100 / $totVenUnit, 2);
                $gasPersonR = $data1[$i][2] / $sumTonel;
                $porcePersonR = round($gasPersonR*100 / $totVenUnit, 2);
                $honorariosR = $data1[$i][3] / $sumTonel;
                $porceHonorR = round($honorariosR*100 / $totVenUnit, 2);
                $serviciosR = $data1[$i][4] / $sumTonel;
                $porceServiR = round($serviciosR*100 / $totVenUnit, 2);
                $otrosR = $data1[$i][5] / $sumTonel;
                $porceOtrosR = round($otrosR*100 / $totVenUnit, 2);
                $gasVentasR = $data1[$i][6] / $sumTonel;
                $porceGasVentR = round($gasVentasR*100/ $totVenUnit, 2);
                $gasPerson2R = $data1[$i][7] / $sumTonel;
                $porcePerson2R = round($gasPerson2R*100 / $totVenUnit, 2);
                $polizCartR = $data1[$i][8] / $sumTonel;
                $porcePolizCartR = round($polizCartR*100 / $totVenUnit, 2);
                $fletesR = $data1[$i][9] / $sumTonel;
                $porceFletesR = round($fletesR*100 / $totVenUnit, 2);
                $servicLogR = $data1[$i][10] / $sumTonel;
                $porceServicLogR = round($servicLogR*100 / $totVenUnit, 2);
                $estratComerR = $data1[$i][11] / $sumTonel;
                $porceEstratComerR = round($estratComerR*100 / $totVenUnit, 2);
                $impuestosR = $data1[$i][12] / $sumTonel;
                $porceImpuestosR = round($impuestosR*100 / $totVenUnit, 2);
                $descuentProntPR = $data1[$i][13] / $sumTonel;
                $porceDescuentProntPR = round($descuentProntPR*100 / $totVenUnit, 2);
                $otros2R = $data1[$i][14] / $sumTonel;
                $porceOtros2R = round($otros2R*100 / $totVenUnit, 2);
                $depreciaciR = $data1[$i][15] / $sumTonel;
                $porceDepreciaR = round($depreciaciR*100 / $totVenUnit, 2);
                $totGasOperR = $data1[$i][16] / $sumTonel;
                $porceTotGasOperR = round($totGasOperR*100 / $totVenUnit, 2);
                $TOTVEN = $data1[$i][1] / $data2[$i];
                $totCosVen = $data1[$i][17] / $data2[$i];
                $utilBrut = intval(round($TOTVEN - $totCosVen));
                $utilOper = $utilBrut - $totGasOperR;
                $porceUtilBrR = round($utilOper*100 / $totVenUnit, 2);
                array_push($meses,$mes[$m]);
                $m++;
                array_push($formOper, [
                    $gasAdmonR, $porceAdmonR . '%', $gasPersonR, $porcePersonR . '%',
                    $honorariosR, $porceHonorR . '%', $serviciosR, $porceServiR . '%', $otrosR, $porceOtrosR . '%', $gasVentasR, $porceGasVentR . '%', $gasPerson2R, $porcePerson2R . '%', $polizCartR, $porcePolizCartR . '%', $fletesR, $porceFletesR . '%', 
                    $servicLogR, $porceServicLogR . '%', $estratComerR, $porceEstratComerR . '%', $impuestosR, $porceImpuestosR . '%', $descuentProntPR, $porceDescuentProntPR . '%', $otros2R, $porceOtros2R . '%', $depreciaciR, $porceDepreciaR . '%',
                    $totGasOperR, $porceTotGasOperR . '%', $utilOper, $porceUtilBrR . '%'
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
                            $gas = $gastosOperacionalesTabla[$s] / $toneladasTabla[0][0];
                            array_push($trimestre, $gas);
                            array_push($trimestre, round($gas * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        }
                        $totGasOpera = $trimestre[0] + $trimestre[10] + $trimestre[28];
                        array_push($trimestre, $totGasOpera);
                        $porceTotaGasOpera = round($totGasOpera * 100 / $ventasNetasUnitariasTabla[0][7]);
                        array_push($trimestre, round($porceTotaGasOpera, 2) . '%');
                        $utilopera = $costosVentasUnitariosTabla[0][14]- $totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        break;
                    case $h == 8:
                        $gastosOperacionalesTabla = $this->tablaGastosoperacionalesC();
                        $gastosOperacionalesTabla = array_slice($gastosOperacionalesTabla, 7, 1);
                        $cuenCos = count($gastosOperacionalesTabla[0]);
                        for ($a = 0; $a < $cuenCos; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($gastosOperacionalesTabla[0][$a]);
                            }
                        }
                        $gastosOperacionalesTabla = array_values($gastosOperacionalesTabla[0]);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 7, 1);
                        $ventasNetasUnitariasTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitariasTabla = array_slice($ventasNetasUnitariasTabla, 7, 1);
                        $costosVentasUnitariosTabla = $this->TablaCostosUnitC();
                        $costosVentasUnitariosTabla = array_slice($costosVentasUnitariosTabla, 7, 1);
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
                        $utilopera = $costosVentasUnitariosTabla[0][14]- $totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        break;
                    case $h == 12:
                        $gastosOperacionalesTabla = $this->tablaGastosoperacionalesC();
                        $gastosOperacionalesTabla = array_slice($gastosOperacionalesTabla, 11, 1);
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
                        $ventasNetasUnitariasTabla = array_slice($ventasNetasUnitariasTabla, 11, 1);
                        $costosVentasUnitariosTabla = $this->TablaCostosUnitC();
                        $costosVentasUnitariosTabla = array_slice($costosVentasUnitariosTabla, 11, 1);
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
                        $utilopera = $costosVentasUnitariosTabla[0][14]- $totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        break;
                    case $h == 16:
                        $gastosOperacionalesTabla = $this->tablaGastosoperacionalesC();
                        $gastosOperacionalesTabla = array_slice($gastosOperacionalesTabla, 15, 1);
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
                        $ventasNetasUnitariasTabla = array_slice($ventasNetasUnitariasTabla, 15, 1);
                        $costosVentasUnitariosTabla = $this->TablaCostosUnitC();
                        $costosVentasUnitariosTabla = array_slice($costosVentasUnitariosTabla, 15, 1);
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
                        $utilopera = $costosVentasUnitariosTabla[0][14]- $totGasOpera;
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        break;
                }
                $h++;
            } else {
                $restaVen = $data1[$i][1];
                $sumTonel = $data2[$i];
                $totVenUnit = intval(round($restaVen / $sumTonel));
                $gasAdmonR = $data1[$i][0] / $sumTonel;
                $porceAdmonR = round($gasAdmonR*100 / $totVenUnit, 2);
                $gasPersonR = $data1[$i][2] / $sumTonel;
                $porcePersonR = round($gasPersonR*100 / $totVenUnit, 2);
                $honorariosR = $data1[$i][3] / $sumTonel;
                $porceHonorR = round($honorariosR*100 / $totVenUnit, 2);
                $serviciosR = $data1[$i][4] / $sumTonel;
                $porceServiR = round($serviciosR*100 / $totVenUnit, 2);
                $otrosR = $data1[$i][5] / $sumTonel;
                $porceOtrosR = round($otrosR*100 / $totVenUnit, 2);
                $gasVentasR = $data1[$i][6] / $sumTonel;
                $porceGasVentR = round($gasVentasR*100/ $totVenUnit, 2);
                $gasPerson2R = $data1[$i][7] / $sumTonel;
                $porcePerson2R = round($gasPerson2R*100 / $totVenUnit, 2);
                $polizCartR = $data1[$i][8] / $sumTonel;
                $porcePolizCartR = round($polizCartR*100 / $totVenUnit, 2);
                $fletesR = $data1[$i][9] / $sumTonel;
                $porceFletesR = round($fletesR*100 / $totVenUnit, 2);
                $servicLogR = $data1[$i][10] / $sumTonel;
                $porceServicLogR = round($servicLogR*100 / $totVenUnit, 2);
                $estratComerR = $data1[$i][11] / $sumTonel;
                $porceEstratComerR = round($estratComerR*100 / $totVenUnit, 2);
                $impuestosR = $data1[$i][12] / $sumTonel;
                $porceImpuestosR = round($impuestosR*100 / $totVenUnit, 2);
                $descuentProntPR = $data1[$i][13] / $sumTonel;
                $porceDescuentProntPR = round($descuentProntPR*100 / $totVenUnit, 2);
                $otros2R = $data1[$i][14] / $sumTonel;
                $porceOtros2R = round($otros2R*100 / $totVenUnit, 2);
                $depreciaciR = $data1[$i][15] / $sumTonel;
                $porceDepreciaR = round($depreciaciR*100 / $totVenUnit, 2);
                $totGasOperR = $data1[$i][16] / $sumTonel;
                $porceTotGasOperR = round($totGasOperR*100 / $totVenUnit, 2);
                $TOTVEN = $data1[$i][1] / $data2[$i];
                $totCosVen = $data1[$i][17] / $data2[$i];
                $utilBrut = intval(round($TOTVEN - $totCosVen));
                $utilOper = +$utilBrut - intval(round($totGasOperR));
                $porceUtilBrR = round($utilOper*100 / $totVenUnit, 2);
                array_push($formOper, [
                    $gasAdmonR, $porceAdmonR . '%', $gasPersonR, $porcePersonR . '%',
                    $honorariosR, $porceHonorR . '%', $serviciosR, $porceServiR . '%', $otrosR, $porceOtrosR . '%', $gasVentasR, $porceGasVentR . '%', $gasPerson2R, $porcePerson2R . '%', $polizCartR, $porcePolizCartR . '%', $fletesR, $porceFletesR . '%', 
                    $servicLogR, $porceServicLogR . '%', $estratComerR, $porceEstratComerR . '%', $impuestosR, $porceImpuestosR . '%', $descuentProntPR, $porceDescuentProntPR . '%', $otros2R, $porceOtros2R . '%', $depreciaciR, $porceDepreciaR . '%',
                    $totGasOperR, $porceTotGasOperR . '%', $utilOper, $porceUtilBrR . '%'
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
        array_push($promedios, intval($promTotalGasOper));
        array_push($promedios, $promPorceTotalGasOper);
        array_push($promedios, intval($promUtilOperUnit));
        array_push($promedios, $promPorceUtilOperUnit);


        array_push($formOper, $acumulados);
        array_push($formOper, $promedios);
        return $formOper;
    }

}