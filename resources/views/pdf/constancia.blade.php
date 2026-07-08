<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Constancia</title>

<style>

@page{
    margin:35px 55px;
}

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size:13px;
    color:#000;
    line-height:1.35;
}

.header{
    width:100%;
    margin-top:25px;
    margin-bottom:50px;
}

.header-left{
    float:left;
    width:68%;
    text-align:center;
    font-size:20px;
    font-weight:bold;
    color:#7b7b7b;
}

.header-right{
    float:right;
    width:32%;
    font-size:10px;
    text-align:left;
    line-height:1.1;
    color:#555;
}

.clear{
    clear:both;
}

.codigo{
    margin-top:35px;
}

.titulo{
    text-align:center;
    margin-top:70px;
    margin-bottom:55px;
    font-size:22px;
    font-weight:bold;
    letter-spacing:8px;
    text-decoration:underline;
}

.texto{
    text-align:justify;
    font-size:14px;
}

.tabla{
    width:100%;
    border-collapse:collapse;
    margin-top:30px;
    margin-bottom:55px;
}

.tabla th{
    border:1px solid black;
    text-align:center;
    padding:4px;
    font-size:13px;
}

.tabla td{
    border:1px solid black;
    padding:4px;
    font-size:13px;
}

.final{
    margin-top:25px;
    font-size:14px;
}

.firma{
    margin-top:120px;
    text-align:center;
}

.linea{
    width:320px;
    border-top:1px solid black;
    margin:0 auto 10px auto;
}

.footer{
    position:fixed;
    bottom:10px;
    left:0;
    right:0;
    text-align:center;
    font-size:12px;
    color:#777;
}

</style>

</head>

<body>

<div class="header">

<div class="header-left">
UNIVERSIDAD DE SAN PEDRO SULA
</div>

<div class="header-right">

<strong>RPD-0118</strong> CONSTANCIA DE<br>
REALIZACIÓN DE PRÁCTICA<br>

EDICIÓN:&nbsp;&nbsp;01<br>

REVISIÓN:&nbsp;01

<span style="float:right">

FECHA: 28/11/2018<br>

FECHA: 28/11/2018

</span>

</div>

<div class="clear"></div>

</div>


<div class="titulo">
CONSTANCIA
</div>


<div class="texto">

La suscrita Abogada Directora del Consultorio Jurídico de la Universidad de San Pedro Sula (USAP) hace constar que la pasante de la carrera de Derecho abajo descrito realizó su práctica reglamentaria previa al título de abogado cuya carpeta se envía a la Dirección de la Escuela de Derecho para ser revisada por la correspondiente Terna Evaluadora.

</div>


<table class="tabla">

<tr>

<th width="34%">
NOMBRE<br>
PROCURADOR
</th>

<th width="33%">
FECHA INGRESO A<br>
PRÁCTICA
</th>

<th width="33%">
FECHA DE EGRESO<br>
DE PRÁCTICA
</th>

</tr>

<tr>

<td>
{{ $procurador->nombre_completo }}
</td>

<td align="center">
____________________
</td>

<td align="center">
____________________
</td>

</tr>

</table>


<div class="final">

Y para los fines consiguientes extiendo la presente en la ciudad de San Pedro Sula a los _____ días del mes __________ de ______.

</div>


<div class="firma">

<div class="linea"></div>

<strong>Nombre y Firma</strong><br>

<strong>Director del Consultorio Jurídico</strong>

</div>


<div class="footer">

Página 1 de 1

</div>

</body>

</html>