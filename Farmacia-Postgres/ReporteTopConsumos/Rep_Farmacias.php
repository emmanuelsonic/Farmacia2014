<?php
include('../Titulo/Titulo.php');

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
            window.location = '../index.php?Permiso=1';
        </script>
        <?php
    } else {
        $tipoUsuario = $_SESSION["tipo_usuario"];
        $nombre = $_SESSION["nombre"];
        $nivel = $_SESSION["nivel"];
        $nick = $_SESSION["nick"];
        require('../Clases/class.php');
        $IdEstablecimiento = $_SESSION["IdEstablecimiento"];
        $IdModalidad = $_SESSION["IdModalidad"];

//******Generacion del combo principal

        function generaSelect2() { //creacioon de combo para las Regiones
            conexion::conectar();
            if ($_SESSION["TipoFarmacia"] == 1) {
                $comp = " where HabilitadoFarmacia='S'";
            } else {
                $comp = "";
            }
            $consulta = mysql_query("select IdFarmacia,Farmacia
							from mnt_farmacia
				" . $comp);
            conexion::desconectar();
            // Voy imprimiendo el primer select compuesto por los paises
            echo "<select name='IdFarmacia' id='IdFarmacia'>";
            echo "<option value='0'>[Consumo General ...]</option>";
            while ($registro = mysql_fetch_row($consulta)) {
                echo "<option value='" . $registro[0] . "'>" . $registro[1] . "</option>";
            }
            echo "</select>";
        }
        ?>
        <html>
            <head>
        <?php head(); ?>
                <title>Reporte por Farmacias</title>
                <script language="javascript"  src="../ReportesArchives/calendar.js"></script>
                <script language="javascript"  src="../ReportesArchives/validaFechas.js"></script>
                <script language="javascript" src="IncludeFiles/ReporteFarmacias.js"></script>
                <link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
                <style type="text/css">
                    <!--
                    .style4 {font-size: 24px}
                    #Layer41 {position:absolute;
                              left:-199px;
                              top:-39px;
                              width:55px;
                              height:31px;
                              z-index:7;
                    }
                    #Layer71 {position:absolute;
                              left:303px;
                              top:39px;
                              width:596px;
                              height:23px;
                              z-index:5;
                    }
                    .style1 {color:#0000CC; font-size:11px; font-family:Arial, Helvetica, sans-serif}
                    #Layer6 {position:absolute;
                             left:25px;
                             top:105px;
                             width:955px;
                             height:30px
                    }
                    #Layer3 {position:absolute;
                             left:2px;
                             top:190px;
                             width:1001px;
                             height:30px;
                             z-index:6;
                    }
                    #Layer1 {	position:absolute;
                              left:115px;
                              top:268px;
                              width:826px;
                              height:192px;
                              z-index:1;
                    }

                    -->
                </style>
            </head>

            <body>
        <?php Menu(); ?>
                <br><br>
            <center>
                <table width="816" border="0">
                    <tr class="MYTABLE">
                        <td colspan="5" align="center"><strong>MEDICAMENTOS CON MAYOR CONSUMO</strong></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="FONDO"><br></td>
                    </tr>
                    <tr>
                        <td width="280" class="FONDO"><strong>Farmacia: </strong></td>
                        <td width="673" colspan="4" class="FONDO"><?php generaSelect2(); ?></td>
                    </tr>
                    <tr>
                        <td class="FONDO"><strong>Maximo de listado:</strong></td>
                        <td class="FONDO">
                            <select id="TOP" name="TOP">
                                <option value="10">TOP 10</option>
                                <option value="20">TOP 20</option>
                                <option value="30">TOP 30</option>
                                <option value="40">TOP 40</option>
                                <option value="50">TOP 50</option>
                                <option value="60">TOP 60</option>
                            </select>
                        </td>
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
                        <td colspan="5" align="right"><input type="button" id="generar" name="generar" value="Generar Reporte" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099" onClick="javascript:Valida();"></td>
                    </tr>
                    <!-- <tr class="MYTABLE">
                      <td colspan="5" align="right">	
                <input type="button" id="Imprimir" name="imprimir" value="Imprimir" onClick="javascript:Imprimir();">
                                </td>
                    </tr> -->
                </table>

                <div id="Reporte" align="center"></div>
            </center>
        </body>
        </html>
        <?php
    }//Fin de IF nivel == 1
}//Fin de IF isset de Nivel
?>