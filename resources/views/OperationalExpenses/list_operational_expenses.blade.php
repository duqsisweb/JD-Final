@extends('voyager::master')
@section('content')

<div class="content">
    <div class="text-center">
        <h1>GASTOS OPERACIONALES 2022</h1>
        <br>
        <form action="gastosfilter" method="POST" class="form-row mb-4">
            @csrf
            <div class="col">
                <h4>Filtrar por mes</h4>
                <label for="fecha">Desde</label>
                <input type="month" name="filter1" value="filter1" required>
                <label for="fecha">Hasta</label>
                <input type="month" name="filter2" value="filter2" required>
                <button id="filtrar" type="submit" class="btn btn-success" style="position: relative; left: 2%;">Filtrar</button>
            </div>
        </form>
        <form action="gastosfilter" method="POST" class="form-row mb-4">
            @csrf
            <div class="col">
                <div>
                    <button id="limpiar" type="submit" class="btn btn-dark" style="position: relative; left: 0%; bottom: px">Limpiar-Filtro</button>
                </div>
            </div>
        </form>
        <!-- <button id="btnExportar" type="submit" class="btn btn-warning" style="position: relative; left: 14%; bottom: 44px">Exportar Reporte</button> -->
    </div>
    <br>
    <table id="tabla" class="table" style="width: 100%; border: 1px solid #000; margin: 0 0 1em 1em">
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
                @if($head == 'GASTOS DE VENTAS' || $head == 'GASTOS DE ADMINISTRACION' || $head == 'DEPRECIACIONES Y AMORTIZACIONES' || $head == 'TOTAL GASTOS OPERACIONALES' || $head == 'UTILIDAD OPERACIONAL')
                    <th scope="row" style=" background:#E62E2D;color:aliceblue">{{$head}}</th>
                @else
                    <th scope="row" style=" background:#E62E2D;color:black">{{$head}}</th>
                @endif
                @foreach($dates as $info)
                @if($p<=$contador)
                    @if($head == 'GASTOS DE VENTAS' || $head == 'GASTOS DE ADMINISTRACION' || $head == 'DEPRECIACIONES Y AMORTIZACIONES' || $head == 'TOTAL GASTOS OPERACIONALES' || $head == 'UTILIDAD OPERACIONAL')
                        @if(is_string($info[$p])==true) 
                        <td>{{$info[$p]}}</td>
                        @endif
                        @if(is_string($info[$p])==false) 
                        <td style=" background:#E62E2D;color:aliceblue">{{'$'.number_format($info[$p])}}</td>
                        @endif
                        @else
                        @if(is_string($info[$p])==true) 
                        <td>{{$info[$p]}}</td>
                        @endif
                        @if(is_string($info[$p])==false) 
                        <td style="color:black" >{{'$'.number_format($info[$p])}}</td>
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
<!-- <script>
    const btnExportar = document.querySelector("#btnExportar");
    tabla = document.querySelector("#tabla");
    console.log(tabla)
    btnExportar.addEventListener("click", function() {
        let tableExport = new TableExport(tabla, {
            exportButtons: false,
            filename: "Reporte Gastos Operacionales",
            sheetname: "Reporte Gastos Operacionales",
        });
        let datos = tableExport.getExportData();
        let preferenciasDocumento = datos.tabla.xlsx;
        tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
    });
</script> -->
@stop