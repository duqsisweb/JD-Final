<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Http\Traits\VentasNetasTrait;
use App\Http\Traits\VentasToneladasTrait;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VentasNetasController extends Controller
{

    use VentasNetasTrait;
    use VentasToneladasTrait;
    public function total_sales(Request $request)
    {
        try {

            if ($request->filter1 != null) {
                if($request->filter1 > $request->filter2){
                    return redirect('admin/ventas/totales')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
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
            $formates = [];
            $cabeceras = ['ACEITES', 'MARGARINAS', 'SOLIDOS_CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'OTROS(AGL-ACIDULADO)', 'SERVICIO DE MAQUILA', 'TOTAL OTROS', 'TOTAL VENTAS'];

            $mes = [];
            $c = 1;
            foreach ($infoSales as $info) {

                if ($c == 3 || $c == 6 || $c == 9 || $c == 12) {
                    $dateObject = DateTime::createFromFormat('!m', $info->INF_D_MES)->format('F');
                    $infoACEITES =  round($info->ACEITES, 5);
                    $infoMARGARINAS =  round($info->MARGARINAS, 5);
                    $infoSOLIDOS_CREMOSOS =  round($info->SOLIDOS_CREMOSOS, 5);
                    $infoINDUSTRIALES =  round($info->INDUSTRIALES, 5);
                    $infoOTROS =  round($info->ACIDOS_GRASOS_ACIDULADO, 5);
                    $infoSERVICIO_MAQUILA =  round($info->SERVICIO_MAQUILA, 5);
                    $TOTALP = $infoACEITES + $infoMARGARINAS + $infoSOLIDOS_CREMOSOS;
                    $TOTALO = $infoINDUSTRIALES + $infoOTROS + $infoSERVICIO_MAQUILA;
                    $TOTALV = $TOTALP + $TOTALO;
                    array_push($formates, [$infoACEITES, $infoMARGARINAS, intval(round($infoSOLIDOS_CREMOSOS)), $TOTALP, intval(round($infoINDUSTRIALES)), intval(round($infoOTROS)), intval(round($infoSERVICIO_MAQUILA)), $TOTALO, $TOTALV]);
                    array_push($mes, ['mes' => __($dateObject)]);
                    array_push($mes, ['mes' => 'TRIMESTRE']);
                    $formEdit = $formates;
                    switch ($c) {
                        case $c <= 3:
                            $formEdit = $formates;
                            $sumaProm = [];
                            for ($i = 0; $i < count($formEdit[0]); $i++) {
                                $suma = 0;
                                foreach ($formEdit as $prom) {
                                    $suma += $prom[$i];
                                }
                                array_push($sumaProm, intval(round($suma / 3)));
                            }
                            array_push($formates, $sumaProm);
                            $c++;
                            break;
                        case $c > 3 && $c <= 7:
                            $formEdit1 = $formates;
                            unset($formEdit1[0], $formEdit1[1], $formEdit1[2], $formEdit1[3]);
                            $sumaProm = [];
                            for ($i = 0; $i < count($formEdit1[4]); $i++) {
                                $suma = 0;
                                foreach ($formEdit1 as $prom) {
                                    $suma += $prom[$i];
                                }
                                array_push($sumaProm, intval(round($suma / 3)));
                            }
                            array_push($formates, $sumaProm);
                            $c++;
                            break;
                        case $c > 7 && $c <= 11:
                            $formEdit2 = $formates;
                            unset($formEdit2[0], $formEdit2[1], $formEdit2[2], $formEdit2[3], $formEdit2[4], $formEdit2[5], $formEdit2[6], $formEdit2[7]);
                            $sumaProm = [];
                            for ($i = 0; $i < count($formEdit2[8]); $i++) {
                                $suma = 0;
                                foreach ($formEdit2 as $prom) {
                                    $suma += $prom[$i];
                                }
                                array_push($sumaProm, intval(round($suma / 3)));
                            }
                            array_push($formates, $sumaProm);
                            $c++;
                            break;
                        case $c > 11:
                            $formEdit3 = $formates;
                            unset($formEdit3[0], $formEdit3[1], $formEdit3[2], $formEdit3[3], $formEdit3[4], $formEdit3[5], $formEdit3[6], $formEdit3[7], $formEdit3[8], $formEdit3[9], $formEdit3[10]);
                            $sumaProm = [];
                            for ($i = 0; $i < count($formEdit3[11]); $i++) {
                                $suma = 0;
                                foreach ($formEdit3 as $prom) {
                                    $suma += $prom[$i];
                                }
                                array_push($sumaProm, intval(round($suma / 3)));
                            }
                            array_push($formates, $sumaProm);
                            $c++;
                            break;
                    }
                } else {
                    $dateObject = DateTime::createFromFormat('!m', $info->INF_D_MES)->format('F');
                    $infoACEITES =  round($info->ACEITES, 5);
                    $infoMARGARINAS =  round($info->MARGARINAS, 5);
                    $infoSOLIDOS_CREMOSOS =  round($info->SOLIDOS_CREMOSOS, 5);
                    $infoINDUSTRIALES =  round($info->INDUSTRIALES, 5);
                    $infoOTROS =  round($info->ACIDOS_GRASOS_ACIDULADO, 5);
                    $infoSERVICIO_MAQUILA =  round($info->SERVICIO_MAQUILA, 5);
                    $TOTALP = $infoACEITES + $infoMARGARINAS + $infoSOLIDOS_CREMOSOS;
                    $TOTALO = $infoINDUSTRIALES + $infoOTROS + $infoSERVICIO_MAQUILA;
                    $TOTALV = $TOTALP + $TOTALO;
                    array_push($formates, [$infoACEITES, $infoMARGARINAS, intval(round($infoSOLIDOS_CREMOSOS)), $TOTALP, intval(round($infoINDUSTRIALES)), intval(round($infoOTROS)), intval(round($infoSERVICIO_MAQUILA)), $TOTALO, $TOTALV]);
                    array_push($mes, ['mes' => __($dateObject)]);
                    $c++;
                }
            }
            array_push($mes, ['mes' => 'ACUMULADO']);
            array_push($mes, ['mes' => 'PROMEDIO']);
            $prmediosOperados = [];
            foreach ($infoSales as $promedio) {
                $infoACEITESP =  round($promedio->ACEITES, 5);
                $infoMARGARINASP =  round($promedio->MARGARINAS, 5);
                $infoSOLIDOS_CREMOSOSP =  round($promedio->SOLIDOS_CREMOSOS, 5);
                $infoINDUSTRIALESP =  round($promedio->INDUSTRIALES, 5);
                $infoOTROSP =  round($promedio->ACIDOS_GRASOS_ACIDULADO, 5);
                $infoSERVICIO_MAQUILAP =  round($promedio->SERVICIO_MAQUILA, 5);
                $TOTALPP = $infoACEITESP + $infoMARGARINASP + $infoSOLIDOS_CREMOSOSP;
                $TOTALOP = $infoINDUSTRIALESP + $infoOTROSP + $infoSERVICIO_MAQUILAP;
                $TOTALVP = $TOTALPP + $TOTALOP;
                array_push($prmediosOperados, [$infoACEITESP, $infoMARGARINASP, $infoSOLIDOS_CREMOSOSP, $TOTALPP, $infoINDUSTRIALESP, $infoOTROSP, $infoSERVICIO_MAQUILAP, $TOTALOP, $TOTALVP]);
            }
            $promedios = [];
            $sumatorias = [];
            for ($i = 0; $i < count($prmediosOperados[0]); $i++) {
                $suma = 0;
                foreach ($prmediosOperados as $prom) {
                    $suma += $prom[$i];
                }
                array_push($sumatorias, [intval(round($suma))]);
                array_push($promedios, [intval(round($suma / count($infoSales)))]);
            }
            array_push($formates, $sumatorias);
            array_push($formates, $promedios);

            return view('SalesTotal/list_sales_total', ['dates' => $formates, 'headers' => $cabeceras, 'mes' => $mes, 'contador' => count($formates[0])]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function unit_sales(Request $request)
    {
        if ($request->filter1 != null) {
            if($request->filter1 > $request->filter2){
                return redirect('admin/ventas/TotUnit')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoSales = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoSales = $infoSales->toArray();
            $infoTons = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTons = $infoTons->toArray();
        } else {
            $infoSales = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoSales = $infoSales->toArray();
            $infoTons = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTons = $infoTons->toArray();
            $fechaIni = null;
            $fechaFin = null;
        }
        $headers = [
            'ACEITES', 'MARGARINAS', 'SOLIDOS Y CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'OTROS (AGL-ACIDULADO)', 'SERVICIO DE MAQUILA', 'TOTAL VENTAS'
        ];

        $meses = [];
        $infos = [];
        $infoTs = [];
        $dates = [];
        //informacion de vista ventas netas
        foreach ($infoSales as $infoD) {
            $aceiteUnit = round($infoD->ACEITES, 5);
            $margaUnit = round($infoD->MARGARINAS, 5);
            $solCrUnit = round($infoD->SOLIDOS_CREMOSOS, 5);
            $totlPUnit = $aceiteUnit + $margaUnit + $solCrUnit;
            $indusUnit = round($infoD->INDUSTRIALES, 5);
            $aglAcid = round($infoD->ACIDOS_GRASOS_ACIDULADO, 5);
            $servMaq = round($infoD->SERVICIO_MAQUILA, 5);
            $totTBL = round(($totlPUnit + $indusUnit + $aglAcid + $servMaq) - $servMaq);
            $dateObject = DateTime::createFromFormat('!m', $infoD->INF_D_MES)->format('F');
            array_push($infos, [$aceiteUnit, $margaUnit, $solCrUnit, $totlPUnit, $indusUnit, $aglAcid, $servMaq, $totTBL, __($dateObject)]);
        }
        foreach ($infoTons as $infoT) {
            $aceiUnit2 = round($infoT->TON_ACEITES, 5);
            $margaUnit2 = round($infoT->TON_MARGARINAS, 5);
            $solCrUnit2 = round($infoT->TON_SOLIDOS_CREMOSOS, 5);
            $totlPTUnit2 = $aceiUnit2 + $margaUnit2 + $solCrUnit2;
            $indusUnit2 = round($infoT->TON_INDUSTRIALES_OLEO, 5);
            $agAcid2 = round($infoT->TON_ACIDOS_GRASOS_ACIDULADO, 5);
            $servMaq2 = round($infoT->TON_SERVICIO_MAQUILA, 5);
            $totlVen2 = $totlPTUnit2 + $infoT->TON_INDUSTRIALES_OLEO + $infoT->TON_ACIDOS_GRASOS_ACIDULADO;
            array_push($infoTs, [$aceiUnit2, $margaUnit2, $solCrUnit2, $totlPTUnit2, $indusUnit2, $agAcid2, $servMaq2, $totlVen2]);
        }
        $c = 1;
        for ($m = 0; $m < count($infoTs); $m++) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
                $divAceit = $infos[$m][0] / $infoTs[$m][0];
                $divMarga = $infos[$m][1] / $infoTs[$m][1];
                $divSolCr = $infos[$m][2] / $infoTs[$m][2];
                $divTotlPt = $infos[$m][3] / $infoTs[$m][3];
                $divIndus = $infos[$m][4] / $infoTs[$m][4];
                $divAgAc = $infos[$m][5] / $infoTs[$m][5];
                $divServMaq = $infos[$m][6] / $infoTs[$m][6];
                $divTotlVen = $infos[$m][7] / $infoTs[$m][7];
                $mes = $infos[$m][8];
                array_push($dates, [
                    intval($divAceit), intval(round($divMarga)), intval(round($divSolCr)), intval(round($divTotlPt)),
                    intval(round($divIndus)), intval(round($divAgAc)), intval(round($divServMaq)), intval(round($divTotlVen))
                ]);
                array_push($meses, ['mes' => $mes]);
                array_push($meses, ['mes' => 'TRIMESTRE']);
                switch ($c) {
                    case $c <= 4:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 3, 1);

                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }
                        $toneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $toneladasTabla = array_slice($toneladasTabla, 3, 1);
                        $sumaTons = [];
                        for ($i = 0; $i < count($toneladasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($toneladasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaTons, intval(round($suma / 3)));
                        }
                        $sumfinals = [];
                        $h = 1;
                        for ($i = 0; $i < count($sumaTons) - 1; $i++) {
                            array_push($sumfinals, intval(round($sumaVentas[$i] / $sumaTons[$h])));
                            $h++;
                        }
                        array_push($sumfinals, intval(round(($sumaVentas[8] - $sumaVentas[6]) / $sumaTons[0])));
                        array_push($dates, $sumfinals);
                        $c++;
                        break;
                    case $c > 4 && $c <= 8:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 7, 1);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }

                        $toneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $toneladasTabla = array_slice($toneladasTabla, 7, 1);
                        $sumaTons = [];
                        for ($i = 0; $i < count($toneladasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($toneladasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaTons, intval(round($suma / 3)));
                        }

                        $sumfinals = [];
                        $h = 1;
                        for ($i = 0; $i < count($sumaTons) - 1; $i++) {
                            array_push($sumfinals, intval(round($sumaVentas[$i] / $sumaTons[$h])));
                            $h++;
                        }
                        array_push($sumfinals, intval(round(($sumaVentas[8] - $sumaVentas[6]) / $sumaTons[0])));
                        array_push($dates, $sumfinals);
                        $c++;
                        break;
                    case $c > 7 && $c <= 11:

                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 11, 1);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }

                        $toneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $toneladasTabla = array_slice($toneladasTabla, 11, 1);
                        $sumaTons = [];
                        for ($i = 0; $i < count($toneladasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($toneladasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaTons, intval(round($suma / 3)));
                        }

                        $sumfinals = [];
                        $h = 1;
                        for ($i = 0; $i < count($sumaTons) - 1; $i++) {
                            array_push($sumfinals, intval(round($sumaVentas[$i] / $sumaTons[$h])));
                            $h++;
                        }
                        array_push($sumfinals, intval(round(($sumaVentas[8] - $sumaVentas[6]) / $sumaTons[0])));
                        array_push($dates, $sumfinals);
                        $c++;
                        break;
                    case $c > 11:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 15, 1);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }

                        $toneladasTabla = $this->TablaVentasToneladas($fechaIni, $fechaFin);
                        $toneladasTabla = array_slice($toneladasTabla, 15, 1);
                        $sumaTons = [];
                        for ($i = 0; $i < count($toneladasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($toneladasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaTons, intval(round($suma / 3)));
                        }

                        $sumfinals = [];
                        $h = 1;
                        for ($i = 0; $i < count($sumaTons) - 1; $i++) {
                            array_push($sumfinals, intval(round($sumaVentas[$i] / $sumaTons[$h])));
                            $h++;
                        }
                        array_push($sumfinals, intval(round(($sumaVentas[8] - $sumaVentas[6]) / $sumaTons[0])));
                        array_push($dates, $sumfinals);
                        $c++;
                        break;
                }
                $c++;
            } else {
                $divAceit = $infos[$m][0] / $infoTs[$m][0];
                $divMarga = $infos[$m][1] / $infoTs[$m][1];
                $divSolCr = $infos[$m][2] / $infoTs[$m][2];
                $divTotlPt = $infos[$m][3] / $infoTs[$m][3];
                $divIndus = $infos[$m][4] / $infoTs[$m][4];
                $divAgAc = $infos[$m][5] / $infoTs[$m][5];
                $divServMaq = $infos[$m][6] / $infoTs[$m][6];
                $divTotlVen = $infos[$m][7] / $infoTs[$m][7];
                $mes = $infos[$m][8];
                array_push($dates, [
                    intval($divAceit), intval(round($divMarga)), intval(round($divSolCr)), intval(round($divTotlPt)),
                    intval(round($divIndus)), intval(round($divAgAc)), intval(round($divServMaq)), intval(round($divTotlVen))
                ]);
                array_push($meses, ['mes' => $mes]);
                $c++;
            }
        }



        array_push($meses, ['mes' => 'ACUMULADO']);
        array_push($meses, ['mes' => 'PROMEDIO']);
        $sumatoriasV = [];
        foreach ($infoSales as $sums) {
            $aceites =  round($sums->ACEITES, 5);
            $margarinas =  round($sums->MARGARINAS, 5);
            $solidCre =  round($sums->SOLIDOS_CREMOSOS, 5);
            $totPrTer = $aceites + $margarinas + $solidCre;
            $industriales =  round($sums->INDUSTRIALES, 5);
            $otros =  round($sums->ACIDOS_GRASOS_ACIDULADO, 5);
            $serviMaqui =  round($sums->SERVICIO_MAQUILA, 5);
            $totVen = $totPrTer + $industriales + $otros + $serviMaqui;
            array_push($sumatoriasV, [$aceites, $margarinas, $solidCre, $totPrTer, $industriales, $otros, $serviMaqui, $totVen]);
        }
        $sumatoriasT = [];
        foreach ($infoTons as $sumst) {
            $aceites =  round($sumst->TON_ACEITES, 5);
            $margarinas =  round($sumst->TON_MARGARINAS, 5);
            $solidCre =  round($sumst->TON_SOLIDOS_CREMOSOS, 5);
            $totPt = $aceites + $margarinas + $solidCre;
            $industriales =  round($sumst->TON_INDUSTRIALES_OLEO, 5);
            $otros =  round($sumst->TON_ACIDOS_GRASOS_ACIDULADO, 5);
            $serviMaqui =  round($sumst->TON_SERVICIO_MAQUILA, 5);
            $venTon = $totPt + $industriales + $otros;
            array_push($sumatoriasT, [$aceites, $margarinas, $solidCre, $totPt, $industriales, $otros, $serviMaqui, $venTon]);
        }
        $sumsV = [];
        $promV = [];
        for ($i = 0; $i < (count($sumatoriasV[0])); $i++) {
            $suma = 0;
            foreach ($sumatoriasV as $sum) {
                $suma += $sum[$i];
            }
            $sumaV = $suma;
            array_push($sumsV, intval(round($sumaV)));
            array_push($promV, intval(round($sumaV)) / count($infoSales));
        };

        $sumsT = [];
        $promT = [];
        for ($i = 0; $i < count($sumatoriasT[0]); $i++) {
            $suma = 0;
            foreach ($sumatoriasT as $sum) {
                $suma += $sum[$i];
            }
            $sumaT = $suma;
            array_push($sumsT, intval(round($sumaT)));
            array_push($promT, intval(round($sumaT)) / count($infoSales));
        };


        $acumulado = [];
        for ($i = 0; $i < (count($sumsT) - 1); $i++) {
            array_push($acumulado, $sumsV[$i] / $sumsT[$i]);
        }
        array_push($acumulado, (intval(round($sumsV[7] - $sumsV[6]) / $sumsT[7])));
        array_push($dates, $acumulado);

        $promedio = [];
        for ($i = 0; $i < (count($promV) - 1); $i++) {
            array_push($promedio, intval(round($promV[$i] / $promT[$i])));
        }
        array_push($promedio, (intval(round($promV[7] - $promV[6]) / $promT[7])));
        array_push($dates, $promedio);

        return view('SalesTotal\list_total_sales_unit', ['headers' => $headers, 'dates' => $dates, 'mes' => $meses, 'contador' => count($dates[0])]);
    }

    
}
