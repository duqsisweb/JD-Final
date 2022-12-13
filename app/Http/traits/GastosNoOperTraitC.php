<?php

namespace App\Http\Traits;

use DateTime;
use Illuminate\Support\Facades\DB;

trait GastosNoOperTraitC
{

    use VentasNetasTraitC;
    public function tablaGastosNoOperacionalesC()
    {
        $infoNoOpe = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
        $infoNoOpe = $infoNoOpe->toArray();

        $fomDates = [];
        $c = 1;
        foreach ($infoNoOpe as $data) {
            if ($c == 3 || $c == 7 || $c == 11 || $c == 15) {
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
                $ebitda = $utilAntImp+$totlNoOp+$data->DEPRECIACIONES_AMORTIZACIONES+$data->EBITDA;
                $porceEbtida = round($ebitda * 100 / $TOTALV, 2) . '%';
                $dateObject = DateTime::createFromFormat('m', $data->INF_D_MES)->format('F');
                array_push($fomDates, [
                    $finan, $porceFinan, $retActiv, $porceActiv, $gravFinan, $porceGravFin, $otros, $porceOtros,
                    $totlNoOp, $porceTotlNoOp, $utilAntImp, $porceUtilAntImp, $ebitda, $porceEbtida
                ]);
                $c++;
                //array_push($mes, ['mes' => $dateObject]);
                //array_push($mes, ['mes' => 'TRIMESTRE']);
                switch ($c) {
                    case $c == 4:
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 0, 3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }

                        $formEdit = $fomDates;
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
                            array_push($sumfinals, $sumaCostos[$i]);
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $sumaVentas[8], 2) . '%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                    case $c == 8:
                        $formEdit1 = array_slice($fomDates, 4, 3);
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 3, 3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }

                        $formEdit = $fomDates;
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
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $sumaVentas[8], 2) . '%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                    case $c == 12:
                        $formEdit2 = array_slice($fomDates, 8, 3);
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 6, 3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }

                        $formEdit = $fomDates;
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
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $sumaVentas[8], 2) . '%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                    case $c == 16:
                        $formEdit2 = array_slice($fomDates, 12, 3);
                        $ventasNetasTabla = $this->TablaVentasC();
                        $ventasNetasTabla = array_slice($ventasNetasTabla, 9, 3);
                        $sumaVentas = [];
                        for ($i = 0; $i < count($ventasNetasTabla[0]); $i++) {
                            $suma = 0;
                            foreach ($ventasNetasTabla as $prom) {
                                $suma += $prom[$i];
                            }
                            array_push($sumaVentas, intval(round($suma / 3)));
                        }

                        $formEdit = $fomDates;
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
                            array_push($sumfinals, round($sumaCostos[$i] * 100 / $sumaVentas[8], 2) . '%');
                        }
                        array_push($fomDates, $sumfinals);
                        $c++;
                        break;
                }
            } else {
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
                $ebitda = $utilAntImp+$totlNoOp+$data->DEPRECIACIONES_AMORTIZACIONES+$data->EBITDA;
                $porceEbtida = round($ebitda * 100 / $TOTALV, 2) . '%';
                $dateObject = DateTime::createFromFormat('m', $data->INF_D_MES)->format('F');
                array_push($fomDates, [
                    $finan, $porceFinan, $retActiv, $porceActiv, $gravFinan, $porceGravFin, $otros, $porceOtros,
                    $totlNoOp, $porceTotlNoOp, $utilAntImp, $porceUtilAntImp, $ebitda, $porceEbtida
                ]);
                $c++;
            }
        }


        $sumados = [];
        $ventTotales = [];
        foreach ($infoNoOpe as $infoOperations) {
            //----
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


        return $fomDates;
    }
}
