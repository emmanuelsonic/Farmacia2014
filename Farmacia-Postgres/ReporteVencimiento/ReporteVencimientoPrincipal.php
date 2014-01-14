<?php include('../Titulo/Titulo.php');

if (!isset($_SESSION["Administracion"])) {
    ?>
    <script language="JavaScript">
        alert('Debe iniciar sesion!');
        window.location = '../signIn.php';
    </script>
    <?php
}

if ($_SESSION["Administracion"] != 1) {
    ?>
    <script language="JavaScript">
        alert('No posee suficientes privilegios para utilizar esta herramienta!');
        window.location = '../IngresoRecetasTodas/IntroduccionRecetasPrincipal.php';
    </script>
    <?php
}
include('../Clases/class.php');

$IdEstablecimiento = $_SESSION["IdEstablecimiento"];
$IdModalidad = $_SESSION["IdModalidad"];

function ComboFarmacias($IdEstablecimiento, $IdModalidad) {
    conexion::conectar();
    $SQL = "select id as IdFarmacia,Farmacia from mnt_farmacia where HabilitadoFarmacia ='S'";
    $resp = pg_query($SQL);
    conexion::desconectar();

    $combo = "<select id='IdFarmacia' name='IdFarmacia' onchange='CargarAreas(this.value);'>
		<option value='0'>[GENERAL...]</option>";
    while ($row = pg_fetch_array($resp)) {
        $combo.="<option value='" . $row["idfarmacia"] . "'>" . $row["farmacia"] . "</otion>";
    }
    $combo.="</select>";
    return($combo);
}

function ComboTerapeutico() {
    conexion::conectar();
    $SQL = "select id as IdTerapeutico,GrupoTerapeutico from mnt_grupoterapeutico where GrupoTerapeutico <> '--'";
    $resp = pg_query($SQL);
    conexion::desconectar();

    $combo = "<select id='IdTerapeutico' name='IdTerapeutico' onchange='CargarMedicinas(this.value);'>
		<option value='0'>[GENERAL...]</option>";
    while ($row = pg_fetch_array($resp)) {
        $combo.="<option value='" . $row["idterapeutico"] . "'>" . $row["idterapeutico"] . ' - ' . $row["grupoterapeutico"] . "</option>";
    }
    $combo.="</select>";
    return($combo);
}
?>

<html>

    <head><title>Reporte de medicamento proximo a vencer</title>
        <link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
        <script language="JavaScript" src="IncludeFiles/ManttoFarmacia.js"></script>
        <script language="JavaScript" src="../trim.js"></script>
        <script language="javascript"  src="../ReportesArchives/calendar.js"></script>
        <script language="javascript"  src="../ReportesArchives/validaFechas.js"></script>
        <script language="JavaScript" src="IncludeFiles/reporte.js"></script>
        <?php head(); ?>
    </head>
    <?php Menu(); ?>
    <br><br>

    <center>
        <form id="formulario">
            <table width="50%">
                <tr class="MYTABLE"><td align="center" colspan="2"><strong>REPORTE DE MEDICAMENTOS PROXIMOS A VENCER</strong></td></tr>


                <tr><td class="FONDO"><strong>Grupo Terapeutico:</strong> </td><td class="FONDO"><?php echo ComboTerapeutico(); ?></td></tr>
                <tr><td class="FONDO"><strong>Medicina:</strong> </td><td class="FONDO"><div id="ComboMedicina"><select id="IdMedicina" name="IdMedicina"><option value="0">[GENERAL...]</option></select></div></td></tr>
                <tr>
                    <td class="FONDO"><strong>Fecha de Inicio: </strong></td>
                    <td colspan="4" class="FONDO"><input type="text" name="fechaInicio" id="fechaInicio" readonly="true" onClick="scwShow(this, event);" /></td>
                </tr>
                <tr>
                    <td class="FONDO"><strong>Fecha de Finalizaci&oacute;n: </strong></td>
                    <td colspan="4" class="FONDO"><input type="text" name="fechaFin" id="fechaFin" readonly="true" onClick="scwShow(this, event);"/></td>
                </tr>
                <tr>
                    <td colspan="5" class="FONDO">&nbsp;</td>
                </tr>
                <tr class="MYTABLE">
                    <td colspan="5" align="right"><input type="button" name="generar" value="Generar Reporte" onclick="valida();"></td>
                </tr>
            </table>

            <div id="Reporte"></div>

        </form>

    </center>
</html>
