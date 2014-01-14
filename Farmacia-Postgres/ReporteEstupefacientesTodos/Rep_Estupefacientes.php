<?php include('../Titulo/Titulo.php');
if (!isset($_SESSION["nivel"])) {
    ?><script language="javascript">
        window.location = '../signIn.php';
    </script>
    <?php
} else {
    if (isset($_SESSION["IdFarmacia2"])) {
        $IdFarmacia = $_SESSION["IdFarmacia2"];
    }
    $nivel = $_SESSION["nivel"];
    if (($_SESSION["Reportes"] != 1)) {
        ?><script language="javascript">
            window.location = '../Principal/index.php?Permiso=1';
        </script>
        <?php
    } else {
        $tipoUsuario = $_SESSION["tipo_usuario"];
        $nombre = $_SESSION["nombre"];
        $nivel = $_SESSION["nivel"];
        $nick = $_SESSION["nick"];
        $IdEstablecimiento = $_SESSION["IdEstablecimiento"];
        $IdModalidad = $_SESSION["IdModalidad"];
        require('../Clases/class.php');

//******Generacion del combo principal

        function generaSelect2($IdEstablecimiento,$IdModalidad) {//creacioon de combo para las Regiones
            conexion::conectar();
            $consulta = pg_query("select msse.IdSubServicioxEstablecimiento,NombreServicio,NombreSubServicio 
				from mnt_subservicio mss
				inner join mnt_subservicioxestablecimiento msse
				on msse.IdSubServicio=mss.IdSubServicio
                                inner join mnt_servicioxestablecimiento mse
                                on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
				inner join mnt_servicio ms
				on ms.IdServicio=mse.IdServicio
				where msse.IdEstablecimiento = $IdEstablecimiento
                                and msse.IdModalidad=$IdModalidad
                                and mse.IdEstablecimiento=$IdEstablecimiento
                                and mse.IdModalidad=$IdModalidad
				and msse.CodigoFarmacia is not null
				order by mse.IdServicio,NombreSubServicio");
            conexion::desconectar();
            // Voy imprimiendo el primer select compuesto por los paises
            echo "<select name='IdSubServicio' id='IdSubServicio'>";
            echo "<option value='0'>[General ...]</option>";
            while ($registro = pg_fetch_row($consulta)) {

                echo "<option value='" . $registro[0] . "'>[" . $registro[1] . '] ' . $registro[2] . "</option>";
            }
            echo "</select>";
        }

        function ComboMedicos($IdEstablecimiento,$IdModalidad) {
            $query = "select distinct mnt_empleado.id,NombreEmpleado
                        from mnt_empleado
                        inner join sec_historial_clinico
                        on sec_historial_clinico.IdEmpleado=mnt_empleado.IdEmpleado

                        where NombreEmpleado is not null
                        and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                        and sec_historial_clinico.IdModalidad=$IdModalidad
                        order by NombreEmpleado";

            $conexion = new conexion;
            $conexion->conectar();
            $resp = pg_query($query);
            $conexion->desconectar();

            $comboMedico = '<select name="IdEmpleado" id="IdEmpleado">
		  <option value="0">[General ...]</option>';

            while ($row = pg_fetch_array($resp)) {
                $comboMedico.='<option value="' . $row["id"] . '">' . $row["nombreempleado"] . '</option>';
            }
            $comboMedico.="</select>";

            echo $comboMedico;
        }

        function comboFarmacia($IdEstablecimiento,$IdModalidad) {
            conexion::conectar();
            if ($_SESSION["TipoFarmacia"] == 1) {
                $comp = " and mfe.HabilitadoFarmacia='S'";
            } else {
                $comp = "";
            }
            $consulta = pg_query("select mf.id as IdFarmacia,Farmacia
                                        from mnt_farmacia mf
                                        inner join mnt_farmaciaxestablecimiento mfe
                                        on mfe.IdFarmacia=mf.Id
                                        where mfe.IdEstablecimiento=$IdEstablecimiento
                                        and mfe.IdModalidad=$IdModalidad
				" . $comp);
            conexion::desconectar();
            // Voy imprimiendo el primer select compuesto por los paises
            echo "<select name='IdFarmacia' id='IdFarmacia'>";
            echo "<option value='0'>[Consumo General ...]</option>";
            while ($registro = pg_fetch_row($consulta)) {
                echo "<option value='" . $registro[0] . "'>" . $registro[1] . "</option>";
            }
            echo "</select>";
        }
        ?>
        <html>
            <head>
        <?php head(); ?>
                <link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
                <title>...:::Reporte por Grupo Terapeutico:::...</title>
                <script language="javascript"  src="../ReportesArchives/calendar.js"></script>
                <script language="javascript" src="reporte.js"></script>
                <script language="javascript"  src="../ReportesArchives/validaFechas.js"></script>
                <script language="javascript">
                    function confirmacion() {
                        var resp = confirm('Desea Cancelar esta Acci�n?');
                        if (resp == 1) {
                            window.location = '../IndexReportes.php';
                        }
                    }//confirmacion
                    function valida() {
                        var form = document.getElementById('formulario');
                        var Ok = true;

                        var fechaFin;
                        var fechaInicio;
                        fechaFin = form.fechaFin.value;
                        fechaInicio = form.fechaInicio.value;

                        if (!mayor(fechaInicio, fechaFin)) {
                            Ok = false;
                            alert("La fecha final no puede ser menor que la inicial");
                        }

                        if (Ok == true) {
                            Reportes();
                        }

                    }//validaand Estupefaciente = 'S'
                </script>
            </head>
            <body>
        <?php Menu(); ?>
                <br>
                <form action="Reporte_Servicios.php" method="post" id="formulario" name="formulario">

                    <table width="816" border="0">
                        <tr class="MYTABLE">
                            <td colspan="5" align="center">
                                <strong>CONSUMO DE MEDICAMENTOS ESTUPEFACIENTES</strong><strong></strong></td>
                        </tr>
                        <tr><td colspan="5" class="FONDO"><br></td></tr>
                        <tr>
                            <td width="280" class="FONDO">
                                <strong>Farmacia:</strong></td>
                            <td width="673" colspan="4" class="FONDO">
                                    <?php comboFarmacia($IdEstablecimiento,$IdModalidad); ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="280" class="FONDO">
                                <strong>Especialidad/</strong><strong>Servicio:</strong></td>
                            <td width="673" colspan="4" class="FONDO"><?php generaSelect2($IdEstablecimiento,$IdModalidad); ?></td>
                        </tr>
                        <tr>
                            <td width="280" class="FONDO">
                                <strong>Medico:</strong></td>
                            <td width="673" colspan="4" class="FONDO"><?php ComboMedicos($IdEstablecimiento,$IdModalidad); ?></td>
                        </tr>  
                        <tr>
                            <td width="280" class="FONDO"><strong>Grupo Terapeutico: </strong></td>
                            <td width="673" colspan="4" class="FONDO">
                                <select name="IdTerapeutico" id="IdTerapeutico" onChange='cargaContenido(this.value, this.id)'>
                                    <option value="0">[General ...]</option>
                                    <?php
                                    conexion::conectar();
                                    $consulta = pg_query("SELECT distinct mnt_grupoterapeutico.* FROM mnt_grupoterapeutico
						inner join farm_catalogoproductos fcp
						on fcp.IdTerapeutico=mnt_grupoterapeutico.Id
						inner join farm_catalogoproductosxestablecimiento fcpe
						on fcpe.IdMedicina=fcp.Id
						where fcpe.Estupefaciente='S'
						order by mnt_grupoterapeutico.Id") or die(pg_error());
                                    conexion::desconectar();
                                    while ($registro = pg_fetch_row($consulta)) {
                                        // Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
                                        $registro[1] = htmlentities($registro[1]);
                                        // Imprimo las opciones del select
                                        if ($registro[1] != "--") {
                                            ?>
                                            <option value='<?php echo $registro[0]; ?>'><?php echo $registro[0] . ' - ' . $registro[1]; ?></option>
                                            <?php
                                        }
                                    }  //while
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="FONDO"><strong>Medicina:</strong></td>
                            <td colspan="4" class="FONDO"><div id="ComboMedicinas"><select name="IdMedicina" id="IdMedicina" disabled="disabled">
                                        <option value="0">[Seleccione ...]</option>
                                    </select></div></td>
                        </tr>
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
                </form>
                <br>
                <div id="Layer2"></div>
            </body>
        </html>
        <?php
    }//Fin de IF nivel == 1
}//Fin de IF isset de Nivel
?>
