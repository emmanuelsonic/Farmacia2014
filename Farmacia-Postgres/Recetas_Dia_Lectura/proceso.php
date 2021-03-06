<?php
session_start();

if (isset($_SESSION["nivel"])) {

    require('../Clases/class.php');
    require('IncludeFiles/DiasClase.php');

    $query = new queries;
    $query2 = new Repetitivas;

    conexion::conectar();
    $IdPersonal = $_SESSION["IdPersonal"];
    $IdArea = $_SESSION["IdArea"];
    $IdReceta = $_REQUEST["IdReceta"];

    pg_query("update farm_recetas set IdEstado='P' where IdReceta='$IdReceta'");
    pg_query("update farm_recetas set IdPersonal='$IdPersonal' where IdReceta='$IdReceta'");


    $respDatos = $query2->ObtenerDatosPacienteRecetaProceso($IdReceta);

    while ($row = pg_fetch_array($respDatos)) {
        //Datos Generales de todos los pacientes.-
        $paciente = $row["NOMBRE"];
        $paciente = htmlentities(strtoupper($paciente));
        $NumeroReceta = $row["NumeroReceta"];
        $expediente = $row["IdNumeroExp"];

        $fechacon = $row["FechaConsulta"];
        $NombreEmpleado = htmlentities($row["NombreEmpleado"]);
        $SubEspecialidad = $row["NombreSubServicio"];
        $Estado = $row["IdEstado"];
        $IdReceta = $row["IdReceta"];
        $Fecha = $row["Fecha"];
        $edad = $row["nac"];
        $FechaDeEntrega = $row["FechaDeEntrega"];
        if ($row["sexo"] == 1) {
            $sexo = "Masculino";
        } else {
            $sexo = "Femenino";
        }
        /* Datos para Link */
        $IdHistorialClinico = $row["IdHistorialClinico"];
        $IdReceta = $row["IdReceta"];
        //$date=date("d-m-Y");
        /*         * ************************* */
        ?>

        <table width="1005">
            <tr><td height="95" align="center">

                    <table class="MYTABLE" width="995" style="color:#000000">
                        <tr class="FONDO"><td colspan="4" align="center"><strong>RECETA No:&nbsp;&nbsp;<?php echo $NumeroReceta; ?>,&nbsp;Fecha de Entrega:&nbsp;<?php echo $FechaDeEntrega; ?></strong></td></tr>
                        <tr>
                            <td width="412"><strong>Expediente:</strong>&nbsp;&nbsp;&nbsp;<?php echo"$expediente"; ?></td>
                            <td width="314"><strong>Fecha de Consulta:&nbsp;</strong>&nbsp;&nbsp;<?php echo"$fechacon"; ?></td>
                            <td width="157">&nbsp;</td>
                            <td width="182"><input type="text" id="expediente" name="expediente" size="25" onkeypress="return acceptNum(event)" value="Digite '/' para la busqueda" />
                                <input type="hidden" id="buscar" name="buscar" value="Buscar"/></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre Paciente:&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo"$paciente"; ?></td>
                            <td colspan="3"><strong>Especialidad:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $SubEspecialidad; //"$date"; ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Edad:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo"$edad"; ?> </td>
                            <td colspan="3"><strong>Nombre Medico:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo"$NombreEmpleado"; ?></td>
                        </tr>
                        <tr>
                            <td height="26"><strong>Sexo:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo"$sexo"; ?> </td>
                            <td colspan="3"><div align="right"><span class="FONDO">
                                        <?php
                                        $RowName = pg_fetch_array($query->NombreTecnico($IdReceta));
                                        $Corr = $RowName["IdPersonal"];
                                        $NombreTecnico = $RowName["NombreTecnico"];
                                        if (!isset($Corr)) {
                                            $Corr = 0;
                                        }
                                        if (!isset($NombreTecnico)) {
                                            $NombreTecnico = "";
                                        }
                                        ?>
                                        <input style="font-size:18px;text-align:center; <?php
                                $par = ($IdReceta % 2 == 0) ? 1 : 0;
                                if ($par == 0) {
                                            ?>background-color:#00FF00; border:#00FF00<?php } else { ?>background-color:#FF9900; border:#FF9900<?php } ?>" value="<?php echo"$NumeroReceta &nbsp;&nbsp;&nbsp;"; ?>....:::: EN PROCESO ::::.... por: <?php echo $NombreTecnico; ?>" readonly="true" name="<?php echo"verde" . $IdHistorialClinico; ?>" id="<?php echo"verde" . $IdHistorialClinico; ?>" type="text" size="65"/>

                                        <input type="button" id="<?php echo"listo" . $IdHistorialClinico; ?>" name="<?php echo"listo" . $IdHistorialClinico; ?>2" value="LISTO" onClick="javascript:Listo(<?php echo $IdReceta; ?>,<?php echo $NumeroReceta; ?>)"/>
                                        <input type="button" name="<?php echo "imprimir" . $IdHistorialClinico; ?>" value="IMPRIMIR" onclick="javascript:popUp('impresion.php?IR=<?php echo $IdReceta; ?>&F=<?php echo $fechacon; ?>&IdArea=<?php echo $IdArea; ?>');"/>
                    <?php
                    ?>
                                    </span></div></td>
                        </tr>
                    </table>
                </td></tr>
            <tr><td align="center">
        <?php
        //Detalles de Receta
        $respDetalles = $query->datosReceta($IdReceta, $IdArea);
        ?>

                    <table class="MYTABLE" width="935" border="1">
                        <tr class="MYTABLE">
                            <td width="58"><div align="center"><strong>Cantidad</strong></div></td>
                            <td width="149"><div align="center"><strong>Nombre de Medicina </strong></div></td>
                            <td width="126"><div align="center"><strong>Concentraci&oacute;n</strong></div></td>
                            <td width="193"><div align="center"><strong>Presentaci&oacute;n</strong></div></td>
                            <td width="231"><strong>Dosificaci&oacute;n</strong></td>
                            <td width="138"><div align="center"><strong>Satisfecho</strong></div></td>
                        </tr>
                        <?php
                        while ($row2 = pg_fetch_array($respDetalles)) {
                            $cantidad = number_format($row2["Cantidad"], 0, '.', '');
                            $NombreMedicina = htmlentities($row2["medicina"]);
                            $Concentracion = $row2["Concentracion"];
                            $forma = $row2["FormaFarmaceutica"];
                            $presentacion = $row2["Presentacion"];
                            $Presentacion = $row2["FormaFarmaceutica"] . ", " . $row2["Presentacion"];
                            $dosis = htmlentities($row2["Dosis"]);
                            $idmedicina = $row2["IdMedicina"];
                            $IdReceta = $row2["IdReceta"];
                            $EstadoMedicina = $row2["IdEstado"];
                            ?>
                            <tr class="FONDO2" <?php if ($EstadoMedicina == 'I') { ?> style="background-color:#FF6633"<?php }//IF ESTADOMEDICINA?>>
                                <td align="center"><?php echo"$cantidad"; ?></td>
                                <td align="center"><a onClick="ActualizaMedicina(<?php echo $IdReceta; ?>,<?php echo $idmedicina; ?>);"><?php echo"$NombreMedicina"; ?></a></td>
                                <td align="center"><?php echo"$Concentracion"; ?></td>
                                <td><?php echo htmlentities($Presentacion); ?></td>
                                <td><?php echo"$dosis"; ?></td>
                                <td align="center"><?php
                        $combo = $IdHistorialClinico . $idmedicina; //Nombre generico del ComboBox 
                        $NombreIdReceta = 'IdReceta' . $IdHistorialClinico;
                        ?>
                                    <input type="hidden" id="<?php echo $NombreIdReceta; ?>" name="<?php echo $NombreIdReceta; ?>" value="<?php echo $IdReceta; ?>" />
                                    <?php if ($EstadoMedicina == 'I') { ?>
                                        <select id="<?php echo "$combo"; ?>" name="<?php echo "$combo"; ?>" onchange="actualizaEstado(<?php echo $IdHistorialClinico; ?>,<?php echo $idmedicina; ?>,this.value)">
                                            <option value="NO" selected="selected">NO</option>
                                            <option value="SI">SI</option>
                                        </select> 

                                        <?php } else { ?>
                                        <select id="<?php echo "$combo"; ?>" name="<?php echo "$combo"; ?>" onchange="actualizaEstado(<?php echo $IdHistorialClinico; ?>,<?php echo $idmedicina; ?>,this.value)">
                                            <option value="SI" selected="selected">SI</option>
                                            <option value="NO">NO</option>
                                        </select>
            <?php } //ESTADO MEDICINA ?>
                                </td>
                            </tr>

        <?php } //fin de while ?>

                        <tr class="MYTABLE">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="left">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                        </tr>

                    </table>
                </td></tr>
            <tr><td><hr style="color:#FF0000"></td></tr>
        </table>


        <?php
        echo"<br>";
    }//fin de while respDatos
    conexion::desconectar();
} else {
    echo "ERROR_SESSION";
}
?>