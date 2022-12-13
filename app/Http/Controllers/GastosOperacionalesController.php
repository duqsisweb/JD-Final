<?php

namespace App\Http\Controllers;

use App\Http\Traits\CostosUnitTrait;
use App\Http\Traits\CostosUnitTraitC;
use App\Http\Traits\GastosOperTrait;
use App\Http\Traits\GastosOperTraitC;
use App\Http\Traits\VentasNetasTrait;
use App\Http\Traits\VentasNetasUnitTrait;
use App\Http\Traits\VentasNetasUnitTraitC;
use App\Http\Traits\VentasToneladasTrait;
use App\Http\Traits\VentasToneladasTraitC;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastosOperacionalesController extends Controller
{
    use CostosUnitTrait;
    use VentasNetasUnitTrait;
    use GastosOperTraitC;
    use VentasToneladasTrait;
    use VentasNetasTrait;
    use VentasToneladasTraitC;
    use VentasNetasUnitTraitC;
    use CostosUnitTraitC;
    use GastosOperTrait;

    public function operational_expenses(Request $request)
    {
        if ($request->filter1 != null) {
            if($request->filter1 > $request->filter2){
                return redirect('admin/gastos/operacionales')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
            $infoGastos = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->whereBetween('INF_D_FECHAS', [$fechaIni, $fechaFin])->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoGastos = $infoGastos->toArray();
        } else {
            $infoGastos = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
            $infoGastos = $infoGastos->toArray();
            $fechaIni = null;
            $fechaFin = null;
        }
        $headers = [
            'GASTOS DE ADMINISTRACION', 'PORCENTAJE GASTOS DE ADMINISTRACION',
            'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'HONORARIOS', 'PORCENTAJE HONORARIOS',
            'SERVICIOS', 'PORCENTAJE SERVICIOS', 'OTROS', 'PORCENTAJE OTROS', 'GASTOS DE VENTAS', 'PORCENTAJE GASTOS DE VENTAS',
            'GATOS DE PERSONAL', 'PORCENTAJE GATOS DE PERSONAL', 'POLIZA CARTERA', 'PORCENTAJE POLIZA CARTERA', 'FLETES',
            'PORCENTAJE FLETES', 'SERVICIO LOGISTICO', 'PORCENTAJE SERVICIO LOGISTICO', 'ESTRATEGIA COMERCIAL',
            'PORCENTAJE ESTRATEGIA COMERCIAL', 'IMPUESTOS', 'PORCENTAJE IMPUESTOS', 'DESCUENTOS PRONTO PAGO',
            'PORCENTAJE DESCUENTOS PRONTO PAGO', 'OTROS', 'PORCENTAJE OTROS', 'DEPRECIACIONES Y AMORTIZACIONES',
            'PORCENTAJE DEPRECIACIONES Y AMORTIZACIONES', 'TOTAL GASTOS OPERACIONALES', 'PORCENTAJE TOTAL GASTOS OPERACIONALES',
            'UTILIDAD OPERACIONAL', 'PORCENTAJE UTILIDAD OPERACIONAL'
        ];
        $mes = [];
        $formGastos = [];
        $c = 1;
        foreach ($infoGastos as $data) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
                $infoACEITES =  round($data->ACEITES, 5);
                $infoMARGARINAS =  round($data->MARGARINAS, 5);
                $infoSOLIDOS_CREMOSOS =  round($data->SOLIDOS_CREMOSOS, 5);
                $infoINDUSTRIALES =  round($data->INDUSTRIALES, 5);
                $infoOTROS =  round($data->ACIDOS_GRASOS_ACIDULADO, 5);
                $infoSERVICIO_MAQUILA =  round($data->SERVICIO_MAQUILA, 5);
                $TOTALP = intval(round($infoACEITES + $infoMARGARINAS + $infoSOLIDOS_CREMOSOS, 5));
                $TOTALO = intval(round($infoINDUSTRIALES + $infoOTROS + $infoSERVICIO_MAQUILA, 5));
                $TOTALV = intval($TOTALP + $TOTALO);
                $infoACEITES2 = intval(round($data->ACEITES2, 5));
                $infoMarga2 = intval(round($data->MARGARINAS2));
                $infoSOLID2 = intval(round($data->SOLIDOS_CREMOSOS2, 5));
                $infoTOTP = intval(round($infoACEITES2+$infoMarga2+$infoSOLID2));
                $infoINDU = intval(round($data->INDUSTRIALES2, 5));
                $infoSERVM = intval(round($data->SERVICIO_MAQUILA2));
                $TOTSUMOTR = round($data->ACEITES2+$data->MARGARINAS2+$data->SOLIDOS_CREMOSOS2+$data->INDUSTRIALES2+$data->ACIDOS_GRASOS_ACIDULADO2+$data->SERVICIO_MAQUILA2);
                $UTLBRUTA = +$TOTALV - $TOTSUMOTR;
                $gastAdmin = round($data->GASTOS_ADMINISTRACION, 5);
                $porceGasAdmin = round($gastAdmin * 100 / $TOTALV, 2) . '%';
                $garPersonal = round($data->GASTOS_PERSONAL, 5);
                $porcePerson = round($garPersonal * 100 / $TOTALV, 2) . '%';
                $honorarios = round($data->HONORARIOS, 5);
                $porceHonor = round($honorarios * 100 / $TOTALV, 2) . '%';
                $servicios = round($data->SERVICIOS, 5);
                $porceServi = round($servicios * 100 / $TOTALV, 2) . '%';
                $otros = round($gastAdmin - $garPersonal - $honorarios - $servicios, 5);
                $porceOtros = round($otros * 100 / $TOTALV, 2) . '%';
                $gasVentas = round($data->GASTOS_VENTAS, 5);
                $porceVentas = round($gasVentas * 100 / $TOTALV, 2) . '%';
                $gasPersonales2 = round($data->GASTOS_PERSONAL2, 5);
                $porcePersonales2 = round($gasPersonales2 * 100 / $TOTALV, 2) . '%';
                $polCartera = round($data->POLIZA_CARTERA, 5);
                $porcePrtCartera = round($polCartera * 100 / $TOTALV, 2) . '%';
                $fletes = round($data->FLETES,5);
                $porceFletes = round($fletes * 100 / $TOTALV, 2) . '%';
                $servLogistico = round($data->SERVICIO_LOGISTICO, 5);
                $porceservLog = round($servLogistico * 100 / $TOTALV, 2) . '%';
                $estrComer = round($data->ESTRATEGIA_COMERCIAL);
                $porceEstrComer = round($estrComer * 100 / $TOTALV, 2) . '%';
                $impuestos = round($data->IMPUESTOS, 5);
                $porceImpu = round($impuestos * 100 / $TOTALV, 2) . '%';
                $descPronPa = round($data->DES_PRONTO_PAGO, 5);
                $porceDesPr = round($descPronPa * 100 / $TOTALV, 2) . '%';
                $otr2 = +$gasVentas - $gasPersonales2 - $polCartera - $fletes - $servLogistico - $estrComer - $impuestos - $descPronPa;
                $porceOtr2 = round($otr2 * 100 / $TOTALV, 2) . '%';
                $depreAmorti = round($data->DEPRECIACIONES_AMORTIZACIONES, 5);
                $porceDepreAmor = round($depreAmorti * 100 / $TOTALV, 2) . '%';
                $totGasOper = +$gastAdmin + $gasVentas + $depreAmorti;
                $porceTotGasOper = round($totGasOper * 100 / $TOTALV, 2) . '%';
                $UtilOper = intval($UTLBRUTA - $totGasOper);
                $porceUtilOper = round($UtilOper * 100 / $TOTALV, 2) . '%';
                $dateObject = DateTime::createFromFormat('!m', $data->INF_D_MES)->format('F');
                
                array_push($formGastos, [
                    $gastAdmin, $porceGasAdmin, $garPersonal, $porcePerson, $honorarios, $porceHonor, $servicios, $porceServi, $otros, $porceOtros, $gasVentas, $porceVentas, $gasPersonales2, $porcePersonales2, $polCartera, $porcePrtCartera, $fletes, $porceFletes, $servLogistico, $porceservLog,
                    $estrComer, $porceEstrComer, $impuestos, $porceImpu, $descPronPa, $porceDesPr, $otr2, $porceOtr2, $depreAmorti, $porceDepreAmor, $totGasOper,
                    $porceTotGasOper, $UtilOper, $porceUtilOper
                ]);
                array_push($mes, ['mes' => __($dateObject)]);
                array_push($mes, ['mes' => 'TRIMESTRE']);
                $c++;
                switch ($c) {
                    case $c == 4:
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 3, 1);
                        
                        $formEdit = $formGastos;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit as $prom) {
                                if ($i % 2 == 0) {
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($i = 0; $i < $cuenta; $i++) {
                            if ($i % 2 == 0) {
                            } else {
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, intval($sumaCostos[$i]));
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        }
                        array_push($formGastos, $sumfinals);
                        break;
                    case $c == 8:
                        $formEdit1 = array_slice($formGastos, 4, 3);
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 7, 1);

                        $formEdit = $formGastos;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit1[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit1 as $prom) {
                                if ($i % 2 == 0) {
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($i = 0; $i < $cuenta; $i++) {
                            if ($i % 2 == 0) {
                            } else {
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        }
                        array_push($formGastos, $sumfinals);
                        break;
                    case $c == 12:
                        $formEdit2 = array_slice($formGastos, 8, 3);
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 11, 1);
                        $formEdit = $formGastos;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit2[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit2 as $prom) {
                                if ($i % 2 == 0) {
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($i = 0; $i < $cuenta; $i++) {
                            if ($i % 2 == 0) {
                            } else {
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        }
                        array_push($formGastos, $sumfinals);
                        break;
                    case $c == 15:
                        $formEdit2 = array_slice($formGastos, 12, 3);
                        $ventasNetasTabla = $this->TablaVentas($fechaIni, $fechaFin);
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 15, 1);
                        $formEdit = $formGastos;
                        $sumaCostos = [];
                        for ($i = 0; $i < count($formEdit2[0]); $i++) {
                            $suma = 0;
                            foreach ($formEdit2 as $prom) {
                                if ($i % 2 == 0) {
                                    $suma += $prom[$i];
                                }
                            }
                            array_push($sumaCostos, intval(round($suma / 3)));
                        }
                        $cuenta = count($sumaCostos);
                        for ($i = 0; $i < $cuenta; $i++) {
                            if ($i % 2 == 0) {
                            } else {
                                unset($sumaCostos[$i]);
                            }
                        }
                        $sumaCostos = array_values($sumaCostos);
                        $sumfinals = [];
                        for ($i = 0; $i < count($sumaCostos); $i++) {
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                        }
                        array_push($formGastos, $sumfinals);
                        break;
                }
                $c++;
            } else {
                $infoACEITES =  round($data->ACEITES, 5);
                $infoMARGARINAS =  round($data->MARGARINAS, 5);
                $infoSOLIDOS_CREMOSOS =  round($data->SOLIDOS_CREMOSOS, 5);
                $infoINDUSTRIALES =  round($data->INDUSTRIALES, 5);
                $infoOTROS =  round($data->ACIDOS_GRASOS_ACIDULADO, 5);
                $infoSERVICIO_MAQUILA =  round($data->SERVICIO_MAQUILA, 5);
                $TOTALP = intval(round($infoACEITES + $infoMARGARINAS + $infoSOLIDOS_CREMOSOS, 5));
                $TOTALO = intval(round($infoINDUSTRIALES + $infoOTROS + $infoSERVICIO_MAQUILA, 5));
                $TOTALV = intval($TOTALP + $TOTALO);
                $infoACEITES2 = intval(round($data->ACEITES2, 5));
                $infoMarga2 = intval(round($data->MARGARINAS2));
                $infoSOLID2 = intval(round($data->SOLIDOS_CREMOSOS2, 5));
                $infoTOTP = intval(round($infoACEITES2+$infoMarga2+$infoSOLID2));
                $infoINDU = intval(round($data->INDUSTRIALES2, 5));
                $infoSERVM = intval(round($data->SERVICIO_MAQUILA2));
                $TOTSUMOTR = round($data->ACEITES2+$data->MARGARINAS2+$data->SOLIDOS_CREMOSOS2+$data->INDUSTRIALES2+$data->ACIDOS_GRASOS_ACIDULADO2+$data->SERVICIO_MAQUILA2);
                $UTLBRUTA = +$TOTALV - $TOTSUMOTR;
                $gastAdmin = round($data->GASTOS_ADMINISTRACION, 5);
                $porceGasAdmin = round($gastAdmin * 100 / $TOTALV, 2) . '%';
                $garPersonal = round($data->GASTOS_PERSONAL, 5);
                $porcePerson = round($garPersonal * 100 / $TOTALV, 2) . '%';
                $honorarios = round($data->HONORARIOS, 5);
                $porceHonor = round($honorarios * 100 / $TOTALV, 2) . '%';
                $servicios = round($data->SERVICIOS, 5);
                $porceServi = round($servicios * 100 / $TOTALV, 2) . '%';
                $otros = round($gastAdmin - $garPersonal - $honorarios - $servicios, 5);
                $porceOtros = round($otros * 100 / $TOTALV, 2) . '%';
                $gasVentas = round($data->GASTOS_VENTAS, 5);
                $porceVentas = round($gasVentas * 100 / $TOTALV, 2) . '%';
                $gasPersonales2 = round($data->GASTOS_PERSONAL2, 5);
                $porcePersonales2 = round($gasPersonales2 * 100 / $TOTALV, 2) . '%';
                $polCartera = round($data->POLIZA_CARTERA, 5);
                $porcePrtCartera = round($polCartera * 100 / $TOTALV, 2) . '%';
                $fletes = round($data->FLETES,5);
                $porceFletes = round($fletes * 100 / $TOTALV, 2) . '%';
                $servLogistico = round($data->SERVICIO_LOGISTICO, 5);
                $porceservLog = round($servLogistico * 100 / $TOTALV, 2) . '%';
                $estrComer = round($data->ESTRATEGIA_COMERCIAL);
                $porceEstrComer = round($estrComer * 100 / $TOTALV, 2) . '%';
                $impuestos = round($data->IMPUESTOS, 5);
                $porceImpu = round($impuestos * 100 / $TOTALV, 2) . '%';
                $descPronPa = round($data->DES_PRONTO_PAGO, 5);
                $porceDesPr = round($descPronPa * 100 / $TOTALV, 2) . '%';
                $otr2 = +$gasVentas - $gasPersonales2 - $polCartera - $fletes - $servLogistico - $estrComer - $impuestos - $descPronPa;
                $porceOtr2 = round($otr2 * 100 / $TOTALV, 2) . '%';
                $depreAmorti = round($data->DEPRECIACIONES_AMORTIZACIONES, 5);
                $porceDepreAmor = round($depreAmorti * 100 / $TOTALV, 2) . '%';
                $totGasOper = +$gastAdmin + $gasVentas + $depreAmorti;
                $porceTotGasOper = round($totGasOper * 100 / $TOTALV, 2) . '%';
                $UtilOper = intval($UTLBRUTA - $totGasOper);
                $porceUtilOper = round($UtilOper * 100 / $TOTALV, 2) . '%';
                $dateObject = DateTime::createFromFormat('!m', $data->INF_D_MES)->format('F');
                
                array_push($formGastos, [
                    $gastAdmin, $porceGasAdmin, $garPersonal, $porcePerson, $honorarios, $porceHonor, $servicios, $porceServi, $otros, $porceOtros, $gasVentas, $porceVentas, $gasPersonales2, $porcePersonales2, $polCartera, $porcePrtCartera, $fletes, $porceFletes, $servLogistico, $porceservLog,
                    $estrComer, $porceEstrComer, $impuestos, $porceImpu, $descPronPa, $porceDesPr, $otr2, $porceOtr2, $depreAmorti, $porceDepreAmor, $totGasOper,
                    $porceTotGasOper, $UtilOper, $porceUtilOper
                ]);
                array_push($mes, ['mes' => __($dateObject)]);
                $c++;
            }
        }
        array_push($mes, ['mes' => 'ACUMULADO']);
        array_push($mes, ['mes' => 'PROMEDIO']);
        $ventTotales = [];
        $infoOper = [];
        foreach ($infoGastos as $dataOper) {
            $gasAdmonO = round($dataOper->GASTOS_ADMINISTRACION, 5);
            $gasPersonalO = round($dataOper->GASTOS_PERSONAL, 5);
            $honorariosO = round($dataOper->HONORARIOS, 5);
            $serviciosO = round($dataOper->SERVICIOS, 5);
            $otrosO = $gasAdmonO - $gasPersonalO - $honorariosO - $serviciosO;
            $gasVentasO = round($dataOper->GASTOS_VENTAS, 5);
            $gasPersonalesO = round($dataOper->GASTOS_PERSONAL2, 5);
            $polCarteraO = round($dataOper->POLIZA_CARTERA, 5);
            $fletesO = round($dataOper->FLETES, 5);
            $servLogisticoO = round($dataOper->SERVICIO_LOGISTICO, 5);
            $estrComerO = round($dataOper->ESTRATEGIA_COMERCIAL, 5);
            $impuestosO = round($dataOper->IMPUESTOS, 5);
            $descPronPaO = round($dataOper->DES_PRONTO_PAGO, 5);
            $otr2 = +$gasVentasO - $gasPersonalesO - $polCarteraO - $fletesO - $servLogisticoO - $estrComerO - $impuestosO - $descPronPaO;
            $depreAmorti = round($dataOper->DEPRECIACIONES_AMORTIZACIONES, 5);
            $totGasOper = +$gasAdmonO + $gasVentasO + $depreAmorti;
            $infoACEITES =  round($dataOper->ACEITES, 5);
            $infoMARGARINAS =  round($dataOper->MARGARINAS, 5);
            $infoSOLIDOS_CREMOSOS =  round($dataOper->SOLIDOS_CREMOSOS, 5);
            $infoINDUSTRIALES =  round($dataOper->INDUSTRIALES, 5);
            $infoOTROS =  round($dataOper->ACIDOS_GRASOS_ACIDULADO, 5);
            $infoSERVICIO_MAQUILA =  round($dataOper->SERVICIO_MAQUILA, 5);
            $TOTALP = intval(round($infoACEITES + $infoMARGARINAS + $infoSOLIDOS_CREMOSOS, 5));
            $TOTALO = intval(round($infoINDUSTRIALES + $infoOTROS + $infoSERVICIO_MAQUILA, 5));
            $TOTALV = intval(round($TOTALP + $TOTALO, 5));
            $infoACEITES = intval(round($dataOper->ACEITES2, 5));
            $infoMarga = intval(round($dataOper->MARGARINAS2, 5));
            $infoSOLID = intval(round($dataOper->SOLIDOS_CREMOSOS2, 5));
            $infoTOTP = intval(round($dataOper->SOLIDOS_CREMOSOS2 + $dataOper->MARGARINAS2 + $dataOper->ACEITES2, 5));
            $infoINDU = intval(round($dataOper->INDUSTRIALES2, 5));
            $infoOTROS = intval(round($dataOper->ACIDOS_GRASOS_ACIDULADO2, 5));
            $infoSERVM = intval(round($dataOper->SERVICIO_MAQUILA2, 5));
            $infoTOLALO = $infoINDU + $infoOTROS + $infoSERVM;
            $TOTALO = intval(round($dataOper->INDUSTRIALES + $dataOper->ACIDOS_GRASOS_ACIDULADO + $dataOper->SERVICIO_MAQUILA, 5));
            $TOTSUMOTR = $TOTALO + $infoTOTP;
            $TOTLCOSVEN = $infoTOTP + $TOTALO;
            $UTLBRUTA = +$TOTALV - $TOTSUMOTR;
            $UtilOper = $UTLBRUTA - $totGasOper;
            array_push($infoOper, [$gasAdmonO, $gasPersonalO, $honorariosO, $serviciosO, $otrosO, $gasVentasO, $gasPersonalesO, $polCarteraO, $fletesO, $servLogisticoO, $estrComerO, $impuestosO, $descPronPaO, $otr2, $depreAmorti, $totGasOper, $UtilOper]);
            array_push($ventTotales, $TOTALV);
        }

        $sumatorias = [];
        $promedios = [];
        for ($i = 0; $i < count($infoOper[0]); $i++) {
            $suma = 0;
            foreach ($infoOper as $sum) {
                $suma += $sum[$i];
            }
            array_push($sumatorias, intval(round($suma, 5)));
            array_push($promedios, intval(round($suma / count($infoGastos)), 5));
        }
        for ($i = 0; $i < count($infoGastos); $i++) {
            $suma = 0;
            foreach ($ventTotales as $tot) {
                $suma += $tot;
            }
        }
        $sumtot = $suma;

        $acumulados = [];
        for ($i = 0; $i < count($infoOper[0]); $i++) {
            $sumD = $sumatorias[$i];
            $porceD = $sumD / $sumtot;
            array_push($acumulados, $sumD);
            array_push($acumulados, round($porceD, 2) . '%');
        }
        array_push($formGastos, $acumulados);

        $promediosF = [];
        for ($i = 0; $i < count($infoOper[0]); $i++) {
            $promD = $promedios[$i];
            $promPorce = $promD / ($sumtot / count($infoGastos));
            array_push($promediosF, $promD);
            array_push($promediosF, round($promPorce, 2) . '%');
        }
        array_push($formGastos, $promediosF);


        $form = 0;
        foreach ($formGastos as $form) {
            $form = count($form);
        }
        return view('OperationalExpenses/list_operational_expenses', ['headers' => $headers, 'dates' => $formGastos, 'mes' => $mes, 'contador' => $form]);
    }




    public function unit_operational_expenses(Request $request)
    {
        if ($request->filter1 != null) {
            if($request->filter1 > $request->filter2){
                return redirect('admin/gastosU/operUnit')->with(['message' => "El mes inicial debe ser mayor que el mes final", 'alert-type' => 'error']);
            }
            $fechaIni = $request->filter1 . '-1';
            $fechaFin = $request->filter2 . '-1';
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
            $dateObject = DateTime::createFromFormat('!m', $info1->INF_D_MES)->format('F');
            $infoTOT = round($info1->SOLIDOS_CREMOSOS2 + $info1->MARGARINAS2 + $info1->ACEITES2) + round($info1->INDUSTRIALES2 + $info1->ACIDOS_GRASOS_ACIDULADO2 + $info1->SERVICIO_MAQUILA2);
            array_push($data1, [
                $gasAdmon, $totVEN, $gasPerson, $honorarios, $servicios, $otros, $gasVentas, $gasPerson2, $polizCart, $fletes, $servicLog, $estratComer, $impuestos, $descuProntP, $otros2, $depresiaci, $totGasOper, $infoTOT
            ]);
            array_push($mes, ['mes' => __($dateObject)]);
            array_push($ven, [$servMqui, $totVen]);
            array_push($arrTotCosVen, $totCosVen);
            array_push($gasOp, [
                $gasAdmon, $gasPerson, $honorarios, $servicios, $otros, $gasVentas, $gasPerson2, $polizCart, $fletes, $servicLog, $estratComer, $impuestos, $descuProntP, $otros2, $depresiaci
            ]);
        }
        $mes2=[];
        $data2 = [];
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
        $cuentaTablas= 0;
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
                $cuentaTablas++;
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
                        $utilopera = $porceTotaGasOpera - $costosVentasUnitariosTabla[0][14];
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        $h++;
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
                        $utilopera = $porceTotaGasOpera - $costosVentasUnitariosTabla[0][14];
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        $h++;
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
                        $toneladasTabla = array_slice($toneladasTabla, 11, 1);
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
                        $utilopera = $porceTotaGasOpera - $costosVentasUnitariosTabla[0][14];
                        array_push($trimestre, $utilopera);
                        array_push($trimestre, round($utilopera * 100 / $ventasNetasUnitariasTabla[0][7], 2) . '%');
                        array_push($formOper, $trimestre);
                        array_push($meses,['mes'=>'TRIMESTRE']);
                        $h++;
                        break;
                    case $h == 15:
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
                        $toneladasTabla = array_slice($toneladasTabla, 15, 1);
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
                        $utilopera = $porceTotaGasOpera - $costosVentasUnitariosTabla[0][14];
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
                    array_push($meses,$mes[$m]);
                    $m++;
                    $h++;
            }
        }
        array_push($meses, ['mes' => 'ACUMULADO']);
        array_push($meses, ['mes' => 'PROMEDIO']);
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
        $ventasNetasUnitarios= array_slice($ventasNetasUnitarios, -2,2);
        $gastosOperacionales = $this->tablaGastosoperacionalesC();
        $gastosOperacionales= array_slice($gastosOperacionales,-2,2);
        $ventastoneladas = $this->TablaVentasToneladasC();
        $ventastoneladas = array_slice($ventastoneladas,-2,2);

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
        for ($i = 0; $i < count($gastosOperacionales[1]) - 4; $i++) {
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
        return view('OperationalExpenses\list_operational_expensesUnit', ['headers' => $headers, 'dates' => $formOper, 'mes' => $meses, 'contador' => count($formOper[0])]);
    }
}




