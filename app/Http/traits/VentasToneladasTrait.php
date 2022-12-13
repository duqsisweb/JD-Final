<?php

namespace App\Http\Traits;

use DateTime;
use Illuminate\Support\Facades\DB;

trait VentasToneladasTrait
{

    public function TablaVentasToneladas($fechaIni, $fechaFin)
    {

        if ($fechaIni != null) {
            $fechaIni = $fechaIni;
            $fechaFin = $fechaFin;
            $infoTons = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTons = $infoTons->toArray();
        } else {
            $fechaIni = null;
            $fechaFin = null;
            $infoTons = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ2')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoTons = $infoTons->toArray();
        }
        $headers = [
            'VENTAS (TONELADAS)', 'ACEITES TONELADAS', 'MARGARINAS TONELADAS', 'SOLIDOS Y CREMOSOS TONELADAS', 'TOTAL PT', 'INDUSTRIALES (OLEOQUIMICOS)',
            'OTROS (AGL-ACIDULADO)', 'SERVICIO MAQUILA'
        ];
        $fomDates = [];
        $mes = [];

        $c = 1;
        foreach ($infoTons as $info) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
                $dateObject = DateTime::createFromFormat('m', $info->INF_D_MES)->format('F');
                $aceites = $info->TON_ACEITES;
                $margarinas = $info->TON_MARGARINAS;
                $soliCrem = $info->TON_SOLIDOS_CREMOSOS;
                $totPt = $aceites + $margarinas + $soliCrem;
                $tonIndOl = $info->TON_INDUSTRIALES_OLEO;
                $acGrAccid = $info->TON_ACIDOS_GRASOS_ACIDULADO;
                $servMaqu = $info->TON_SERVICIO_MAQUILA;
                $ventTon = $totPt + $tonIndOl + $acGrAccid;
                array_push($fomDates, [
                    $ventTon, $aceites, $margarinas, $soliCrem, $totPt, $tonIndOl, $acGrAccid,
                    $servMaqu
                ]);
                $c++;
                array_push($mes, ['mes' => $dateObject]);
                array_push($mes, ['mes' => 'TRIMESTRE']);
                $formEdit = $fomDates;
                switch ($c) {
                    case $c == 4:
                        $formEdit = $fomDates;
                        $sumaProm = [];
                        for ($i = 0; $i < count($formEdit[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit as $prom) {
                                $suma += round($prom[$i]);
                            }
                            array_push($sumaProm, $suma / 3);
                        }
                        array_push($fomDates, $sumaProm);
                        $c++;
                        break;
                    case $c == 8:
                        $formEdit1 = array_slice($fomDates, 4, 3);
                        $sumaProm = [];
                        for ($i = 0; $i < count($formEdit1[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit1 as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaProm, $suma / 3);
                        }
                        array_push($fomDates, $sumaProm);
                        $c++;
                        break;
                    case $c == 12:
                        $formEdit2 = array_slice($fomDates, 8, 3);
                        $sumaProm = [];
                        for ($i = 0; $i < count($formEdit2[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit2 as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaProm, $suma / 3);
                        }
                        array_push($fomDates, $sumaProm);
                        $c++;
                        break;
                    case $c == 16:
                        $formEdit3 = array_slice($fomDates, 12, 3);
                        $sumaProm = [];
                        for ($i = 0; $i < count($formEdit3[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit3 as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaProm, $suma / 3);
                        }
                        array_push($fomDates, $sumaProm);
                        $c++;
                        break;
                }
                
            } else {
                $dateObject = DateTime::createFromFormat('m', $info->INF_D_MES)->format('F');
                $aceites = $info->TON_ACEITES;
                $margarinas = $info->TON_MARGARINAS;
                $soliCrem = $info->TON_SOLIDOS_CREMOSOS;
                $totPt = $aceites + $margarinas + $soliCrem;
                $tonIndOl = $info->TON_INDUSTRIALES_OLEO;
                $acGrAccid = $info->TON_ACIDOS_GRASOS_ACIDULADO;
                $servMaqu = $info->TON_SERVICIO_MAQUILA;
                $ventTon = $totPt + $tonIndOl + $acGrAccid;
                array_push($fomDates, [
                    $ventTon, $aceites, $margarinas, $soliCrem, $totPt, $tonIndOl, $acGrAccid,
                    $servMaqu
                ]);
                array_push($mes, ['mes' => $dateObject]);
                $c++;
            }
        }
        array_push($mes, ['mes' => 'ACUMULADO']);
        array_push($mes, ['mes' => 'PROMEDIO']);
        $sumados = [];
        foreach ($infoTons as $ton) {
            $aceitesT = round($ton->TON_ACEITES, 5);
            $margarinasT = round($ton->TON_MARGARINAS, 5);
            $soliCremT = round($ton->TON_SOLIDOS_CREMOSOS, 5);
            $tonIndOlT = round($ton->TON_INDUSTRIALES_OLEO, 5);
            $acGrAccidT = round($ton->TON_ACIDOS_GRASOS_ACIDULADO, 5);
            $servMaquT = round($ton->TON_SERVICIO_MAQUILA, 5);
            $ventt = $aceitesT + $margarinasT + $soliCremT;
            $ttot = $ventt + $tonIndOlT + $acGrAccidT;
            array_push($sumados, [$aceitesT, $margarinasT, $soliCremT, $tonIndOlT, $acGrAccidT, $servMaquT, $ttot, $ventt]);
        }

        $sumatorias = [];
        $promedios = [];
        for ($i = 0; $i < count($sumados[0]); $i++) {
            $suma = 0;
            foreach ($sumados as $sum) {
                $suma += $sum[$i];
            }
            array_push($sumatorias, intval(round($suma)));
            array_push($promedios, intval(round($suma / count($infoTons))));
        }

        $acumulados = [];
        $totalPTT =  $sumatorias[0] + $sumatorias[1] + $sumatorias[2];
        $ventTonels =  $totalPTT + $sumatorias[3] + $sumatorias[4];
        array_push($acumulados, intval($sumatorias[6]));
        array_push($acumulados, intval($sumatorias[0]));
        array_push($acumulados, intval($sumatorias[1]));
        array_push($acumulados, intval($sumatorias[2]));
        array_push($acumulados, intval($sumatorias[7]));
        array_push($acumulados, intval($sumatorias[3]));
        array_push($acumulados, intval($sumatorias[4]));
        array_push($acumulados, intval($sumatorias[5]));
        array_push($fomDates, $acumulados);

        $promedios=[];
        foreach($acumulados as $ac){
            array_push($promedios, intval(round($ac / count($infoTons))));
        }
        
        array_push($fomDates, $promedios);
        
        return $fomDates;
    }
}
