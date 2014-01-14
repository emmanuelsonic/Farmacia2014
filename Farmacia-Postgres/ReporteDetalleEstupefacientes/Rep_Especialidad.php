<?php include('../Titulo/Titulo.php');
if (!isset($_SESSION["nivel"])) {
    ?>
    <script language="javascript">
        window.location = '../signIn.php';
    </script>
    <?php
} else {
    if (isset($_SESSION["IdFarmacia2"])) {
        $IdFarmacia = $_SESSION["IdFarmacia2"];
    }
    $nivel = $_SESSION["nivel"];
    if (($_SESSION["Reportes"] != 1)) {
        ?>
        <script language="javascript">
            window.location = '../Principal/index.php?Permiso=1';
        </script>
        <?php
    } else {
        $tipoUsuario = $_SESSION["tipo_usuario"];
        $nombre = $_SESSION["nombre"];
        $nivel = $_SESSION["nivel"];
        $nick = $_SESSION["nick"];
        require('../Clases/class.php');
        $conexion = new conexion;

        $IdEstablecimiento = $_SESSION["IdEstablecimiento"];
        $IdModalidad = $_SESSION["IdModalidad"];

//******Generacion del combo principal

        function generaSelect2($IdEstablecimiento,$IdModalidad) { //creacioon de combo para las Regiones
            $conexion = new conexion;
            $conexion->conectar();
            $consulta = pg_query("select * 
                                    from mnt_farmacia  mf
                                    inner join mnt_farmaciaxestablecimiento mfe
                                    on mf.Id=mfe.IdFarmacia
                                    where mfe.HabilitadoFarmacia='S'
                                    and mfe.IdEstablecimiento=$IdEstablecimiento
                                    and mfe.IdModalidad=$IdModalidad
                                    ");
            $conexion->desconectar();
            // Voy imprimiendo el primer select compuesto por los paises
            echo "<select name='farmacia' id='farmacia' onChange='cargaContenido8(this.value,this.id)'>";
            echo "<option value='0'>SELECCIONE UNA FARMACIA</option>";
            while ($registro = pg_fetch_row($consulta)) {
                if ($registro[1] != "--") {
                    echo "<option value='" . $registro[0] . "'>" . $registro[1] . "</option>";
                }
            }
            echo "</select>";
        }

        function ComboEspecialidad($IdEstablecimiento,$IdModalidad) {

            $query = "SELECT distinct msse.IdSubServicioxEstablecimiento,NombreServicio,NombreSubServicio

                    FROM mnt_subservicio mss
                    inner join mnt_subservicioxestablecimiento msse
                    on msse.IdSubServicio=mss.IdSubServicio
                    inner join mnt_servicioxestablecimiento mse
                    on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                    inner join mnt_servicio ms
                    on ms.IdServicio=mse.IdServicio
                    inner join sec_historial_clinico shc
                    on shc.IdSubServicioxEstablecimiento=msse.IdSubServicioxEstablecimiento
                    where msse.IdEstablecimiento=$IdEstablecimiento
                    and msse.IdModalidad=$IdModalidad
                    and mse.IdEstablecimiento=$IdEstablecimiento
                    and mse.IdModalidad=$IdModalidad
                    and shc.IdEstablecimiento=$IdEstablecimiento
                    and shc.IdModalidad=$IdModalidad
                    order by ms.IdServicio
                    ";

            $conexion = new conexion;
            $conexion->conectar();
            $consulta = pg_query($query);
            $conexion->desconectar();

            echo "<select name='IdSubServicio' id='IdSubServicio' onChange='cargaContenido8(this.value,this.id)'>";
            echo "<option value='0'>SELECCIONE UNA ESPECIALIDAD / SERVICIO</option>";
            while ($registro = pg_fetch_row($consulta)) {
                if ($registro[1] != "--") {
                    echo "<option value='" . $registro[0] . "'>[" . $registro[1] . "] " . $registro[2] . "</option>";
                }
            }
            echo "</select>";
        }

        function ComboMedicos($IdEstablecimiento,$IdModalidad) {
            $query = "select distinct mnt_empleado.Id,NombreEmpleado
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
		  <option value="0">TODOS LOS MEDICOS</option>';

            while ($row = pg_fetch_array($resp)) {
                $comboMedico.='<option value="' . $row["id"] . '">' . $row["nombreempleado"] . '</option>';
            }
            $comboMedico.="</select>";

            echo $comboMedico;
        }

        function comboMedicina($IdEstablecimiento,$IdModalidad) {
            $query2 = "select distinct GrupoTerapeutico,farm_catalogoproductos.id as IdMedicina,Codigo,
		 left(farm_catalogoproductos.Nombre,'80') as Nombre,
		left(farm_catalogoproductos.Concentracion,30) as Concentracion
		from farm_catalogoproductos
		inner join farm_catalogoproductosxestablecimiento fcpe
		on fcpe.IdMedicina = farm_catalogoproductos.Id

		inner join mnt_grupoterapeutico
		on mnt_grupoterapeutico.Id=farm_catalogoproductos.IdTerapeutico
		
		where fcpe.IdEstablecimiento = $IdEstablecimiento
		and fcpe.Estupefaciente='S'
                and fcpe.IdModalidad=$IdModalidad";
		//order by mnt_grupoterapeutico.Id,farm_catalogoproductos.Codigo";  -->le quite el order by 


            $conexion = new conexion;
            $conexion->conectar();
            $consulta2 = pg_query($query2);
            $conexion->desconectar();

            $combo = "<select id='IdMedicina' name='IdMedicina'>
	<option value='0'>TODAS LAS MEDICINAS</option>";
            while ($row = pg_fetch_array($consulta2)) {
                $combo.="<option value='" . $row["idmedicina"] . "'>" . $row["codigo"] . " - " . $row["nombre"] . " - " . $row["concentracion"] . "</option>";
            }

            $combo.="</select>";
            return($combo);
        }

//**********
//********** VALIDACION DE FECHAS*********
        /* $fechas = array();
          $fechas = explode("-",$fecha0);
          $ano = intval($fechas[0]);
          $mes = intval($fechas[1]);
          $dia = intval($fechas[2]); */
//*****************
        ?>
        <html>
            <head>
        <?php head(); ?>
                <link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
                <title>...:::Reporte por Especialidades:::...</title>
                <script language="javascript"  src="../ReportesArchives/calendar.js"></script>
                <script language="javascript"  src="../ReportesArchives/validaFechas.js"></script>
                <script type="text/javascript" src="reporte.js"></script>

                <script language="javascript">
                    function confirmacion() {
                        var resp = confirm('Desea Cancelar esta Accion?');
                        if (resp == 1) {
                            window.location = '../IndexReportes.php';
                        }
                    }//confirmacion
                    function valida() {

                        var Ok = true;
                        var form = document.getElementById('formulario');


                        var fechaFin;
                        var fechaInicio;
                        fechaFin = form.fechaFin.value;
                        fechaInicio = form.fechaInicio.value;

                        if (!mayor(fechaInicio, fechaFin)) {
                            Ok = false;
                            alert("La fecha final no puede ser menor que la inicial");
                        }

                        if (Ok == true) {
                            //Llamado de funcion AJAX para la realizacion de Reporte
                            GeneraReporte();
                        }
                    }//valida
                </script>
            </head>
            <body>
        <?php Menu(); ?>
                <br>
                <form action="Reporte_Especialidad.php" method="post" id="formulario" name="formulario" onSubmit="return false;">

                    <table width="65%" border="0">
                        <tr class="MYTABLE">
                            <td colspan="5" align="center"><strong>CONSUMO DE MEDICAMENTOS ESTUPEFACIENTES POR ESPECIALIDAD/MEDICO</strong></td>
                        </tr>
                        <tr><td colspan="5" class="FONDO"><br></td></tr>
                        <tr>
                            <td class="FONDO"><strong>Farmacia: </strong></td>
                            <td colspan="4" class="FONDO"><?php generaSelect2($IdEstablecimiento,$IdModalidad); ?></td>
                        </tr>
                        <tr>
                            <td class="FONDO"><strong>&Aacute;rea: </strong></td>
                            <td colspan="4" class="FONDO">
                                <div id="ComboAreas">
                                    <select name="area" id="area" disabled="disabled">
                                        <option value="0">SELECCIONE UNA AREA</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td  class="FONDO"><strong>Especialidad / Servicio: </strong></td>
                            <td  colspan="4" class="FONDO">

        <?php comboEspecialidad($IdEstablecimiento,$IdModalidad); ?>

                            </td>
                        </tr>
                        <tr>
                            <td class="FONDO"><strong>Medico:</strong></td>
                            <td colspan="4" class="FONDO">
                                <div id="comboMedico">
        <?php ComboMedicos($IdEstablecimiento,$IdModalidad); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="FONDO"><strong>Medicamento:</strong></td>
                            <td colspan="4" class="FONDO">
        <?php echo comboMedicina($IdEstablecimiento,$IdModalidad); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="FONDO"><strong>Fecha de Inicio: </strong></td>
                            <td colspan="4" class="FONDO">
                                <input type="text" name="fechaInicio" id="fechaInicio" readonly="true" onClick="scwShow(this, event);"/>
                            </td>
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
                        <tr><TD colspan="5">&nbsp;</TD></tr>
                    </table>

                </form>
                <br>



                <div id="Respuesta"></div>
            </body>
        </html>
        <?php
    }//Fin de IF nivel == 1
}//Fin de IF isset de Nivel
?>
