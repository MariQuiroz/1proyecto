<?php
function formatearFecha($fecha)
{
    /*2024-06-24 21:37:30*/ 
    //solo invoco el prefijo en en caso de funciones_helper me aceptara solo funciones

    $dia=substr($fecha,8,2);
    $mes=substr($fecha,5,2);
    $anio=substr($fecha,0,2);

    $fechaFormateada=$dia."/".$mes."/".$anio;
    return $fechaFormateada;
}
function estado($nota)
{
    if($nota>=61)
    {
        $estado="aprobado";
    }
    else
    {
        $estado="reprobado";
    }
    return $estado;
}
?>