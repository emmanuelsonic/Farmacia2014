<?php

session_start();

// Array que vincula los IDs de los selects declarados en el HTML con el nombre de la tabla donde se encuentra su contenido
$listadoSelects = array(
    "farmacia" => "mnt_farmacia",
    "area" => "mnt_areafarmacia",
    "select1" => "mnt_grupoterapeutico",
    "select2" => "farm_catalogoproductos"
);

$IdEstablecimiento = $_SESSION["IdEstablecimiento"];
$IdModalidad = $_SESSION["IdModalidad"];

function validaSelect($selectDestino) {
    // Se valida que el select enviado via GET exista
    global $listadoSelects;
    if (isset($listadoSelects[$selectDestino]))
        return true;
    else
        return false;
}

function validaOpcion($opcionSeleccionada) {
    // Se valida que la opcion seleccionada por el usuario en el select tenga un valor numerico
    if (is_numeric($opcionSeleccionada))
        return true;
    else
        return false;
}

include '../../Clases/class.php';
$selectDestino = $_REQUEST["select"];
$opcionSeleccionada = $_REQUEST["opcion"];

//if(validaSelect($selectDestino) && validaOpcion($opcionSeleccionada))
//{
$tabla = $listadoSelects[$selectDestino];

if ($tabla == "mnt_farmacia") {
    $conexion = new conexion;
    $conexion->conectar();
    $consulta = pg_query("SELECT * FROM $tabla'") or die(pg_error());
    $conexion->desconectar();

    // Comienzo a imprimir el selec
    echo "<select name='" . $selectDestino . "' id='" . $selectDestino . "' onChange='cargaContenido(this.id)'>";
    echo "<option value='0'>[Seleccione ...]</option>";
    while ($registro = pg_fetch_row($consulta)) {
        // Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
        $registro[1] = htmlentities($registro[1]);
        // Imprimo las opciones del select
        echo "<option value='" . $registro[0] . "'>" . $registro[1] . "</option>";
    }
    echo "</select>";
}



if ($tabla == "mnt_areafarmacia") {
    $conexion = new conexion;
    $conexion->conectar();
    $consulta = pg_query("SELECT mnt_areafarmacia.IdArea,mnt_areafarmacia.Area
						   FROM mnt_areafarmacia
						   inner join mnt_farmacia
						   on mnt_farmacia.IdFarmacia=mnt_areafarmacia.IdFarmacia
						   WHERE mnt_farmacia.IdFarmacia='$opcionSeleccionada'") or die(pg_error());

    $conexion->desconectar();

    // Comienzo a imprimir el select
    echo "<select name='" . $selectDestino . "' id='" . $selectDestino . "' onChange='cargaContenido(this.id)' onmouseover=\"Tip('Selecci&oacute;n de &Aacute;rea')\" onmouseout=\"UnTip()\">";
    echo "<option value='0'>[Seleccione ...]</option>";
    while ($registro = pg_fetch_row($consulta)) {
        // Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
        $registro[1] = htmlentities($registro[1]);
        // Imprimo las opciones del select
        echo "<option value='" . $registro[0] . "'>" . $registro[1] . "</option>";
    }
    echo "</select>";
}


if ($tabla == "mnt_grupoterapeutico") {
    $conexion = new conexion;
    $conexion->conectar();
    $consulta = pg_query("SELECT * FROM $tabla") or die(pg_error());
    $conexion->desconectar();

    // Comienzo a imprimir el select
    echo "<select name='" . $selectDestino . "' id='" . $selectDestino . "' onChange='cargaContenido(this.id)'>";
    echo "<option value='0'>[Seleccione ...]</option>";
    while ($registro = pg_fetch_row($consulta)) {
        // Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
        $registro[1] = htmlentities($registro[1]);
        // Imprimo las opciones del select
        if ($registro[1] != "--") {
            echo "<option value='" . $registro[0] . "'>" . $registro[1] . "</option>";
        }
    }
    echo "</select>";
}



if ($tabla == "farm_catalogoproductos") {
    $conexion = new conexion;
    $conexion->conectar();
    $consulta = pg_query("SELECT $tabla.id as IdMedicina,$tabla.Nombre,$tabla.FormaFarmaceutica, $tabla.Concentracion, Presentacion
						   FROM $tabla
						   inner join mnt_grupoterapeutico 
						   on mnt_grupoterapeutico.Id=$tabla.IdTerapeutico
						   inner join farm_catalogoproductosxestablecimiento fcpe
						   on fcpe.IdMedicina=$tabla.Id
						   WHERE mnt_grupoterapeutico.Id='$opcionSeleccionada' 
                                                   and fcpe.IdEstablecimiento=$IdEstablecimiento
                                                   and fcpe.IdModalidad=$IdModalidad
                                                   order by $tabla.Nombre") or die(pg_error());

    $conexion->desconectar();

    // Comienzo a imprimir el select
    echo "&nbsp;<select name='" . $selectDestino . "' id='" . $selectDestino . "' onChange='cargaContenido(this.id)' onmouseover=\"Tip('Selecci&oacute;n de Medicamento')\" onmouseout=\"UnTip()\">";
    echo "<option value='0'>[Seleccione ...]</option>";
    while ($registro = pg_fetch_row($consulta)) {
        // Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
        $registro[1] = htmlentities($registro[1]);
        // Imprimo las opciones del select
        echo "<option value='" . $registro[0] . "'>" . $registro[1] . ", " . $registro[3] . " - " . htmlentities($registro[2]) . "</option>";
    }
    echo "</select>";
}
?>