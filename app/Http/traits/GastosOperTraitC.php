<?php

namespace App\Http\Traits;

use DateTime;
use Illuminate\Support\Facades\DB;

trait GastosOperTraitC
{

   public function tablaGastosoperacionalesC()
   {
     
      $infoGastos = DB::connection('sqlsrv2')->table('TBL_RINFORME_JUNTA_DUQ')->orderBy('INF_D_FECHAS', 'asc')->get();
      $infoGastos = $infoGastos->toArray();
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
                $UTLBRUTA = $TOTALV - $TOTSUMOTR;
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
                $fletes = round($data->FLETES, 5);
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
                $dateObject = DateTime::createFromFormat('m', $data->INF_D_MES)->format('F');

            array_push($formGastos, [
               $gastAdmin, $porceGasAdmin, $garPersonal, $porcePerson, $honorarios, $porceHonor, $servicios, $porceServi, $otros, $porceOtros, $gasVentas, $porceVentas, $gasPersonales2, $porcePersonales2, $polCartera, $porcePrtCartera, $fletes, $porceFletes, $servLogistico, $porceservLog,
               $estrComer, $porceEstrComer, $impuestos, $porceImpu, $descPronPa, $porceDesPr, $otr2, $porceOtr2, $depreAmorti, $porceDepreAmor, $totGasOper,
               $porceTotGasOper, $UtilOper, $porceUtilOper
            ]);
            $c++;
            //array_push($mes, ['mes' => $dateObject]);
            //array_push($mes, ['mes' => 'TRIMESTRE']);
            switch ($c) {
               case $c == 4:
                  $ventasNetasTabla = $this->TablaVentasC();
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
                     array_push($sumfinals, $sumaCostos[$i]);
                     array_push($sumfinals, round($sumaCostos[$i] * 100 / $ventasNetasTabla[0][8], 2) . '%');
                  }
                  array_push($formGastos, $sumfinals);
                  break;
               case $c == 8:
                  $formEdit1 = array_slice($formGastos, 4, 3);
                  $ventasNetasTabla = $this->TablaVentasC();
                  $ventasNetasTabla = array_slice($ventasNetasTabla, 7, 3);
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
                  $ventasNetasTabla = $this->TablaVentasC();
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
               case $c == 16:
                  $formEdit2 = array_slice($formGastos, 12, 3);
                  $ventasNetasTabla = $this->TablaVentasC();
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
                $fletes = round($data->FLETES, 5);
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
                $dateObject = DateTime::createFromFormat('m', $data->INF_D_MES)->format('F');

            array_push($formGastos, [
               $gastAdmin, $porceGasAdmin, $garPersonal, $porcePerson, $honorarios, $porceHonor, $servicios, $porceServi, $otros, $porceOtros, $gasVentas, $porceVentas, $gasPersonales2, $porcePersonales2, $polCartera, $porcePrtCartera, $fletes, $porceFletes, $servLogistico, $porceservLog,
               $estrComer, $porceEstrComer, $impuestos, $porceImpu, $descPronPa, $porceDesPr, $otr2, $porceOtr2, $depreAmorti, $porceDepreAmor, $totGasOper,
               $porceTotGasOper, $UtilOper, $porceUtilOper
            ]);
            //array_push($mes, ['mes' => $dateObject]);
            $c++;
         }
      }
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
      return $formGastos;
   }
}
