@extends('voyager::master')
@section('content')

<div class="content">
    <div class="text-center">
        <h1>PARTIDAS</h1>
        <br>
        <form action="partiteFilter" method="POST" class="form-row mb-4">
            @csrf
            <div class="col">
                <h4>Filtrar por mes</h4>
                <input type="month" name="month" value="" required>
                <button id="filtrar" type="submit" class="btn btn-success" style="position: relative; left: 2%;">Filtrar</button>
            </div>
        </form>
        <form action="partiteFilter" method="POST" class="form-row mb-4">
            @csrf
            <div class="col">
                <div>
                    <button id="limpiar" type="submit" class="btn btn-dark" style="position: relative; left: 0%; bottom: px">Limpiar-Filtro</button>
                </div>
            </div>
        </form>
        <!-- <button id="btnExportar" type="submit" class="btn btn-success" style="position: relative; left: 14%; bottom: 44px">Exportar Reporte</button> -->
    <br>
    <table id="tabla" class="table table-hover" style=" text-align: center; width: 50%; height: 300px; margin-left :25%;">
        <thead style=" background:#FF0000;width: 50%; vertical-align: top; padding: 0.3em;">
            <tr>
                <th scope="col" style=" background:#E62E2D; color:white">FECHAS</th>
                <th scope="col" style=" background:#E62E2D; color:white">PARTIDAS</th>
                <th scope="col" style=" background:#E62E2D; color:white">CANTIDAD</th>
                <th scope="col" style=" background:#E62E2D; color:white">ACCIONES</th>
            </tr>
        </thead>
        <tbody style=" background:#FF0000;width: 50%; vertical-align: top; padding: 0.3em; color:white">
            @foreach($dates as $data)
            <tr>
                <form action="editDate" method="POST">
                @csrf
                <th scope="row">{{$data->PAR_D_FECHA_REGISTRO}}</th>
                <th scope="row">{{$data->PAR_CPARTIDURA}}</th>
                <input type="text" name="nombre" value="{{$data->PAR_CPARTIDURA}}" hidden>
                <input type="text" name="Id" value="{{$data->PAR_NID}}"  hidden>
                <th><input type="text"  name="Cantidad" placeholder="{{$data->PAR_CCANTIDAD}}" ></th>
                    <th><button type="submit"><img src="https://cdn-icons-png.flaticon.com/512/159/159029.png" alt="" style="width: 25px;"></button></th>
                </form>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop