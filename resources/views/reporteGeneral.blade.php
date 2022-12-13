@extends('voyager::master')
@section('content')

<div class="content">
    <div class="text-center">
        <h1>REPORTE GENERAL</h1>
        <div class="content">
            <div class="text-center">
                <br>
                <form action="download" method="POST" class="form-row mb-4">
                    @csrf
                    <div class="col">
                        <h4>Filtrar Reporte</h4>
                        <label for="fecha">Desde</label>
                        <input type="month" name="filter1" value="filter1" required>
                        <label for="fecha">Hasta</label>
                        <input type="month" name="filter2" value="filter2" required>
                        <div>
                            <button type="submit" class="btn btn-success">Descargar</button>
                            <a type="submit" name="para" value="mamá"></a>
                        </div>
                    </div>
                </form>
            </div>
            <br>
        </div>
        <br>
        <form action="reportFilter" method="POST" class="form-row mb-4">
            @csrf
            <div class="col">
                <h4>Filtrar Tablas por mes</h4>
                <label for="fecha">Desde</label>
                <input type="month" name="filter1" value="filter1" required>
                <label for="fecha">Hasta</label>
                <input type="month" name="filter2" value="filter2" required>
                <div>
                    <button id="filtrar" type="submit" class="btn btn-warning" style="position: relative; left: -5%; top: 44px">Filtrar</button>
                </div>
            </div>
        </form>
        <form action="reportFilter" method="POST" class="form-row mb-4">
            @csrf
            <div class="col">
                <div>
                    <button id="limpiar" type="submit" class="btn btn-warning" style="position: relative; left: 5%; bottom: px">Limpiar-Filtro</button>
                </div>
            </div>
        </form>
        <br>
        <table id="tabla"  class="table"  style="width: 100%; border: 1px solid #000; margin: 0 0 1em 1em; ">
            <thead style=" background:#FF0000">
                <tr>
                    <th scope="col" style=" background:#E62E2D; color:aliceblue">CONCEPTO</th>
                    @foreach($mes as $info)
                    <th scope="col" style=" background:#E62E2D; color:aliceblue">{{$info['mes']}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                $p = 0;
                @endphp
                @foreach($headers as $head)
                <tr>
                    @if($head == 'TOTAL PRODUCTO TERMINADO' || $head == 'TOTAL OTROS' || $head == 'TOTAL COSTOS DE VENTAS' || $head == 'UTILIDAD BRUTA' || $mes == 'TRIMESTRE')
                    <th scope="row" style=" background:#E62E2D;color:aliceblue">{{$head}}</th>
                    @else
                    <th scope="row" style=" background:#E62E2D;color:black">{{$head}}</th>
                    @endif
                    @foreach($data as $info)
                    @if($head=='TOTAL PRODUCTO TERMINADO' || $head=='TOTAL OTROS' || $head=='TOTAL COSTOS DE VENTAS' || $head=='UTILIDAD BRUTA' || $mes=='TRIMESTRE' || $head=='GASTOS DE VENTAS' || $head=='DEPRECIACIONES Y AMORTIZACIONES' || $head=='GASTOS DE VENTAS' || $head=='TOTAL GASTOS OPERACIONALES'|| $head=='UTILIDAD OPERACIONAL' || $head=='TOTAL NO OPERACIONALES' || $head=='UTILIDAD ANTES DE IMPUESTOS' || $head=='EBITDA')
                        @if(strpos($info[$p], '%') != false) 
                            <td>{{$info[$p]}}</td>
                        @else
                            @if($info[$p] < 0) 
                                <td style=" background:#E62E2D;color:aliceblue">({{'$'.number_format($info[$p])}})</td>
                            @else
                            <td style=" background:#E62E2D;color:aliceblue">{{'$'.number_format($info[$p])}}</td>
                            @endif
                        @endif
                        @else
                        @if(strpos($info[$p], '%')!= false)
                        <td style="color:#000">{{$info[$p]}}</td>
                        @else
                        @if($info[$p] < 0) <td style="color:#000">({{'$'.number_format($info[$p])}})</td>
                            @else
                            <td style="color:#000">{{'$'.number_format($info[$p])}}</td>
                            @endif
                            @endif
                            @endif

                            @endforeach
                            @php
                            $p++;
                            @endphp
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    const btnExportar = document.querySelector("#btnExportar");
    tabla = document.querySelector("#tabla");
    btnExportar.addEventListener("click", function() {
        let tableExport = new TableExport(tabla, {
            exportButtons: false,
            filename: "Reporte general",
            sheetname: "Reporte general",
        });
        let datos = tableExport.getExportData();
        let preferenciasDocumento = datos.tabla.xlsx;
        tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
    });
</script>
@stop