<?php
namespace App\Http\Traits;

use DateTime;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\CostosTrait;
use App\Http\Traits\VentasNetasTrait;
use App\Http\Traits\VentasNetasUnitTrait;
use App\Http\Traits\VentasToneladasTrait;

trait CostosUnitTrait {
    
    use CostosTrait;
    use VentasToneladasTrait;
    use VentasNetasTrait;
    use VentasNetasUnitTrait;
    use VentasNetasUnitTraitC; 
    use CostosTraitC;
    use VentasNetasUnitTraitC;

    public function TablaCostosUnit($fechaIni, $fechaFin)
    {
        if ($fechaIni != null) {
            $fechaIni = $fechaIni;
            $fechaFin = $fechaFin;
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
            $aceites = $info->ACEITES2;
            $aceiteDiv = $info->ACEITES;
            $margarinas = $info->MARGARINAS2;
            $margarinasDiv = $info->MARGARINAS;
            $solidCrem = $info->SOLIDOS_CREMOSOS2;
            $solidCreDiv = $info->SOLIDOS_CREMOSOS;
            $industriales = $info->INDUSTRIALES2;
            $induastrialesDiv = $info->INDUSTRIALES;
            $otrosAcGr = $info->ACIDOS_GRASOS_ACIDULADO2;
            $otrosAcGrDiv = $info->ACIDOS_GRASOS_ACIDULADO;
            $serviciosMqu = $info->SERVICIO_MAQUILA2;
            $serviciosMaqDiv = $info->SERVICIO_MAQUILA;
            $TOTALOT = $industriales + $otrosAcGr + $serviciosMqu;
            $infoTOTP = $aceites + $margarinas + $solidCrem;
            $infoTOTCOSV = $TOTALOT + $infoTOTP;
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
            $aceites2 = $info->TON_ACEITES;
            $margarinas2 = $info->TON_MARGARINAS;
            $solidCrem2 = $info->TON_SOLIDOS_CREMOSOS;
            $industriales2 = $info->TON_INDUSTRIALES_OLEO;
            $otrosAcGr2 = $info->TON_ACIDOS_GRASOS_ACIDULADO;
            $serviciosMaq2 = $info->TON_SERVICIO_MAQUILA;
            $totp = $aceites2 + $margarinas2 + $solidCrem2;
            $sumTt = $totp + $industriales2 + $otrosAcGr2;
            array_push($data2, [$aceites2, $margarinas2, $solidCrem2, $industriales2, $otrosAcGr2, $serviciosMaq2, $sumTt]);
        }
        $mes = [];
        $formates = [];
        $cosVenUnit = [];
        $c = 1;
        $m=0;
        $formates2=[];
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
                    $aceiteF, round($porceAceiteF, 2) . '%', $margaF, round($porceMargaF, 2) . '%',
                    $solidCreF, round($porceSolidCreF, 2) . '%', $industrialesF, round($porceIndustrialesF, 2) . '%',
                    $otrosAcGrF, round($porceOtrosAgF, 2) . '%', $servMaqF, round($porceservMaq) . '%', $ventTon,
                    round($porceCosVen, 2) . '%', $utlBrut, round($porceTotlBrut, 2) . '%'
                ]);
                if($c<=count($data2)){
                    array_push($formates2, [
                        intval(round($aceiteF)), round($porceAceiteF, 2) . '%', intval(round($margaF)), round($porceMargaF, 2) . '%',
                        intval(round($solidCreF)), round($porceSolidCreF, 2) . '%', intval(round($industrialesF)), round($porceIndustrialesF, 2) . '%',
                        intval(round($otrosAcGrF)), round($porceOtrosAgF, 2) . '%', intval(round($servMaqF)), round($porceservMaq) . '%', intval(round($ventTon)),
                        round($porceCosVen, 2) . '%', intval(round($utlBrut)), round($porceTotlBrut, 2) . '%'
                    ]);
                }
                array_push($cosVenUnit, [
                    intval(round($aceiteF)), intval(round($margaF)),
                    intval(round($solidCreF)), intval(round($industrialesF)),
                    intval(round($otrosAcGrF)), intval(round($servMaqF))
                ]);
                if($m<11){
                    array_push($mes, ['mes' => $meses[$m]]);
                }
                $m++;
                $c++;
                array_push($mes, ['mes' => 'TRIMESTRE']);
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
                        $ventasNetasUnitTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 3, 1);
                        $contoVentasTabla= $this->TablaCostosC();
                        $contoVentasTabla = array_slice($contoVentasTabla, 3, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladasC();
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
                        $utilBrut= $ventasNetasUnitTabla[0][7]-$totCosVen;
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
                        $ventasNetasUnitTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 7, 1);
                        $contoVentasTabla= $this->TablaCostosC();
                        $contoVentasTabla = array_slice($contoVentasTabla, 7, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladasC();
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
                        $utilBrut= $ventasNetasUnitTabla[0][7]-$totCosVen;
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
                        $ventasNetasUnitTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 11, 1);
                        $contoVentasTabla= $this->TablaCostosC();
                        $contoVentasTabla = array_slice($contoVentasTabla, 11, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladasC();
                        $ventasToneladasTabla = array_slice($ventasToneladasTabla,11, 1);
                        $sumsFin=[];
                        for($a=0;$a<count($sumCostVen);$a++){
                            array_push($sumsFin,$sumCostVen[$a]);
                            array_push($sumsFin,round($sumCostVen[$a]*100/$ventasNetasUnitTabla[0][$a],2).'%');
                        }
                        $totCosVen=round($contoVentasTabla[0][16]/$ventasToneladasTabla[0][0]);
                        array_push($sumsFin,$totCosVen);
                        $porceTotCosVen= round($totCosVen*100/$ventasNetasUnitTabla[0][7]);
                        array_push($sumsFin,round($porceTotCosVen,2).'%');
                        $utilBrut= $ventasNetasUnitTabla[0][7]-$totCosVen;
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
                        $ventasNetasUnitTabla = $this->TablaVentasUnitC();
                        $ventasNetasUnitTabla = array_slice($ventasNetasUnitTabla, 15, 1);
                        $contoVentasTabla= $this->TablaCostosC();
                        $contoVentasTabla = array_slice($contoVentasTabla, 15, 1);
                        $ventasToneladasTabla = $this->TablaVentasToneladasC();
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
                        $utilBrut= $ventasNetasUnitTabla[0][7]-$totCosVen;
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
                        $aceiteF, round($porceAceiteF, 2) . '%', $margaF, round($porceMargaF, 2) . '%',
                        $solidCreF, round($porceSolidCreF, 2) . '%', $industrialesF, round($porceIndustrialesF, 2) . '%',
                        $otrosAcGrF, round($porceOtrosAgF, 2) . '%', $servMaqF, round($porceservMaq) . '%', $ventTon,
                        round($porceCosVen, 2) . '%', $utlBrut, round($porceTotlBrut, 2) . '%'
                    ]);
                    if($c<=count($data2)){
                        array_push($formates2, [
                            intval(round($aceiteF)), round($porceAceiteF, 2) . '%', intval(round($margaF)), round($porceMargaF, 2) . '%',
                            intval(round($solidCreF)), round($porceSolidCreF, 2) . '%', intval(round($industrialesF)), round($porceIndustrialesF, 2) . '%',
                            intval(round($otrosAcGrF)), round($porceOtrosAgF, 2) . '%', intval(round($servMaqF)), round($porceservMaq) . '%', intval(round($ventTon)),
                            round($porceCosVen, 2) . '%', intval(round($utlBrut)), round($porceTotlBrut, 2) . '%'
                        ]);
                    }
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
        array_push($formates, $acumulados);

        $promVenNe= $this->TablaVentasUnitC();
        $promVenNe= array_slice($promVenNe,-1,1);
        $sumaPro=[];
        for($i=0;$i<count($formates2[0]);$i++){
            $sum=0;
            foreach($formates2 as $for){
                if ($i % 2 == 0) {
                    $sum += $for[$i];
                };
            }
            array_push($sumaPro,$sum);
        }
        $num= count($sumaPro);
        for($i=0;$i<$num;$i++){
            if ($i % 2 != 0) {
                unset($sumaPro[$i]);
            };
        }
        $sumaPro= array_values($sumaPro);
        $promedios = [];
        for($i=0;$i<count($sumaPro)-2;$i++){
            array_push($promedios, intval($sumaPro[$i]));
            array_push($promedios, round($sumaPro[$i]/$promVenNe[0][$i], 2) . '%');
        }
        $suma = 0;
        for ($i = 0; $i < count($ttven); $i++) {
            $suma += $ttven[$i];
            $promVenNetT = intval(round($suma / count($infoCosts)));
        }
        $resPromVenNe = $promVenNetT - $promVenNe[0][5];
        $t127 = intval(round($promCosVen[6] / $promTonsTot[6]));
        $t113 = intval(round($resPromVenNe / $promTonsTot[6]));
        $t129 = intval(round($t113 - $t127));
        array_push($promedios, $t127);
        array_push($promedios, round($t127 / $t113,2).'%');
        array_push($promedios, $t129);
        array_push($promedios, round($t129 / $t113,2).'%');
        array_push($formates, $promedios);
        

        return $formates;
    }
}