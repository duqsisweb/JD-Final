<?php

namespace App\Http\Traits;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait VentasNetasUnitTraitC
{

    use VentasNetasTrait;
    use VentasNetasTraitC;
    use VentasToneladasTraitC;

    public function TablaVentasUnitC()
    {
        $infoSales = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
        $infoSales = $infoSales->toArray();
        $infoTons = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->orderBy('INF_D_FECHAS', 'asc')->get();
        $infoTons = $infoTons->toArray();
        
        $headers = [
            'ACEITES', 'MARGARINAS', 'SOLIDOS Y CREMOSOS', 'TOTAL PRODUCTO TERMINADO', 'INDUSTRIALES', 'OTROS (AGL-ACIDULADO)', 'SERVICIO DE MAQUILA', 'TOTAL VENTAS'
        ];

        $meses = [];
        $infos = [];
        $infoTs = [];
        $dates = [];
        //informacion de vista ventas netas
        foreach ($infoSales as $infoD) {
            $aceiteUnit = $infoD->ACEITES;
            $margaUnit = $infoD->MARGARINAS;
            $solCrUnit = $infoD->SOLIDOS_CREMOSOS;
            $totlPUnit = $aceiteUnit + $margaUnit + $solCrUnit;
            $indusUnit = $infoD->INDUSTRIALES;
            $aglAcid = $infoD->ACIDOS_GRASOS_ACIDULADO;
            $servMaq = $infoD->SERVICIO_MAQUILA;
            $totTBL = ($totlPUnit + $indusUnit + $aglAcid + $servMaq) - $servMaq;
            $dateObject = DateTime::createFromFormat('m', $infoD->INF_D_MES)->format('F');
            array_push($infos, [$aceiteUnit, $margaUnit, $solCrUnit, $totlPUnit, $indusUnit, $aglAcid, $servMaq, $totTBL, $dateObject]);
        }
        foreach ($infoTons as $infoT) {
            $aceiUnit2 = $infoT->TON_ACEITES;
            $margaUnit2 = $infoT->TON_MARGARINAS;
            $solCrUnit2 = $infoT->TON_SOLIDOS_CREMOSOS;
            $totlPTUnit2 = $aceiUnit2 + $margaUnit2 + $solCrUnit2;
            $indusUnit2 = $infoT->TON_INDUSTRIALES_OLEO;
            $agAcid2 = $infoT->TON_ACIDOS_GRASOS_ACIDULADO;
            $servMaq2 = $infoT->TON_SERVICIO_MAQUILA;
            $totlVen2 = $totlPTUnit2 + $infoT->TON_INDUSTRIALES_OLEO + $infoT->TON_ACIDOS_GRASOS_ACIDULADO;
            array_push($infoTs, [$aceiUnit2, $margaUnit2, $solCrUnit2, $totlPTUnit2, $indusUnit2, $agAcid2, $servMaq2, $totlVen2]);
        }
        //fin de informacion
        //contador de posiciones en arreglo de registros

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
                    $divAceit, $divMarga, $divSolCr, $divTotlPt,
                    $divIndus, $divAgAc, $divServMaq, $divTotlVen
                ]);
                $c++;
                array_push($meses, ['mes' => $mes]);
                array_push($meses, ['mes' => 'TRIMESTRE']);
                
                
                switch ($c) {
                    case $c == 4:
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 3, 1);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 3, 1);
                        $trimestre = [];
                        $t=1;
                        for ($i = 0; $i < count($ventasNetasTabla[0]) - 2; $i++) {
                            array_push($trimestre, intval(round($ventasNetasTabla[0][$i] / $toneladasTabla[0][$t])));
                            $t++;
                        }
                        array_push($trimestre, intval(round(($ventasNetasTabla[0][8] - $ventasNetasTabla[0][6]) / $toneladasTabla[0][0])));
                        array_push($dates, $trimestre);
                        $c++;
                        break;
                    case $c == 8:
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 7, 1);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 7, 1);
                        $trimestre = [];
                        $t=1;
                        for ($i = 0; $i < count($ventasNetasTabla[0]) - 2; $i++) {
                            array_push($trimestre, intval(round($ventasNetasTabla[0][$i] / $toneladasTabla[0][$t])));
                            $t++;
                        }
                        array_push($trimestre, intval(round(($ventasNetasTabla[0][8] - $ventasNetasTabla[0][6]) / $toneladasTabla[0][0])));
                        array_push($dates, $trimestre);
                        $c++;
                        break;
                    case $c == 12:
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 11, 1);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 11, 1);
                        $trimestre = [];
                        $t=1;
                        for ($i = 0; $i < count($ventasNetasTabla[0]) - 2; $i++) {
                            array_push($trimestre, intval(round($ventasNetasTabla[0][$i] / $toneladasTabla[0][$t])));
                            $t++;
                        }
                        array_push($trimestre, intval(round(($ventasNetasTabla[0][8] - $ventasNetasTabla[0][6]) / $toneladasTabla[0][0])));
                        array_push($dates, $trimestre);
                        $c++;
                        break;
                    case $c == 16:
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 15, 1);
                        $toneladasTabla = $this->TablaVentasToneladasC();
                        $toneladasTabla = array_slice($toneladasTabla, 15, 1);
                        $trimestre = [];
                        $t=1;
                        for ($i = 0; $i < count($ventasNetasTabla[0]) - 2; $i++) {
                            array_push($trimestre, intval(round($ventasNetasTabla[0][$i] / $toneladasTabla[0][$t])));
                            $t++;
                        }
                        array_push($trimestre, intval(round(($ventasNetasTabla[0][8] - $ventasNetasTabla[0][6]) / $toneladasTabla[0][0])));
                        array_push($dates, $trimestre);
                        $c++;
                        break;
                }
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
                    $divAceit, $divMarga, $divSolCr, $divTotlPt,
                    $divIndus, $divAgAc, $divServMaq, $divTotlVen
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

        return $dates;
    }
}
