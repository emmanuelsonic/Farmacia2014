<?php

require('../../Clases/class.php');

class TransferenciaProceso {
    /* INTRODUCCION DE NUEVA TRANSFERENCIA */

    function ObtenerExistencia($Lote, $Bandera, $IdArea) {
        /* Se usa para obtener su existencia y para obtener el codigo de lote[despliegue del detalle] */
        $querySelect = "select IdExistencia,farm_medicinaexistenciaxarea.Existencia,farm_lotes.Lote
					from farm_medicinaexistenciaxarea
					inner join farm_lotes
					on farm_lotes.IdLote=farm_medicinaexistenciaxarea.IdLote
					where farm_lotes.IdLote='$Lote'
					and IdArea=" . $IdArea . " and Existencia <> 0";
        $resp = pg_fetch_array(pg_query($querySelect));
        if ($Bandera == 1) {
            return($resp);
        } else {
            return($resp);
        }
    }

//ObtenerExistencia

    function ObtenerExistencia2($Lote, $Bandera, $IdArea) {
        /* Se usa para obtener su existencia y para obtener el codigo de lote[despliegue del detalle] */
        $querySelect = "select IdExistencia,farm_medicinaexistenciaxarea.Existencia,farm_lotes.Lote
					from farm_medicinaexistenciaxarea
					inner join farm_lotes
					on farm_lotes.IdLote=farm_medicinaexistenciaxarea.IdLote
					where farm_lotes.IdLote='$Lote'
					and IdArea=" . $IdArea . " ";
        $resp = pg_fetch_array(pg_query($querySelect));
        return($resp);
    }

//ObtenerExistencia

    function ObtenerSiguienteLote($IdMedicina, $Lote, $IdArea) {
        $querySelect = "select farm_lotes.IdLote, Existencia,IdExistencia
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.IdLote
					where farm_lotes.IdLote <> '$Lote'
					and farm_medicinaexistenciaxarea.IdMedicina='$IdMedicina'
					and IdArea=" . $IdArea . "
                                            and Existencia <> 0
					order by FechaVencimiento asc";

        $resp = pg_fetch_array(pg_query($querySelect));
        return($resp);
    }

    function IntroducirAjuste($Cantidad, $IdMedicina, $IdArea, $Acta, $Justificacion, $FechaTransferencia, $IdPersonal, $Lote, $Divisor, $UnidadesContenidas, $Precio, $TipoFarmacia,$FechaVencimiento,$IdEstablecimiento,$IdModalidad) {
        //INGRESO DE EXISTENCIAS POR AJUSTES DE EXTRAVIOS

        //Ingreso de datos generales del Lote a utilizarse
        
        $query="insert into farm_lotes (Lote,PrecioLote,FechaVencimiento,IdEstablecimiento,IdModalidad) 
                                values ('$Lote','$Precio','$FechaVencimiento',$IdEstablecimiento,$IdModalidad)";
        pg_query($query);
        
        $IdLote=pg_insert_id();
        
        if ($TipoFarmacia == 1) {
            $SQL = "insert into farm_entregamedicamento (IdMedicina,Existencia,IdLote,IdEstablecimiento,IdModalidad) 
                                                  values('$IdMedicina','$Cantidad','$IdLote',$IdEstablecimiento,$IdModalidad)";
            pg_query($SQL);
            $IdIngresoExistencia=pg_insert_id();        
            
        } else {

            $SQL = "insert into farm_medicinaexistenciaxarea (IdMedicina,IdArea,Existencia,IdLote,IdEstablecimiento,IdModalidad) 
                                                       values('$IdMedicina','$IdArea','$Cantidad','$IdLote',$IdEstablecimiento,$IdModalidad)";
            pg_query($SQL);
            $IdIngresoExistencia=pg_insert_id();  
            
        }
        
        //INGRESO DE REGISTROS EN farm_ajustes
        
        $SQL2 = "insert into farm_ajustes (ActaNumero,IdMedicina,IdArea,Existencia,IdLote,FechaAjuste,Justificacion,IdPersonal,IdExistencia,FechaHoraIngreso,IdEstablecimiento, IdModalidad) 
                                    values('$Acta','$IdMedicina','$IdArea','$Cantidad','$IdLote','$FechaTransferencia','$Justificacion','$IdPersonal','$IdIngresoExistencia',now(),$IdEstablecimiento,$IdModalidad)";
        pg_query($SQL2);
    }

//Introducir Ajuste

    function ObtenerAjustes($IdPersonal, $Fecha,$IdEstablecimiento,$IdModalidad) {
        /* OBTENCION DE INFORMES INTRODUCIDOS POR EL USUARIO SIN SER FINALIZADOS */
        $querySelect = "select farm_ajustes.Existencia as Cantidad,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion, Presentacion,Descripcion, 
					mnt_areafarmacia.Area,farm_ajustes.Justificacion,ActaNumero,farm_ajustes.IdExistencia,
					farm_ajustes.IdAjuste,farm_lotes.Lote,farm_catalogoproductos.IdMedicina
					from farm_ajustes
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_ajustes.IdMedicina
					inner join mnt_areafarmacia
					on mnt_areafarmacia.IdArea=farm_ajustes.IdArea
					inner join farm_lotes
					on farm_lotes.IdLote=farm_ajustes.IdLote
					inner join farm_unidadmedidas fum
					on fum.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
                                        where farm_ajustes.IdPersonal='$IdPersonal'
					and date(farm_ajustes.FechaAjuste)= '$Fecha'
                                        and farm_ajustes.IdEstablecimiento=$IdEstablecimiento
                                        and farm_ajustes.IdModalidad=$IdModalidad
					and farm_ajustes.IdEstado='X'";
        $resp = pg_query($querySelect);
        return($resp);
    }

//Obtener transferencias

    function NombreArea($IdArea) {
        $querySelect = "select mnt_areafarmacia.Area
					from mnt_areafarmacia
					where mnt_areafarmacia.IdArea='$IdArea'";
        if ($resp = pg_fetch_array(pg_query($querySelect))) {
            return($resp[0]);
        } else {
            return("Otras Areas");
        }
    }

    /* ELIMINAR */

    function EliminarAjustes($IdAjuste,$TipoFarmacia,$IdEstablecimiento,$IdModalidad) {
        //eliminar ajustes
        $query="select IdExistencia,IdLote from farm_ajustes where IdAjuste=".$IdAjuste;
            $row=pg_fetch_array(pg_query($query));
            $IdExistencia=$row["IdExistencia"];
            $IdLote=$row["IdLote"];
            
        if($TipoFarmacia==1){
            $SQL="delete from farm_entregamedicamento where IdEntrega=".$IdExistencia;
        }else{
            $SQL="delete from farm_medicinaexistenciaxarea where IdExistencia=".$IdExistencia;
        }
       
        pg_query($SQL);
        
        $SQL2="delete from farm_lotes where IdLote=".$IdLote;
        pg_query($SQL2);
        
        $SQL3="delete from farm_ajustes where IdAjuste=".$IdAjuste;
        pg_query($SQL3);
        
    }//eliminar ajustes



    /* FINALIZA TODAS LAS TRANSFERENCIAS */

    function FinalizaAjustes($IdPersonal) {
        $queryUpdate = "update farm_ajustes set IdEstado='D' 
                        where IdPersonal='$IdPersonal' and IdEstado='X'";
        pg_query($queryUpdate);
    }

//Receta Lista

    function ObtenerCantidadMedicina($IdPersonal) {
        $querySelect = "select farm_transferencias.Cantidad1,farm_transferencias.Cantidad2,farm_transferencias.IdMedicina,
				farm_transferencias.IdArea as IdArea,farm_transferencias.IdLote,farm_transferencias.IdLote2
				from farm_transferencias
				where farm_transferencias.FechaTransferencia=curdate()
				and farm_transferencias.IdEstado='X'
				and farm_transferencias.IdPersonal='$IdPersonal'";
        $resp = pg_query($querySelect);
        return($resp);
    }

//ObtenerCantidadMedicina

    function ObtenerLotesMedicamento($IdMedicina, $Cantidad, $IdArea, $IdEstablecimiento,$IdModalidad) {
        $querySelect = "select sum(Existencia),farm_lotes.IdLote,
                                     if (left(farm_lotes.FechaVencimiento,7) < left(curdate(),7), 
                                        concat_ws(' ',farm_lotes.Lote,' [Lote Vencido]'), 
                                        farm_lotes.Lote) as Lote, 
                                     farm_lotes.FechaVencimiento
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.IdLote
					where farm_medicinaexistenciaxarea.IdMedicina='$IdMedicina'
					and farm_medicinaexistenciaxarea.Existencia <> 0
                                        and farm_medicinaexistenciaxarea.IdEstablecimiento=$IdEstablecimiento
                                        and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
					and IdArea=" . $IdArea . "
					
					group by farm_lotes.IdLote
					order by farm_lotes.FechaVencimiento";
        $resp = pg_query($querySelect);
        return($resp);
    }

//ObtenerLotesMedicamento

    function ObtenerDetalleLote($IdAjuste) {
        $querySelect = "select Existencia as Cantidad, Lote, fl.IdLote
				from farm_ajustes ft
				inner join farm_lotes fl
				on fl.IdLote = ft.IdLote
					where IdAjuste='$IdAjuste'";
        $resp = pg_fetch_array(pg_query($querySelect));
        return($resp);
    }

//ObtenerDetalleLote

    function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad) {
        $SQL = "select DivisorMedicina from farm_divisores 
                where IdMedicina=" . $IdMedicina." 
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
        $resp = pg_query($SQL);
        return($resp);
    }

    function UnidadesContenidas($IdMedicina) {
        $SQL = "select UnidadesContenidas,Descripcion
		from farm_unidadmedidas fu
		inner join farm_catalogoproductos fcp
		on fcp.IdUnidadMedida = fu.IdUnidadMedida
		where IdMedicina=" . $IdMedicina;
        $resp = pg_fetch_array(pg_query($SQL));
        return($resp[0]);
    }

}

//Clase RecetasProceso
?>