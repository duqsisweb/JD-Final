<?php

namespace App\Http\Controllers;

use App\Http\Traits\CostosTrait;
use App\Http\Traits\CostosUnitTrait;
use App\Http\Traits\VentasNetasTrait;
use App\Http\Traits\VentasNetasUnitTrait;
use App\Http\Traits\VentasToneladasTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CostosVentasController extends Controller
{
    use CostosTrait;
    use VentasToneladasTrait;
    use CostosUnitTrait;
    use VentasNetasTrait;
    use VentasNetasUnitTrait; 
    public function total_costs(Request $request)
    {
        if ($request->filter1 != null) {
            if($request->filter1 > $request->filter2){
                return redirect('admin/costos/table')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoSales = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoSales = $infoSales->toArray();
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoSales = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoSales->toArray();
            $infoSales = $infoSales->toArray();
        }
        $headers = ['ACEITES', 'PORCENTAJE ACEITES', 'MARGARINAS', 'PORCENTAJE MARGARINAS', 'SOLIDOS Y CREMOSOS', 'PORCENTAJE SOLIDOS Y CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'PORCENTAJE TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'PORCENTAJE INDUSTRIALES', 'OTROS PRODUCTOS', 'PORCENTAJE OTROS PRODUCTOS', 'SERVICIO DE MAQUILA', 'PORCENTAJE SERVICIO DE MAQUILA', 'TOTAL OTROS', 'PORCENTAJE TOTAL OTROS', 'TOTAL COSTOS DE VENTAS', 'PORCENTAJE TOTAL COSTOS DE VENTAS', 'UTILIDAD BRUTA', 'PORCENTAJE UTILIDAD BRUTA'];
        $formates = [];
        $formates2 = [];
        $mes = [];
        $acumPorceSinSu = [];
        $acumSinSum = [];
        $c = 1;
        foreach ($infoSales as $info) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
                $infoACEITESP =  round($info->ACEITES, 5);
                $infoMARGARINASP =  round($info->MARGARINAS, 5);
                $infoSOLIDOS_CREMOSOSP =  round($info->SOLIDOS_CREMOSOS, 5);
                $infoINDUSTRIALESP =  round($info->INDUSTRIALES, 5);
                $infoOTROSP =  round($info->ACIDOS_GRASOS_ACIDULADO, 5);
                $infoSERVICIO_MAQUILAP =  round($info->SERVICIO_MAQUILA, 5);
                $TOTALPP = $infoACEITESP + $infoMARGARINASP + $infoSOLIDOS_CREMOSOSP;
                $TOTALOP = $infoINDUSTRIALESP + $infoOTROSP + $infoSERVICIO_MAQUILAP;
                $TOTALVP = $TOTALPP + $TOTALOP;
                $dateObject = DateTime::createFromFormat('!m', $info->INF_D_MES)->format('F');
                $infoACEITES = round($info->ACEITES2, 5);
                $porceAc = round($info->ACEITES2 * 100 / $info->ACEITES) . '%';
                $infoMarga = round($info->MARGARINAS2);
                $porceMarga = round($info->MARGARINAS2 * 100 / $info->MARGARINAS, 2) . '%';
                $infoSOLID = round($info->SOLIDOS_CREMOSOS2, 5);
                $porceSOLID = round($info->SOLIDOS_CREMOSOS2 * 100 / $info->SOLIDOS_CREMOSOS, 2) . '%';
                $infoTOTP = round($info->SOLIDOS_CREMOSOS2 + $info->MARGARINAS2 + $info->ACEITES2);
                $TOTALPT = $info->ACEITES + $info->MARGARINAS + $info->SOLIDOS_CREMOSOS;
                $porceTOTALP = round($infoTOTP * 100 / $TOTALPT, 2) . '%';
                $totalProd = intval(round($info->ACEITES, 3) + round($info->MARGARINAS, 3) + round($info->SOLIDOS_CREMOSOS, 3));
                $porceTOTP = round($infoTOTP * 100 / $totalProd, 2) . '%';
                $infoINDU = intval(round($info->INDUSTRIALES2, 5));
                $porceINDU = round($info->INDUSTRIALES2 * 100 / (round($info->INDUSTRIALES, 2)), 2) . '%';
                $infoOTROS = intval(round($info->ACIDOS_GRASOS_ACIDULADO2));
                $porceOTROS = round($info->ACIDOS_GRASOS_ACIDULADO2 * 100 / $info->ACIDOS_GRASOS_ACIDULADO, 2) . '%';
                $infoSERVM = round($info->SERVICIO_MAQUILA2);
                $porceSERVM = round($info->SERVICIO_MAQUILA2 * 100 / $info->SERVICIO_MAQUILA, 2) . '%';
                $TOTALOT = round($info->INDUSTRIALES2 + $info->ACIDOS_GRASOS_ACIDULADO2 + $info->SERVICIO_MAQUILA2);
                $infoTOLALO = $infoINDU + $infoOTROS + $infoSERVM;
                $TOTALO = round($info->INDUSTRIALES + $info->ACIDOS_GRASOS_ACIDULADO + $info->SERVICIO_MAQUILA);
                $porceTOTALO = round($infoTOLALO * 100 / $TOTALO, 2) . '%';
                $infoTOTCOSV = $TOTALOT + $infoTOTP;
                $TOTALV = $totalProd + $TOTALO;
                $porceTOTCOSV = round($infoTOTCOSV * 100 / $TOTALV, 2) . '%';
                $infoTOTALBR = $TOTALV - $infoTOTCOSV;
                $porceTOTALBR =  round($infoTOTALBR * 100 / $TOTALV, 2) . '%';
                array_push($formates, [intval($infoACEITES), $porceAc, intval($infoMarga), $porceMarga, intval($infoSOLID), $porceSOLID, intval($infoTOTP), $porceTOTALP, intval($infoINDU), $porceINDU, intval($infoOTROS), $porceOTROS, intval($infoSERVM), $porceSERVM, intval($TOTALOT), $porceTOTALO, intval($infoTOTCOSV), $porceTOTCOSV, intval($infoTOTALBR), $porceTOTALBR]);
                array_push($acumPorceSinSu, [intval($infoACEITESP), intval($infoMARGARINASP), intval($infoSOLIDOS_CREMOSOSP), intval($TOTALPP), intval($infoINDUSTRIALESP), intval($infoOTROSP), intval($infoSERVICIO_MAQUILAP), intval($TOTALOP), intval($TOTALVP)]);
                array_push($acumSinSum, [$infoACEITES, $infoMarga, $infoSOLID, $infoTOTP, $infoINDU, $infoOTROS, $infoSERVM, $TOTALOT, $infoTOTCOSV, $infoTOTALBR]);
                array_push($mes, ['mes' => __($dateObject)]);
                array_push($mes, ['mes' => 'TRIMESTRE']);
                $c++;
                switch ($c) {
                    case $c == 4:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 3, 1);
                        $formEdit = $formates;
                        $sumaCostos = [];
                        for ($a = 0; $a < count($formEdit[0]); $a++) {
                            $suma = 0;
                            foreach ($formEdit as $prom) {
                                if ($a % 2 == 0) {
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($a = 0; $a < $cuenta; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($sumaCostos[$a]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($a = 0; $a < count($ventasNetasTabla[0]); $a++) {
                            array_push($sumfinals, $sumaCostos[$a]);
                            array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][$a], 2) . '%');
                        }
                        array_push($sumfinals, $sumaCostos[$a]);
                        array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        array_push($formates, $sumfinals);
                        break;
                    case $c == 8:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 7, 1);
                        $formEdit = $formates;
                        $sumaCostos = [];
                        for ($a = 0; $a < count($formEdit[0]); $a++) {
                            $suma = 0;
                            foreach ($formEdit as $prom) {
                                if ($a % 2 == 0) {
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($a = 0; $a < $cuenta; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($sumaCostos[$a]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($a = 0; $a < count($ventasNetasTabla[0]); $a++) {
                            array_push($sumfinals, $sumaCostos[$a]);
                            array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][$a], 2) . '%');
                        }
                        array_push($sumfinals, $sumaCostos[$a]);
                        array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        array_push($formates, $sumfinals);
                        break;
                    case $c == 12:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 11, 1);
                        $formEdit = $formates;
                        $sumaCostos = [];
                        for ($a = 0; $a < count($formEdit[0]); $a++) {
                            $suma = 0;
                            foreach ($formEdit as $prom) {
                                if ($a % 2 == 0) {
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($a = 0; $a < $cuenta; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($sumaCostos[$a]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($a = 0; $a < count($ventasNetasTabla[0]); $a++) {
                            array_push($sumfinals, $sumaCostos[$a]);
                            array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][$a], 2) . '%');
                        }
                        array_push($sumfinals, $sumaCostos[$a]);
                        array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        array_push($formates, $sumfinals);
                        break;
                    case $c == 16:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 15, 1);
                        $formEdit = $formates;
                        $sumaCostos = [];
                        for ($a = 0; $a < count($formEdit[0]); $a++) {
                            $suma = 0;
                            foreach ($formEdit as $prom) {
                                if ($a % 2 == 0) {
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($a = 0; $a < $cuenta; $a++) {
                            if ($a % 2 == 0) {
                            } else {
                                unset($sumaCostos[$a]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($a = 0; $a < count($ventasNetasTabla[0]); $a++) {
                            array_push($sumfinals, $sumaCostos[$a]);
                            array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][$a], 2) . '%');
                        }
                        array_push($sumfinals, $sumaCostos[$a]);
                        array_push($sumfinals, round($sumaCostos[$a] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        array_push($formates, $sumfinals);
                        break;
                }
                $c++;
            } else {
                $infoACEITESP =  round($info->ACEITES, 5);
                $infoMARGARINASP =  round($info->MARGARINAS, 5);
                $infoSOLIDOS_CREMOSOSP =  round($info->SOLIDOS_CREMOSOS, 5);
                $infoINDUSTRIALESP =  round($info->INDUSTRIALES, 5);
                $infoOTROSP =  round($info->ACIDOS_GRASOS_ACIDULADO, 5);
                $infoSERVICIO_MAQUILAP =  round($info->SERVICIO_MAQUILA, 5);
                $TOTALPP = $infoACEITESP + $infoMARGARINASP + $infoSOLIDOS_CREMOSOSP;
                $TOTALOP = $infoINDUSTRIALESP + $infoOTROSP + $infoSERVICIO_MAQUILAP;
                $TOTALVP = $TOTALPP + $TOTALOP;
                $dateObject = DateTime::createFromFormat('!m', $info->INF_D_MES)->format('F');
                $infoACEITES = round($info->ACEITES2, 5);
                $porceAc = round($info->ACEITES2 * 100 / $info->ACEITES) . '%';
                $infoMarga = round($info->MARGARINAS2);
                $porceMarga = round($info->MARGARINAS2 * 100 / $info->MARGARINAS, 2) . '%';
                $infoSOLID = round($info->SOLIDOS_CREMOSOS2, 5);
                $porceSOLID = round($info->SOLIDOS_CREMOSOS2 * 100 / $info->SOLIDOS_CREMOSOS, 2) . '%';
                $infoTOTP = round($info->SOLIDOS_CREMOSOS2 + $info->MARGARINAS2 + $info->ACEITES2);
                $TOTALPT = $info->ACEITES + $info->MARGARINAS + $info->SOLIDOS_CREMOSOS;
                $porceTOTALP = round($infoTOTP * 100 / $TOTALPT, 2) . '%';
                $totalProd = intval(round($info->ACEITES, 3) + round($info->MARGARINAS, 3) + round($info->SOLIDOS_CREMOSOS, 3));
                $porceTOTP = round($infoTOTP * 100 / $totalProd, 2) . '%';
                $infoINDU = intval(round($info->INDUSTRIALES2, 5));
                $porceINDU = round($info->INDUSTRIALES2 * 100 / round($info->INDUSTRIALES, 2), 2) . '%';
                $infoOTROS = intval(round($info->ACIDOS_GRASOS_ACIDULADO2));
                $porceOTROS = round($info->ACIDOS_GRASOS_ACIDULADO2 * 100 / $info->ACIDOS_GRASOS_ACIDULADO, 2) . '%';
                $infoSERVM = round($info->SERVICIO_MAQUILA2);
                $porceSERVM = round($info->SERVICIO_MAQUILA2 * 100 / $info->SERVICIO_MAQUILA, 2) . '%';
                $TOTALOT = round($info->INDUSTRIALES2 + $info->ACIDOS_GRASOS_ACIDULADO2 + $info->SERVICIO_MAQUILA2);
                $infoTOLALO = $infoINDU + $infoOTROS + $infoSERVM;
                $TOTALO = round($info->INDUSTRIALES + $info->ACIDOS_GRASOS_ACIDULADO + $info->SERVICIO_MAQUILA);
                $porceTOTALO = round($infoTOLALO * 100 / $TOTALO, 2) . '%';
                $infoTOTCOSV = $TOTALOT + $infoTOTP;
                $TOTALV = $totalProd + $TOTALO;
                $porceTOTCOSV = round($infoTOTCOSV * 100 / $TOTALV, 2) . '%';
                $infoTOTALBR = $TOTALV - $infoTOTCOSV;
                $porceTOTALBR =  round($infoTOTALBR * 100 / $TOTALV, 2) . '%';
                
                array_push($formates, [intval($infoACEITES), $porceAc, intval($infoMarga), $porceMarga, intval($infoSOLID), $porceSOLID, intval($infoTOTP), $porceTOTALP, intval($infoINDU), $porceINDU, intval($infoOTROS), $porceOTROS, intval($infoSERVM), $porceSERVM, intval($TOTALOT), $porceTOTALO, intval($infoTOTCOSV), $porceTOTCOSV, intval($infoTOTALBR), $porceTOTALBR]);
                array_push($acumPorceSinSu, [intval($infoACEITESP), intval($infoMARGARINASP), intval($infoSOLIDOS_CREMOSOSP), intval($TOTALPP), intval($infoINDUSTRIALESP), intval($infoOTROSP), intval($infoSERVICIO_MAQUILAP), intval($TOTALOP), intval($TOTALVP)]);
                array_push($acumSinSum, [$infoACEITES, $infoMarga, $infoSOLID, $infoTOTP, $infoINDU, $infoOTROS, $infoSERVM, $TOTALOT, $infoTOTCOSV, $infoTOTALBR]);
                array_push($mes, ['mes' => __($dateObject)]);
                $c++;
            }
        }
        array_push($mes, ['mes' => 'ACUMULADO']);
        array_push($mes, ['mes' => 'PROMEDIO']);

        //inicio de costo de ventas-- solo productos--
        $acumEnt = [];
        $promEnt = [];
        for ($i = 0; $i < count($acumSinSum[0]); $i++) {
            $suma = 0;
            foreach ($acumSinSum as $prom) {
                $suma += $prom[$i];
            }
            array_push($acumEnt, intval(round($suma)));
            array_push($promEnt, intval(round($suma / count($infoSales))));
        }
        //fin de la sumatoria
        //iniciop de sumatoria de ventas netas
        $acumPorc = [];
        $promPorc = [];
        for ($i = 0; $i < count($acumPorceSinSu[0]); $i++) {
            $suma = 0;
            foreach ($acumPorceSinSu as $prom) {
                $suma += $prom[$i];
            }
            array_push($acumPorc, intval(round($suma)));
            array_push($promPorc, intval(round($suma / count($infoSales))));
        }
        $acumulados = [];
        for ($i = 0; $i < count($acumPorceSinSu[0]); $i++) {
            $valorSum = $acumEnt[$i];
            $valorInd = $acumPorc[$i];
            array_push($acumulados, $valorSum);
            array_push($acumulados, round($valorSum / $valorInd, 2) . '%');
        }
        array_push($acumulados, intval($acumEnt[9]));
        array_push($acumulados, round($acumEnt[9] / $acumPorc[8], 2) . '%');
        array_push($formates, $acumulados);
        $promedios = [];
        for ($i = 0; $i < count($acumPorceSinSu[0]); $i++) {
            $valorSumP = $promEnt[$i];
            $valorIndP = $promPorc[$i];
            array_push($promedios, $valorSumP);
            array_push($promedios, round($valorSumP / $valorIndP, 2) . '%');
        }
        array_push($promedios, $promEnt[9]);
        array_push($promedios, round($promEnt[9] / $promPorc[8], 2) . '%');
        //fin de creacion del acumulador     
        array_push($formates, $promedios);
        return view('TotalCosts\list_total_costs', ['dates' => $formates, 'headers' => $headers, 'mes' => $mes, 'contador' => count($formates[0])]);
    }

    public function unit_sales_costs(Request $request)
    {
        if ($request->filter1 != null) {
            if($request->filter1 > $request->filter2){
                return redirect('admin/costU/unit')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoCosts = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoCosts = $infoCosts->toArray();
            $infoUnits = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoUnits = $infoUnits->toArray();
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoCosts = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoCosts = $infoCosts->toArray();
            $infoUnits = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoUnits = $infoUnits->toArray();
        }
        $headers = [
            'ACEITES', 'PORCENTAJE ACEITES', 'MARGARINAS', 'PORCENTAJE MARGARINAS', 'SOLIDOS Y CREMOSOS', 'PORCENTAJE SOLIDOS Y CREMOSOS', 'INDUSTRIALES', 'PORCENTAJE INDUSTRIALES',
            'OTROS PRODUCTOS', 'PORCENTAJE OTROS PRODUCTOS', 'SERVICIO DE MAQUILA', 'PORCENTAJE SERVICIO DE MAQUILA', 'TOTAL COSTOS DE VENTAS', 'PORCENTAJE TOTAL COSTOS DE VENTAS', 'UTILIDAD BRUTA', 'PORCENTAJE UTILIDAD BRUTA'
        ];
        $data1 = [];
        $meses = [];
        $sumaPrd = [];
        $sumaVn = [];
        $venNe = [];
        $ttven = [];
        foreach ($infoCosts as $info) {
            $aceites = round($info->ACEITES2, 5);
            $aceiteDiv = round($info->ACEITES, 5);
            $margarinas = round($info->MARGARINAS2, 5);
            $margarinasDiv = round($info->MARGARINAS, 5);
            $solidCrem = round($info->SOLIDOS_CREMOSOS2, 5);
            $solidCreDiv = round($info->SOLIDOS_CREMOSOS, 5);
            $industriales = round($info->INDUSTRIALES2, 5);
            $induastrialesDiv = round($info->INDUSTRIALES, 5);
            $otrosAcGr = round($info->ACIDOS_GRASOS_ACIDULADO2, 5);
            $otrosAcGrDiv = round($info->ACIDOS_GRASOS_ACIDULADO, 5);
            $serviciosMqu = round($info->SERVICIO_MAQUILA2, 5);
            $serviciosMaqDiv = round($info->SERVICIO_MAQUILA, 5);
            $TOTALOT = round($industriales + $otrosAcGr + $serviciosMqu);
            $infoTOTP = round($aceites + $margarinas + $solidCrem);
            $infoTOTCOSV = round($TOTALOT + $infoTOTP);
            $totPt = $aceites + $margarinas + $solidCrem;
            $totVent = $aceiteDiv + $margarinasDiv + $solidCreDiv + $induastrialesDiv + $otrosAcGrDiv + $serviciosMaqDiv;
            array_push($meses, DateTime::createFromFormat('m', $info->INF_D_MES)->format('F'));
            array_push($data1, [
                $aceites, $aceiteDiv, $margarinas, $margarinasDiv, $solidCrem, $solidCreDiv, $industriales,
                $induastrialesDiv, $otrosAcGr, $otrosAcGrDiv,
                $serviciosMqu, $serviciosMaqDiv, $infoTOTCOSV, $totPt, $totVent
            ]);
            array_push($sumaVn, [$serviciosMaqDiv, $totVent]);
            array_push($venNe, [$aceiteDiv, $margarinasDiv, $solidCreDiv, $induastrialesDiv, $otrosAcGrDiv, $serviciosMaqDiv]);
            array_push($sumaPrd, [$aceites, $margarinas, $solidCrem, $industriales, $otrosAcGr, $serviciosMqu, $infoTOTCOSV]);
            array_push($ttven, intval(round($totVent)));
        }

        $data2 = [];
        foreach ($infoUnits as $info) {
            $aceites2 = round($info->TON_ACEITES, 5);
            $margarinas2 = round($info->TON_MARGARINAS, 5);
            $solidCrem2 = round($info->TON_SOLIDOS_CREMOSOS, 5);
            $industriales2 = round($info->TON_INDUSTRIALES_OLEO, 5);
            $otrosAcGr2 = round($info->TON_ACIDOS_GRASOS_ACIDULADO, 5);
            $serviciosMaq2 = round($info->TON_SERVICIO_MAQUILA, 5);
            $totp = $aceites2 + $margarinas2 + $solidCrem2;
            $sumTt = $totp + $industriales2 + $otrosAcGr2;
            array_push($data2, [$aceites2, $margarinas2, $solidCrem2, $industriales2, $otrosAcGr2, $serviciosMaq2, $sumTt]);
        }
        $mes = [];
        $formates = [];
        $cosVenUnit = [];
        $c = 1;
        $m=0;
        for ($i = 0; $i < count($data2); $i++) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
                $aceiteF = $data1[$i][0] / $data2[$i][0];
                $divAceit1 = $data1[$i][1] / $data2[$i][0];
                $divMarga1 = $data1[$i][3] / $data2[$i][1];
                $divSolidCre1 = $data1[$i][5] / $data2[$i][2];
                $divIndustriales1 = $data1[$i][7] / $data2[$i][3];
                $divOtrosAcGr1 = $data1[$i][9] / $data2[$i][4];
                $divservMaq1 = $data1[$i][11] / $data2[$i][5];
                $porceAceiteF = $aceiteF * 100 / $divAceit1;
                $margaF = $data1[$i][2] / $data2[$i][1];
                $porceMargaF = round($margaF, 2) * 100 / $divMarga1;
                $solidCreF = $data1[$i][4] / $data2[$i][2];
                $porceSolidCreF = round($solidCreF, 2) * 100 / $divSolidCre1;
                $industrialesF = $data1[$i][6] / $data2[$i][3];
                $porceIndustrialesF = round($industrialesF, 2) * 100 / $divIndustriales1;
                $otrosAcGrF = $data1[$i][8] / $data2[$i][4];
                $porceOtrosAgF = round($otrosAcGrF, 2) * 100 / $divOtrosAcGr1;
                $servMaqF = $data1[$i][10] / $data2[$i][5];
                $porceservMaq = round($servMaqF, 2) * 100 / $divservMaq1;
                $ventTon = $data1[$i][12] / $data2[$i][6];
                $porceCosVen = round($ventTon, 2) * 100 / (($data1[$i][14] - $data1[$i][11]) / $data2[$i][6]);
                $utlBrut = ((round($data1[$i][14]) - round($data1[$i][11])) / round($data2[$i][6])) - $ventTon;
                $porceTotlBrut = intval($utlBrut) * 100 / intval(round(($data1[$i][14] - $data1[$i][11]) / $data2[$i][6]));
                array_push($formates, [
                    intval(round($aceiteF)), round($porceAceiteF, 2) . '%', intval(round($margaF)), round($porceMargaF, 2) . '%',
                    intval(round($solidCreF)), round($porceSolidCreF, 2) . '%', intval(round($industrialesF)), round($porceIndustrialesF, 2) . '%',
                    intval(round($otrosAcGrF)), round($porceOtrosAgF, 2) . '%', intval(round($servMaqF)), round($porceservMaq) . '%', intval(round($ventTon)),
                    round($porceCosVen, 2) . '%', intval(round($utlBrut)), round($porceTotlBrut, 2) . '%'
                ]);
                array_push($cosVenUnit, [
                    intval(round($aceiteF)), intval(round($margaF)),
                    intval(round($solidCreF)), intval(round($industrialesF)),
                    intval(round($otrosAcGrF)), intval(round($servMaqF))
                ]);
                if($m<count($meses)){
                    array_push($mes, ['mes' => $meses[$m]]);
                }
                array_push($mes, ['mes' => 'TRIMESTRE']);
                $m++;
                $c++;
                switch ($c) {
                    case $c == 4:
                        $sumCostVen=[];
                        for ($a = 0; $a < count($formates[0])-4; $a++) {
                            $suma = 0;
                            foreach ($formates as $prom) {
                                if($a%2==0){
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumCostVen, intval(round($suma / 3)));
                        }
                        $cuenCos= count($sumCostVen);
                        for($a=0;$a<$cuenCos;$a++){
                            if($a%2==0){
                            }else{
                                unset($sumCostVen[$a]);
                            }
                        }
                        $sumCostVen= array_values($sumCostVen);
                        $ventasNetasUnitTabla = $this->TablaVentasUnit($fechaIni, $fechaFin);
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 3, 1);
                        $contoVentasTabla= $this->TablaCostos($fechaIni, $fechaFin);
                        $contoVentasTabla = array_slice($contoVentasTabla, 3, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $ventasToneladasTabla = array_slice($ventasToneladasTabla, 3, 1);
                        $sumsFin=[];
                        for($a=0;$a<count($sumCostVen);$a++){
                            array_push($sumsFin,$sumCostVen[$a]);
                            array_push($sumsFin,round($sumCostVen[$a]*100/$ventasNetasUnitTabla[0][$a],2).'%');
                        }
                        $totCosVen=round($contoVentasTabla[0][16]/$ventasToneladasTabla[0][0]);
                        array_push($sumsFin,$totCosVen);
                        $porceTotCosVen= round($totCosVen*100/$ventasNetasUnitTabla[0][7]);
                        array_push($sumsFin,round($porceTotCosVen,2).'%');
                        $utilBrut= $totCosVen-$ventasNetasUnitTabla[0][7];
                        array_push($sumsFin,$utilBrut);
                        array_push($sumsFin,round($utilBrut/$ventasNetasUnitTabla[0][7],2).'%');
                        array_push($formates, $sumsFin);
                        break;
                    case $c == 8:
                        $sumCostVen=[];
                        for ($a = 0; $a < count($formates[0])-4; $a++) {
                            $suma = 0;
                            foreach ($formates as $prom) {
                                if($a%2==0){
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumCostVen, intval(round($suma / 3)));
                        }
                        $cuenCos= count($sumCostVen);
                        for($a=0;$a<$cuenCos;$a++){
                            if($a%2==0){
                            }else{
                                unset($sumCostVen[$a]);
                            }
                        }
                        $sumCostVen= array_values($sumCostVen);
                        $ventasNetasUnitTabla = $this->TablaVentasUnit($fechaIni, $fechaFin);
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 7, 1);
                        $contoVentasTabla= $this->TablaCostos($fechaIni, $fechaFin);
                        $contoVentasTabla = array_slice($contoVentasTabla, 7, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $ventasToneladasTabla = array_slice($ventasToneladasTabla, 7, 1);
                        $sumsFin=[];
                        for($a=0;$a<count($sumCostVen);$a++){
                            array_push($sumsFin,$sumCostVen[$a]);
                            array_push($sumsFin,round($sumCostVen[$a]*100/$ventasNetasUnitTabla[0][$a],2).'%');
                        }
                        $totCosVen=round($contoVentasTabla[0][16]/$ventasToneladasTabla[0][0]);
                        array_push($sumsFin,$totCosVen);
                        $porceTotCosVen= round($totCosVen*100/$ventasNetasUnitTabla[0][7]);
                        array_push($sumsFin,round($porceTotCosVen,2).'%');
                        $utilBrut= $totCosVen-$ventasNetasUnitTabla[0][7];
                        array_push($sumsFin,$utilBrut);
                        array_push($sumsFin,round($utilBrut/$ventasNetasUnitTabla[0][7],2).'%');
                        array_push($formates, $sumsFin);
                        break;
                    case $c == 12:
                        $sumCostVen=[];
                        for ($a = 0; $a < count($formates[0])-4; $a++) {
                            $suma = 0;
                            foreach ($formates as $prom) {
                                if($a%2==0){
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumCostVen, intval(round($suma / 3)));
                        }
                        $cuenCos= count($sumCostVen);
                        for($a=0;$a<$cuenCos;$a++){
                            if($a%2==0){
                            }else{
                                unset($sumCostVen[$a]);
                            }
                        }
                        $sumCostVen= array_values($sumCostVen);
                        $ventasNetasUnitTabla = $this->TablaVentasUnit($fechaIni, $fechaFin);
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 11, 1);
                        $contoVentasTabla= $this->TablaCostos($fechaIni, $fechaFin);
                        $contoVentasTabla = array_slice($contoVentasTabla, 11, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $ventasToneladasTabla = array_slice($ventasToneladasTabla, 11, 1);
                        $sumsFin=[];
                        for($a=0;$a<count($sumCostVen);$a++){
                            array_push($sumsFin,$sumCostVen[$a]);
                            array_push($sumsFin,round($sumCostVen[$a]*100/$ventasNetasUnitTabla[0][$a],2).'%');
                        }
                        $totCosVen=round($contoVentasTabla[0][16]/$ventasToneladasTabla[0][0]);
                        array_push($sumsFin,$totCosVen);
                        $porceTotCosVen= round($totCosVen*100/$ventasNetasUnitTabla[0][7]);
                        array_push($sumsFin,round($porceTotCosVen,2).'%');
                        $utilBrut= $totCosVen-$ventasNetasUnitTabla[0][7];
                        array_push($sumsFin,$utilBrut);
                        array_push($sumsFin,round($utilBrut/$ventasNetasUnitTabla[0][7],2).'%');
                        array_push($formates, $sumsFin);
                        break;
                    case $c == 16:
                        $sumCostVen=[];
                        for ($a = 0; $a < count($formates[0])-4; $a++) {
                            $suma = 0;
                            foreach ($formates as $prom) {
                                if($a%2==0){
                                    $suma += $prom[$a];
                                }
                            }
                            array_push($sumCostVen, intval(round($suma / 3)));
                        }
                        $cuenCos= count($sumCostVen);
                        for($a=0;$a<$cuenCos;$a++){
                            if($a%2==0){
                            }else{
                                unset($sumCostVen[$a]);
                            }
                        }
                        $sumCostVen= array_values($sumCostVen);
                        $ventasNetasUnitTabla = $this->TablaVentasUnit($fechaIni, $fechaFin);
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 15, 1);
                        $contoVentasTabla= $this->TablaCostos($fechaIni, $fechaFin);
                        $contoVentasTabla = array_slice($contoVentasTabla, 15, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $ventasToneladasTabla = array_slice($ventasToneladasTabla, 15, 1);
                        $sumsFin=[];
                        for($a=0;$a<count($sumCostVen);$a++){
                            array_push($sumsFin,$sumCostVen[$a]);
                            array_push($sumsFin,round($sumCostVen[$a]*100/$ventasNetasUnitTabla[0][$a],2).'%');
                        }
                        $totCosVen=round($contoVentasTabla[0][16]/$ventasToneladasTabla[0][0]);
                        array_push($sumsFin,$totCosVen);
                        $porceTotCosVen= round($totCosVen*100/$ventasNetasUnitTabla[0][7]);
                        array_push($sumsFin,round($porceTotCosVen,2).'%');
                        $utilBrut= $totCosVen-$ventasNetasUnitTabla[0][7];
                        array_push($sumsFin,$utilBrut);
                        array_push($sumsFin,round($utilBrut/$ventasNetasUnitTabla[0][7],2).'%');
                        array_push($formates, $sumsFin);
                        break;
                }
                $c++;
            } else {
                    $aceiteF = $data1[$i][0] / $data2[$i][0];
                    $divAceit1 = $data1[$i][1] / $data2[$i][0];
                    $divMarga1 = $data1[$i][3] / $data2[$i][1];
                    $divSolidCre1 = $data1[$i][5] / $data2[$i][2];
                    $divIndustriales1 = $data1[$i][7] / $data2[$i][3];
                    $divOtrosAcGr1 = $data1[$i][9] / $data2[$i][4];
                    $divservMaq1 = $data1[$i][11] / $data2[$i][5];
                    $porceAceiteF = $aceiteF * 100 / $divAceit1;
                    $margaF = $data1[$i][2] / $data2[$i][1];
                    $porceMargaF = round($margaF, 2) * 100 / $divMarga1;
                    $solidCreF = $data1[$i][4] / $data2[$i][2];
                    $porceSolidCreF = round($solidCreF, 2) * 100 / $divSolidCre1;
                    $industrialesF = $data1[$i][6] / $data2[$i][3];
                    $porceIndustrialesF = round($industrialesF, 2) * 100 / $divIndustriales1;
                    $otrosAcGrF = $data1[$i][8] / $data2[$i][4];
                    $porceOtrosAgF = round($otrosAcGrF, 2) * 100 / $divOtrosAcGr1;
                    $servMaqF = $data1[$i][10] / $data2[$i][5];
                    $porceservMaq = round($servMaqF, 2) * 100 / $divservMaq1;
                    $ventTon = $data1[$i][12] / $data2[$i][6];
                    $porceCosVen = round($ventTon, 2) * 100 / (($data1[$i][14] - $data1[$i][11]) / $data2[$i][6]);
                    $utlBrut = ((round($data1[$i][14]) - round($data1[$i][11])) / round($data2[$i][6])) - $ventTon;
                    $porceTotlBrut = intval($utlBrut) * 100 / intval(round(($data1[$i][14] - $data1[$i][11]) / $data2[$i][6]));
                    array_push($formates, [
                        intval(round($aceiteF)), round($porceAceiteF, 2) . '%', intval(round($margaF)), round($porceMargaF, 2) . '%',
                        intval(round($solidCreF)), round($porceSolidCreF, 2) . '%', intval(round($industrialesF)), round($porceIndustrialesF, 2) . '%',
                        intval(round($otrosAcGrF)), round($porceOtrosAgF, 2) . '%', intval(round($servMaqF)), round($porceservMaq) . '%', intval(round($ventTon)),
                        round($porceCosVen, 2) . '%', intval($utlBrut), round($porceTotlBrut, 2) . '%'
                    ]);
                    array_push($cosVenUnit, [
                        intval(round($aceiteF)), intval(round($margaF)),
                        intval(round($solidCreF)), intval(round($industrialesF)),
                        intval(round($otrosAcGrF)), intval(round($servMaqF))
                    ]);
                    if($m<count($meses)){
                        array_push($mes, ['mes' => $meses[$m]]);
                    }
                    $c++;
                    $m++;
            }
        }
        array_push($mes, ['mes' => 'ACUMULADO']);
        array_push($mes, ['mes' => 'PROMEDIO']);

        //sumatorias de tonelajes
        $promTonsTot = [];
        $acumTons = [];
        for ($i = 0; $i < count($data2[0]); $i++) {
            $suma = 0;
            foreach ($data2 as $ton) {
                $suma += $ton[$i];
            }
            array_push($acumTons, intval(round($suma)));
            array_push($promTonsTot, intval(round($suma / count($infoCosts))));
        }

        //sumatorias de productos
        $promCosVen = [];
        $acumCosVen = [];
        for ($i = 0; $i < count($sumaPrd[0]); $i++) {
            $suma = 0;
            foreach ($sumaPrd as $prod) {
                $suma += $prod[$i];
            }
            array_push($acumCosVen, intval(round($suma)));
            array_push($promCosVen, round($suma / count($infoCosts)));
        }
        $promVenNe = [];
        $acumVenNe = [];
        for ($i = 0; $i < count($venNe[0]); $i++) {
            $suma = 0;
            foreach ($venNe as $prod) {
                $suma += $prod[$i];
            }
            array_push($acumVenNe, intval(round($suma)));
            array_push($promVenNe, intval(round($suma / count($infoCosts))));
        }
        $promS15S13 = [];
        $acumS15S13 = [];
        for ($i = 0; $i < count($sumaVn[0]); $i++) {
            $suma = 0;
            foreach ($sumaVn as $prod) {
                $suma += $prod[$i];
            }
            array_push($acumS15S13, intval(round($suma)));
            array_push($promS15S13, intval(round($suma / count($infoCosts))));
        }
        $restaS15S13 =  round($acumS15S13[1] - $acumS15S13[0]);

        $acumVenNetUnit = [];
        for ($i = 0; $i < count($acumVenNe); $i++) {
            array_push($acumVenNetUnit, intval(round($acumVenNe[$i] / $acumTons[$i])));
        }
        array_push($acumVenNetUnit, intval(round($restaS15S13 / $acumTons[6])));

        $acumulados = [];
        for ($i = 0; $i < count($acumCosVen); $i++) {
            $acuM = intval(round($acumCosVen[$i] / $acumTons[$i]));
            $porceM = round($acuM / $acumVenNetUnit[$i], 2) . '%';
            array_push($acumulados, $acuM);
            array_push($acumulados, $porceM);
        }
        array_push($acumulados, $acumulados[6] - $acumVenNetUnit[6]);
        array_push($acumulados, round($acumulados[14] / $acumVenNetUnit[6], 2) . '%');

        $promVenNetUnit = [];
        for ($i = 0; $i < count($promVenNe); $i++) {
            array_push($promVenNetUnit, intval(round($promVenNe[$i] / $promTonsTot[$i])));
        }
        $promedios = [];
        for ($i = 0; $i < count($formates[0]); $i++) {
            $suma = 0;
            foreach ($formates as $prod) {
                if ($i % 2 == 0) {
                    $suma += round($prod[$i] / count($infoCosts));
                };
            }
            if ($i % 2 == 0) {
                $a = 0;
                array_push($promedios, intval($suma));
                array_push($promedios, round($suma / $promVenNetUnit[$a], 2) . '%');
                $a++;
            };
        }
        $suma = 0;
        for ($i = 0; $i < count($ttven); $i++) {
            $suma += $ttven[$i];
            $promVenNetT = intval(round($suma / count($infoCosts)));
        }
        $resPromVenNe = $promVenNetT - $promVenNe[5];
        $t127 = intval(round($promCosVen[6] / $promTonsTot[6]));
        $t113 = intval(round($resPromVenNe / $promTonsTot[6]));
        $t129 = intval(round($t113 - $t127));
        array_push($promedios, $t127);
        array_push($promedios, $t127 / $t113);
        array_push($promedios, $t129);
        array_push($promedios, $t129 / $t113);
        array_push($formates, $acumulados);
        array_push($formates, $promedios);
        $form = 0;
        foreach ($formates as $form) {
            $form = count($form);
        }
        return view('TotalCosts\list_total_costs_unit', ['dates' => $formates, 'headers' => $headers, 'mes' => $mes, 'contador' => $form]);
    }
}
