<?php
require('../../Clases/class.php');

class RecetasProceso{
/*ACTUALIZACION DE HISTORIAL CLINICO*/
	function IntroducirHistorialClinico($Expediente,$IdMedico,$IdSubEspecialidad,$FechaConsulta){
		$IdSubServicio=RecetasProceso::ObtenerIdSubServicio($IdSubEspecialidad);
		$queryInsert="insert into sec_historial_clinico (IdNumeroExp,FechaConsulta,IdEmpleado,IdSubServicio,IdSubEspecialidad,Piloto) values('$Expediente','$FechaConsulta','$IdMedico','$IdSubServicio','$IdSubEspecialidad','V')";
		$querySelect="select IdHistorialClinico 
						from sec_historial_clinico
						where IdNumeroExp='$Expediente'
						order by IdHistorialClinico desc limit 1";
		
		pg_query($queryInsert);
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);		
	}//Introducir Historial Clinico

	function ObtenerIdSubServicio($IdSubServicio){
		$querySelect="select mnt_subservicio.IdSubServicio
					from mnt_subservicio
					
					where IdSubServicio='$IdSubServicio'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);	
	}//Obtener IdSubServicio


/* FIN HISTORIAL CLINICO */

	function Correlativo($Fecha){
		$querySelect="select farm_recetas.NumeroReceta
					from farm_recetas
					where farm_recetas.Fecha='$Fecha'
					and (farm_recetas.IdEstado<>'RE' and farm_recetas.IdEstado<>'ER')
					order by farm_recetas.NumeroReceta desc
					limit 1";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}

	function ObtenerIdReceta($IdHistorialClinico){
		$querySelect="select farm_recetas.IdReceta
					from farm_recetas
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdEstado='XX'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	function ObtenerExpediente($Expediente){
		$querySelect="select mnt_expediente.IdNumeroExp,mnt_datospaciente.PrimerNombre,
					mnt_datospaciente.PrimerApellido,mnt_expediente.IdNumeroExp
					from  mnt_expediente
					inner join mnt_datospaciente
					on mnt_datospaciente.IdPaciente=mnt_expediente.IdPaciente
					where mnt_expediente.IdNumeroExp='$Expediente'";
		//Obtenemos el ultimo IdHistorialClinico del Paciente
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//ObtenerExpediente
	
	
	function IntroducirRecetaNueva($IdHistorialClinico,$IdMedico,$IdPersonal,$Fecha,$IdArea){
		$Correlativo=RecetasProceso::Correlativo($Fecha);
		$Correlativo++;
		$queryInsert=" insert into farm_recetas(IdHistorialClinico,Fecha,IdEstado,IdArea,NumeroReceta,IdPersonalIntro) values('$IdHistorialClinico','$Fecha','XX','$IdArea','$Correlativo','$IdPersonal')";
		pg_query($queryInsert);
	}//Intro Receta Nueva
	
	
		function IntroducirRecetaNuevaRepetitiva($IdHistorialClinico,$IdMedico,$IdPersonal,$Fecha,$IdArea){
		$Correlativo=RecetasProceso::Correlativo($Fecha);
		if($Correlativo==NULL){$Correlativo=0;}
		$Correlativo++;
		$queryInsert=" insert into farm_recetas(IdHistorialClinico,Fecha,IdEstado,IdArea,NumeroReceta,IdPersonalIntro) values('$IdHistorialClinico','$Fecha','RE','$IdArea','$Correlativo','$IdPersonal')";
		pg_query($queryInsert);
	}//Intro Receta Nueva

	
/*	ELIMINAR RECETA E HISTORIA CLINICA	*/	
	
	function EliminarReceta($IdHistorialClinico,$IdPersonal,$IdReceta){
		/****	RECUPERACION DE EXISTENCIAS	 ****/
		$querySelect1="select IdArea from farm_recetas where IdReceta=".$IdReceta;
		$IdArea=pg_fetch_array(pg_query($querySelect1));
		if($IdArea[0]==2){
		/****************************************************/
		/*				AREA CONSULTA EXTERNA				*/
		/****************************************************/
			/********************************************************/
			/*							LOTE 1						*/
			/********************************************************/
		
			$querySelect2="select IdMedicinaRecetada,IdEstado from farm_medicinarecetada where IdReceta=".$IdReceta;
			$resp2=pg_query($querySelect2);
			while($row2=pg_fetch_array($resp2)){
				$querySelect3="select CantidadLote1, Lote1, CantidadLote2, Lote2
							from farm_medicinarecetada
							where IdMedicinaRecetada=".$row2["IdMedicinaRecetada"];
				$resp3=pg_fetch_array(pg_query($querySelect3));
				$CantidadLote1=$resp3["CantidadLote1"];
				$IdLote1=$resp3["Lote1"];
				$CantidadLote2=$resp3["CantidadLote2"];
				$IdLote2=$resp3["Lote2"];
				$IdEstado=$row2["IdEstado"];
				/***************	LOTE 1	*******************/
				if($IdEstado=='S'){
					$querySelect4="select Existencia
								from farm_medicinaexistenciaxarea
								where IdLote=".$IdLote1;
					$respExistencia1=pg_fetch_array(pg_query($querySelect4));
					$Existencia_new1=$CantidadLote1+$respExistencia1["Existencia"];
					
					$queryUpdate1="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new1' where IdLote=".$IdLote1;
						pg_query($queryUpdate1);
					
					/***************	LOTE 2	*******************/
						if($IdLote2!='' and $IdLote2!=NULL and $IdLote2!='0'){
							$querySelect4="select Existencia
										from farm_medicinaexistenciaxarea
										where IdLote=".$IdLote2;
							$respExistencia2=pg_fetch_array(pg_query($querySelect4));
							$Existencia_new2=$CantidadLote2+$respExistencia2["Existencia"];
							
							$queryUpdate2="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new2' where IdLote=".$IdLote2;
								pg_query($queryUpdate2);
						}//IdLote2 != NULL
				}//IDEstado=='S'
			}//While
				
		}else{
		/****************************************************/
		/*					LAS DEMAS AREAS					*/
		/****************************************************/
			$querySelect2="select IdMedicinaRecetada,IdEstado from farm_medicinarecetada where IdReceta=".$IdReceta;
			$resp2=pg_query($querySelect2);
			while($row2=pg_fetch_array($resp2)){
				$querySelect3="select CantidadLote1, Lote1, CantidadLote2, Lote2
							from farm_medicinarecetada
							where IdMedicinaRecetada=".$row2["IdMedicinaRecetada"];
				$resp3=pg_fetch_array(pg_query($querySelect3));
				$CantidadLote1=$resp3["CantidadLote1"];
				$IdLote1=$resp3["Lote1"];
				$CantidadLote2=$resp3["CantidadLote2"];
				$IdLote2=$resp3["Lote2"];
				$IdEstado=$row2["IdEstado"];
				if($IdEstado=='S'){
					/***************	LOTE 1	*******************/
					$querySelect4="select Existencia
								from farm_entregamedicamento
								where IdLote=".$IdLote1;
					$respExistencia1=pg_fetch_array(pg_query($querySelect4));
					$Existencia_new1=$CantidadLote1+$respExistencia1["Existencia"];
					
					$queryUpdate1="update farm_entregamedicamento set Existencia='$Existencia_new1' where IdLote=".$IdLote1;
						pg_query($queryUpdate1);
					
	
					/***************	LOTE 2	*******************/
					if($IdLote2!='' and $IdLote2!=NULL and $IdLote2!='0'){
						$querySelect4="select Existencia
									from farm_entregamedicamento
									where IdLote=".$IdLote2;
						$respExistencia2=pg_fetch_array(pg_query($querySelect4));
						$Existencia_new2=$CantidadLote2+$respExistencia2["Existencia"];
						
						$queryUpdate2="update farm_entregamedicamento set Existencia='$Existencia_new2' where IdLote=".$IdLote2;
							pg_query($queryUpdate2);
					}//IdLote != NULL
				}//IDEstado==S
			}//While
	
				
		}//ELSE AREA = 2
		
		
		/*****************		ELIMINACION DE RECETA Y DETALLE		***********************/
		$queryDeleteReceta="delete from farm_recetas where farm_recetas.IdReceta='$IdReceta'";
		$querySelect="select * from farm_medicinarecetada where farm_medicinarecetada.IdReceta='$IdReceta'";
		$queryDeleteMedicina="delete from farm_medicinarecetada where farm_medicinarecetada.IdReceta='$IdReceta'";
		$queryDeleteHistorialClinico="delete from sec_historial_clinico where sec_historial_clinico.IdHistorialClinico='$IdHistorialClinico'";
		
		
		$resp=pg_fetch_array(pg_query($querySelect));
			
		if($resp!=NULL){
		pg_query($queryDeleteMedicina);
		}		
		pg_query($queryDeleteReceta);


		$resp2=RecetasProceso::ObtenerIdRecetaRepetitivaEliminar($IdHistorialClinico,$IdPersonal);
		
		while($row=pg_fetch_array($resp2)){
			$IdReceta2=$row["IdReceta"];
			$queryDeleteReceta2="delete from farm_recetas where farm_recetas.IdReceta='$IdReceta2'";
			$querySelect2="select * from farm_medicinarecetada where farm_medicinarecetada.IdReceta='$IdReceta2'";
			$queryDeleteMedicina2="delete from farm_medicinarecetada where farm_medicinarecetada.IdReceta='$IdReceta2'";

			$respuesta=pg_fetch_array(pg_query($querySelect2));
				if($respuesta!=NULL){
					pg_query($queryDeleteMedicina2);
				}		
			pg_query($queryDeleteReceta2);
		}//fin de while
		pg_query($queryDeleteHistorialClinico);
				
	}//Eliminar Receta



	function ObtenerIdRecetaRepetitivaEliminar($IdHistorialClinico,$IdPersonal){
		$querySelect="select farm_recetas.IdReceta
					from farm_recetas
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdEstado='RE'
					and IdPersonalIntro='$IdPersonal'";
		$resp=pg_query($querySelect);
		return($resp);
			
	}//ObtenerIdRecetaRepetitivaEliminar



/* DETALLE DE RECETA */
	function IntroducirMedicinaPorReceta($IdReceta,$IdMedicina,$Cantidad,$Dosis,$Satisfecha,$Fecha,$IdEstablecimiento,$IdModalidad){
		$queryInsert="insert into farm_medicinarecetada(IdReceta,IdMedicina,Cantidad,Dosis,FechaEntrega,IdEstado,IdEstablecimiento,IdModalidad) 
                                                         values('$IdReceta','$IdMedicina','$Cantidad','$Dosis','$Fecha','$Satisfecha',$IdEstablecimiento,$IdModalidad)";
		pg_query($queryInsert);		
		
		$IdMedicinaRecetada=pg_insert_id();
		return($IdMedicinaRecetada);
		
		
	}//Introducir Medicina Receta

	
	function ObtenerFecha($intervalo){
	$querySelect="select adddate(curdate(), interval +".$intervalo." month)";
	$resp=pg_fetch_array(pg_query($querySelect));
	return($resp[0]);
	}
	
	
	
	function ObtenerMedicinaIntroducida($IdReceta,$IdEstablecimiento,$IdModalidad){
		$querySelect="SELECT farm_recetas.IdHistorialClinico, FechaConsulta, farm_medicinarecetada.*,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,IdArea,FormaFarmaceutica,Presentacion,IdFarmacia,
                                (SELECT	string_agg(lote, '-')
                                 FROM farm_lotes fl INNER JOIN farm_medicinadespachada fmd ON fl.Id=fmd.IdLote 
                                 WHERE fmd.IdMedicinaRecetada=farm_medicinarecetada.Id) AS Lotes                    

                                 FROM farm_medicinarecetada
                                 INNER JOIN farm_catalogoproductos
                                 ON farm_catalogoproductos.Id=farm_medicinarecetada.IdMedicina
                                 INNER JOIN farm_recetas
                                 ON farm_recetas.Id=farm_medicinarecetada.IdReceta
                                 INNER JOIN sec_historial_clinico
                                 ON sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
                                 WHERE (farm_recetas.numeroreceta='$IdReceta' OR farm_recetas.CorrelativoAnual = '$IdReceta')
                                 AND sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                                 AND farm_recetas.IdModalidad=$IdModalidad
                                 ORDER BY farm_medicinarecetada.Id DESC";
		$resp=pg_query($querySelect);
		return($resp);
	}//Obtener Medicina Introducida
	
	
	function ObtenerDatosGenerales($IdReceta,$IdEstablecimiento,$IdModalidad){
		$query="select farm_recetas.IdArea,
                            (select Area from mnt_areafarmacia where IdArea=farm_recetas.IdArea) as Area, 
                            NombreEmpleado, mss.NombreSubServicio,

                            (   select NombreServicio 
                                from mnt_servicio ms 
                                inner join mnt_servicioxestablecimiento mse on ms.IdServicio=mse.IdServicio 
                                inner join mnt_subservicioxestablecimiento msse on msse.IdServicioxEstablecimiento = mse.IdServicioxEstablecimiento 
                                inner join mnt_subservicio mss on mss.IdSubServicio = msse.IdSubServicio
                                where   msse.IdEstablecimiento=$IdEstablecimiento and mse.IdServicio != 'CONBMG' and msse.IdModalidad=$IdModalidad 
                                        and msse.IdSubServicioxEstablecimiento = sec_historial_clinico.IdSubServicioxEstablecimiento
                            )  as Origen,

                            case farm_recetas.IdFarmacia when 1 then 'Central' when 2 then 'Con. Externa' when 3 then 'Emergencias' when 4 then 'Bodega' end as NombreFarmacia, IdAreaOrigen,
                            (select Area from mnt_areafarmacia where IdArea=farm_recetas.IdAreaOrigen) as AreaOrigen,

                            IdNumeroExp,

                            (   SELECT CONCAT_WS(' ', a.PrimerNombre,a.SegundoNombre,a.TercerNombre,a.PrimerApellido,a.SegundoApellido) AS Nombre 
                                FROM mnt_datospaciente a 
                                INNER JOIN mnt_expediente b ON a.IdPaciente = b.IdPaciente 
                                WHERE b.IdNumeroExp=sec_historial_clinico.IdNumeroExp
                            ) AS Nombre

                    from farm_recetas
                    inner join sec_historial_clinico on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
                    inner join mnt_empleados on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
                    inner join mnt_subservicioxestablecimiento on mnt_subservicioxestablecimiento.IdSubServicioxEstablecimiento=sec_historial_clinico.IdSubServicioxEstablecimiento
                    inner join mnt_subservicio mss on mss.IdSubServicio= mnt_subservicioxestablecimiento.IdSubServicio

                    where IdReceta=".$IdReceta."
                    and sec_historial_clinico.IdEstablecimiento=".$IdEstablecimiento."
                    and farm_recetas.IdModalidad=$IdModalidad";
		
                $resp=pg_fetch_array(pg_query($query));
		return($resp);		
	}
	
	
	function CambiarEstado($IdReceta,$IdEstablecimiento,$IdModalidad){
		$query="update farm_recetas set IdEstado='XX' 
                        where (IdReceta like '".$IdReceta."' or CorrelativoAnual='$IdReceta')
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		pg_query($query);
	}
	
	
	function RecetaLista($IdReceta,$IdEstablecimiento,$IdModalidad){
		$queryUpdate="update farm_recetas set IdEstado='E' 
                              where IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento
                              and IdModalidad=$IdModalidad";
		pg_query($queryUpdate);
	}//Receta Lista


	function ObtenerIdRecetaRepetitiva($IdHistorialClinico,$Fecha){
		$querySelect="select farm_recetas.IdReceta
					from farm_recetas
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdEstado='RE'
					and farm_recetas.Fecha='$Fecha'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//Obtener ID Receta

	function ObtenerRecetaRepetitiva($IdHistorialClinico,$IdPersonal){
		$querySelect="select farm_medicinarecetada.Cantidad,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,
					farm_recetas.Fecha,farm_medicinarecetada.*
					from farm_recetas
					inner join farm_medicinarecetada
					on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdPersonalIntro='$IdPersonal'
					and farm_recetas.IdEstado='RE'
					order by farm_recetas.Fecha";
		$resp=pg_query($querySelect);
		return($resp);
	
	}

	function EliminarMedicinaRecetada($IdMedicinaRecetada,$IdEstablecimiento,$IdModalidad){
			
		$queryDelete="delete from farm_medicinarecetada 
                              where IdMedicinaRecetada='$IdMedicinaRecetada'
                              and IdEstablecimiento=$IdEstablecimiento
                              and IdModalidad=$IdModalidad";
		pg_query($queryDelete);		
		
		
	}//EliminarMedicinaRecetada
	
	

	function UpdateDosis($IdMedicinaRecetada,$Dosis){
		$queryUpdate="update farm_medicinarecetada set Dosis='$Dosis' where IdMedicinaRecetada='$IdMedicinaRecetada'";
		pg_query($queryUpdate);		
	}//UpdateDosis

	function UpdateCantidad($IdMedicinaRecetada,$Cantidad){
		$querySelect1="select Cantidad,IdEstado from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
		$rowCantidad=pg_fetch_array(pg_query($querySelect1));
		$querySelect2="select IdArea
					from farm_recetas
					inner join farm_medicinarecetada
					on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
					where IdMedicinaRecetada=".$IdMedicinaRecetada;
		$IdArea=pg_fetch_array(pg_query($querySelect2));
		
		if($IdArea[0]==2){
		$querySelect3="select IdMedicina, CantidadLote1,Lote1, CantidadLote2,Lote2 from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
		$resp1=pg_fetch_array(pg_query($querySelect3));
        $IdMedicina=$resp1["IdMedicina"];
		$CantidadLote1=$resp1["CantidadLote1"];
		$IdLote1=$resp1["Lote1"];
		$CantidadLote2=$resp1["CantidadLote2"];
		$IdLote2=$resp1["Lote2"];
		
			if($rowCantidad[1]=='S'){
				if($rowCantidad[0] < $Cantidad){
					/*	SUMA DE CANTIDAD, RESTA EN EXISTENCIAS	*/
					$Diferencia=$Cantidad-$rowCantidad[0]; 	//Diferencia es lo que falta para llegar al nuevo numero de 
															//medicamento

					if($IdLote2!='' and $IdLote2!=NULL and $IdLote2!='0'){
					/*		SE UTILIZO UN LOTE SECUNDARIO		*/
						$querySelect4="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote2;
						$rowExistencia=pg_fetch_array(pg_query($querySelect4));
						
					/*		ACTUALIZACION DE EXISTENCIAS		*/
					if($rowExistencia[0]!=0){
						$Existencia_new=$rowExistencia[0]-$Diferencia;
				$UpdateExistencia="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new' where IdLote=".$IdLote2;
						pg_query($UpdateExistencia);
												
					/*		ACTUALIZACION DE LA CANTIDAD DE MEDICAMENTOS	*/							
						$NuevaCantidad=$CantidadLote2+$Diferencia;
				$UpdateCantidadLote2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidad' where IdMedicinaRecetada=".$IdMedicinaRecetada;
						pg_query($UpdateCantidadLote2);
					}
					/***********************************************************************************************/
						
						
					}else{
					/*		SE UTILIZO SOLAMENTE EL LOTE 1		*/
						$querySelect4="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote1;
						$rowExistencia=pg_fetch_array(pg_query($querySelect4));
						
						if($rowExistencia[0]< $Diferencia){
						/*EXISTENCIA MENOR QUE LA DIFERENCIA A AUMENTAR*/
							$queryUpdate="update farm_medicinaexistenciaxarea set Existencia='0' where IdLote=".$IdLote1;
								pg_query($queryUpdate);
							
							$NuevaCantidad1=$rowExistencia[0]+$CantidadLote1;
							$updateCantidad1="update farm_medicinarecetada set CantidadLote1='$NuevaCantidad1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
								pg_query($updateCantidad1);
							
							$Restante=$Diferencia-$rowExistencia[0];
							$NuevaCantidad2=$CantidadLote2+$Restante;

							
							if($IdLote2!='' and $IdLote2!='0' and $IdLote2!=NULL){
                                $SelectExistencia2="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote2;
                                $rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2)); 
                                
                            $UpdateCantidad2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidad2' where IdMedicinaRecetada=".$IdMedicinaRecetada;
                                pg_query($UpdateCantidad2);                                
                                
                            }else{
							    $SelectExistencia2="select Existencia,farm_lotes.IdLote 
                                from farm_medicinaexistenciaxarea 
                                inner join farm_lotes
                                on farm_lotes.IdLote=farm_medicinaexistenciaxarea.IdLote
                                where Existencia <> '0' and IdMedicina='$IdMedicina'
                                order by FechaVencimiento
                                limit 1";
                                $rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2)); 
                                $IdLote2=$rowExistencia2["IdLote"];
                                
                            $UpdateCantidad2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidad2',Lote2='$IdLote2' where IdMedicinaRecetada=".$IdMedicinaRecetada;
                                pg_query($UpdateCantidad2);
                                                                
                            }   
                            
							
							$NuevaExistencia2=$rowExistencia2[0]-$Restante;
							$UpdateExistencia2="update farm_medicinaexistenciaxarea set Existencia='$NuevaExistencia2' where IdLote=".$IdLote2;
								pg_query($UpdateExistencia2);
													
							
						}else{
							/*EXISTENCIA MAYOR DE LA DIFERENCIA A AUMENTAR*/
							$NuevaExistencia=$rowExistencia[0]-$Diferencia;
							
							$UpdateExistencia1="update farm_medicinaexistenciaxarea set Existencia='$NuevaExistencia' where IdLote=".$IdLote1;
								pg_query($UpdateExistencia1);
							$NuevaCantidad1=$CantidadLote1+$Diferencia;
							
							$UpdateCantidad1="update farm_medicinarecetada set CantidadLote1='$NuevaCantidad1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
								pg_query($UpdateCantidad1);
							
							
						}
						
						
												
					}
					
					
				}else{
					/*	RESTA DE CANTIDAD, SUMA DE EXISTENCIA	*/
					$queryCantidad="select Cantidad from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
						$rowCantidad=pg_fetch_array(pg_query($queryCantidad));
					$Diferencia=$rowCantidad[0]-$Cantidad; 	//Diferencia es lo que falta para llegar al nuevo numero de 
															//medicamento

					
					
					$selectLotes="select CantidadLote1,Lote1,CantidadLote2,Lote2
								from farm_medicinarecetada
								where IdMedicinaRecetada=".$IdMedicinaRecetada;
					$respLotes=pg_fetch_array(pg_query($selectLotes));
					$CantidadLote1=$respLotes["CantidadLote1"];
					$IdLote1=$respLotes["Lote1"];
					$CantidadLote2=$respLotes["CantidadLote2"];
					$IdLote2=$respLotes["Lote2"];
					
					if($IdLote2!='0' and $IdLote2!='' and $IdLote2!=NULL){
						/*	SI SE UTILIZO EL LOTE SECUNDARIO ES EL PRIMERO EN SER DISMINUIDO	*/
						if($CantidadLote2 <= $Diferencia){
							/*SI LA CANTIDAD A ELIMINAR ES MENOR A LA DEL LOTE2*/
							$DiferenciaLotes=$Diferencia-$CantidadLote2;
							
							/*RECUPERACION DE EXISTENCIAS*/
							$SelectExistencia1="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote1;
							$SelectExistencia2="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote2;
								$rowExistencia1=pg_fetch_array(pg_query($SelectExistencia1));
								$rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2));
							
							
							/******************************/
							
							
							$queryUpdateLote2="update farm_medicinarecetada set CantidadLote2='0', Lote2='0' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($queryUpdateLote2);
							
							/*RECUPERACION DE EXISTENCIA*/
							$Existencia2_new=$rowExistencia2[0]+$CantidadLote2;
							$UpdateExistencia2="update farm_medicinaexistenciaxarea set Existencia='$Existencia2_new' where IdLote=".$IdLote2;
							pg_query($UpdateExistencia2);
							
							
														
							$NuevaCantidadLote1=$CantidadLote1-$DiferenciaLotes;
							/*RECUPERACION DE EXISTENCIAS LOTE 1*/
							$Existencia1_new=$rowExistencia1[0]+$DiferenciaLotes;
							$UpdateExistencia1="update farm_medicinaexistenciaxarea set Existencia='$Existencia1_new' where IdLote=".$IdLote1;
							pg_query($UpdateExistencia1);
							
							$queryUpdateLote1="update farm_medicinarecetada set CantidadLote1='$NuevaCantidadLote1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($queryUpdateLote1);
							
						}else{
							/*SI LA CANTIDAD A ELIMINAR ES MAYOR O IGUAL A LA DEL LOTE2*/
							$NuevaCantidadLote2=$CantidadLote2-$Diferencia;
							
							/*RECUPERACION DE EXISTENCIAS*/
							$SelectExistencia2="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote2;
								$rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2));
							$NuevaExistencia2=$rowExistencia2[0]+$Diferencia;
							
							$UpdateExistencia2="Update farm_medicinaexistenciaxarea set Existencia='$NuevaExistencia2' where IdLote=".$IdLote2;
								pg_query($UpdateExistencia2);
							/********************************************/
							
							$UpdateCantidadLote2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidadLote2' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($UpdateCantidadLote2);
							
						}
						
					}else{
						/*	DISMINUCION DE LOTE 1 	*/
						
							$DiferenciaLote1=$CantidadLote1-$Diferencia;
							
							/*RECUPERACION DE EXISTENCIAS*/
							$SelectExistencia1="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote1;
								$rowExistencia1=pg_fetch_array(pg_query($SelectExistencia1));
							
							/******************************/
							
							
							/*RECUPERACION DE EXISTENCIA*/
							$Existencia1_new=$rowExistencia1[0]+$Diferencia;
							$UpdateExistencia1="update farm_medicinaexistenciaxarea set Existencia='$Existencia1_new' where IdLote=".$IdLote1;
							pg_query($UpdateExistencia1);
							
							$UpdateCantidadLote1="update farm_medicinarecetada set CantidadLote1='$DiferenciaLote1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($UpdateCantidadLote1);
						
					}
					
					
				}
			}//IdEstado =='S'
		}else{
			/*		SI EL MEDICAMENTO ES DE OTRA AREA DIFERENTE DE CON. EXT.		*/
			//	rowCantidad[] viene de la query principal al inicio de la funcion
			
$querySelect3="select CantidadLote1,Lote1, CantidadLote2,Lote2 from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
		$resp1=pg_fetch_array(pg_query($querySelect3));
		$Cantidad1=$resp1["CantidadLote1"];
		$IdLote1=$resp1["Lote1"];
		$CantidadLote2=$resp1["CantidadLote2"];
		$IdLote2=$resp1["Lote2"];
		
			if($rowCantidad[1]=='S'){
				if($rowCantidad[0] < $Cantidad){
					/*	SUMA DE CANTIDAD, RESTA EN EXISTENCIAS	*/
					$Diferencia=$Cantidad-$rowCantidad[0]; 	//Diferencia es lo que falta para llegar al nuevo numero de 
															//medicamento

					if($IdLote2!='' and $IdLote2!=NULL and $IdLote2!='0'){
					/*		SE UTILIZO UN LOTE SECUNDARIO		*/
						$querySelect4="select Existencia from farm_entregamedicamento where IdLote=".$IdLote2;
						$rowExistencia=pg_fetch_array(pg_query($querySelect4));
						
					/*		ACTUALIZACION DE EXISTENCIAS		*/
					if($rowExistencia[0]!=0){
						$Existencia_new=$rowExistencia[0]-$Diferencia;
				$UpdateExistencia="update farm_entregamedicamento set Existencia='$Existencia_new' where IdLote=".$IdLote2;
						pg_query($UpdateExistencia);
												
					/*		ACTUALIZACION DE LA CANTIDAD DE MEDICAMENTOS	*/							
						$NuevaCantidad=$CantidadLote2+$Diferencia;
				$UpdateCantidadLote2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidad' where IdMedicinaRecetada=".$IdMedicinaRecetada;
						pg_query($UpdateCantidadLote2);
					}
					/***********************************************************************************************/
						
						
					}else{
					/*		SE UTILIZO SOLAMENTE EL LOTE 1		*/
						$querySelect4="select Existencia from farm_entregamedicamento where IdLote=".$IdLote1;
						$rowExistencia=pg_fetch_array(pg_query($querySelect4));
						
						if($rowExistencia[0]< $Diferencia){
						/*EXISTENCIA MENOR QUE LA DIFERENCIA A AUMENTAR*/
							$queryUpdate="update farm_entregamedicamento set Existencia='0' where IdLote=".$IdLote1;
								pg_query($queryUpdate);
							
							$NuevaCantidad1=$rowExistencia[0]+$CantidadLote1;
							$updateCantidad1="update farm_medicinarecetada set CantidadLote1='$NuevaCantidad1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
								pg_query($updateCantidad1);
							
							$Restante=$Diferencia-$rowExistencia[0];
							$NuevaCantidad2=$CantidadLote2+$Restante;
					
							
                            if($IdLote2!='' and $IdLote2!='0' and $IdLote2!=NULL){
                                $SelectExistencia2="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote2;
                                $rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2)); 
                                
                                        $UpdateCantidad2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidad2' where IdMedicinaRecetada=".$IdMedicinaRecetada;
                                pg_query($UpdateCantidad2);
                                
                            }else{
                                $SelectExistencia2="select Existencia,farm_lotes.IdLote 
                                from farm_medicinaexistenciaxarea 
                                inner join farm_lotes
                                on farm_lotes.IdLote=farm_medicinaexistenciaxarea.IdLote
                                where Existencia <> '0' and IdMedicina='$IdMedicina'
                                order by FechaVencimiento
                                limit 1";
                                $rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2)); 
                                $IdLote2=$rowExistencia2["IdLote"];
                                
                                        $UpdateCantidad2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidad2',Lote2='$IdLote2' where IdMedicinaRecetada=".$IdMedicinaRecetada;
                                pg_query($UpdateCantidad2);
                                
                            }   
							
							$NuevaExistencia2=$rowExistencia[0]-$Restante;
							$UpdateExistencia2="update farm_entregamedicamento set Existencia='$NuevaExistencia2' where IdLote=".$IdLote2;
								pg_query($UpdateExistencia2);
													
							
						}else{
							/*EXISTENCIA MAYOR DE LA DIFERENCIA A AUMENTAR*/
							$NuevaExistencia=$rowExistencia[0]-$Diferencia;
							
							$UpdateExistencia1="update farm_entregamedicamento set Existencia='$NuevaExistencia' where IdLote=".$IdLote1;
								pg_query($UpdateExistencia1);
							$NuevaCantidad1=$CantidadLote1+$Diferencia;
							
							$UpdateCantidad1="update farm_medicinarecetada set CantidadLote1='$NuevaCantidad1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
								pg_query($UpdateCantidad1);
							
							
						}
						
						
												
					}
					
					
				}else{
					/*	RESTA DE CANTIDAD, SUMA DE EXISTENCIA	*/
					$queryCantidad="select Cantidad from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
						$rowCantidad=pg_fetch_array(pg_query($queryCantidad));
					$Diferencia=$rowCantidad[0]-$Cantidad; 	//Diferencia es lo que falta para llegar al nuevo numero de 
															//medicamento

					
					
					$selectLotes="select CantidadLote1,Lote1,CantidadLote2,Lote2
								from farm_medicinarecetada
								where IdMedicinaRecetada=".$IdMedicinaRecetada;
					$respLotes=pg_fetch_array(pg_query($selectLotes));
					$CantidadLote1=$respLotes["CantidadLote1"];
					$IdLote1=$respLotes["Lote1"];
					$CantidadLote2=$respLotes["CantidadLote2"];
					$IdLote2=$respLotes["Lote2"];
					
					if($IdLote2!='0' and $IdLote2!='' and $IdLote2!=NULL){
						/*	SI SE UTILIZO EL LOTE SECUNDARIO ES EL PRIMERO EN SER DISMINUIDO	*/
						if($CantidadLote2 <= $Diferencia){
							/*SI LA CANTIDAD A ELIMINAR ES MENOR A LA DEL LOTE2*/
							$DiferenciaLotes=$Diferencia-$CantidadLote2;
							
							/*RECUPERACION DE EXISTENCIAS*/
							$SelectExistencia1="select Existencia from farm_entregamedicamento where IdLote=".$IdLote1;
							$SelectExistencia2="select Existencia from farm_entregamedicamento where IdLote=".$IdLote2;
								$rowExistencia1=pg_fetch_array(pg_query($SelectExistencia1));
								$rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2));
							
							
							/******************************/
							
							
							$queryUpdateLote2="update farm_medicinarecetada set CantidadLote2='0', Lote2='0' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($queryUpdateLote2);
							
							/*RECUPERACION DE EXISTENCIA*/
							$Existencia2_new=$rowExistencia2[0]+$CantidadLote2;
							$UpdateExistencia2="update farm_entregamedicamento set Existencia='$Existencia2_new' where IdLote=".$IdLote2;
							pg_query($UpdateExistencia2);
							
							
														
							$NuevaCantidadLote1=$CantidadLote1-$DiferenciaLotes;
							/*RECUPERACION DE EXISTENCIAS LOTE 1*/
							$Existencia1_new=$rowExistencia1[0]+$DiferenciaLotes;
							$UpdateExistencia1="update farm_entregamedicamento set Existencia='$Existencia1_new' where IdLote=".$IdLote1;
							pg_query($UpdateExistencia1);
							
							$queryUpdateLote1="update farm_medicinarecetada set CantidadLote1='$NuevaCantidadLote1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($queryUpdateLote1);
							
						}else{
							/*SI LA CANTIDAD A ELIMINAR ES MAYOR O IGUAL A LA DEL LOTE2*/
							$NuevaCantidadLote2=$CantidadLote-$Diferencia;
							
							/*RECUPERACION DE EXISTENCIAS*/
							$SelectExistencia2="select Existencia from farm_entregamedicamento where IdLote=".$IdLote2;
								$rowExistencia2=pg_fetch_array(pg_query($SelectExistencia2));
							$NuevaExistencia2=$rowExistencia2[0]+$Diferencia;
							
							$UpdateExistencia2="Update farm_entregamedicamento set Existencia='$NuevaExistencia2' where IdLote=".$IdLote2;
								pg_query($UpdateExistencia2);
							/********************************************/
							
							$UpdateCantidadLote2="update farm_medicinarecetada set CantidadLote2='$NuevaCantidadLote2' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($UpdateCantidadLote2);
							
						}
						
					}else{
						/*	DISMINUCION DE LOTE 1 	*/
						
							$DiferenciaLote1=$CantidadLote1-$Diferencia;
							
							/*RECUPERACION DE EXISTENCIAS*/
							$SelectExistencia1="select Existencia from farm_entregamedicamento where IdLote=".$IdLote1;
								$rowExistencia1=pg_fetch_array(pg_query($SelectExistencia1));
							
							/******************************/
							
							
							/*RECUPERACION DE EXISTENCIA*/
							$Existencia1_new=$rowExistencia1[0]+$Diferencia;
							$UpdateExistencia1="update farm_entregamedicamento set Existencia='$Existencia1_new' where IdLote=".$IdLote1;
							pg_query($UpdateExistencia1);
							
							$UpdateCantidadLote1="update farm_medicinarecetada set CantidadLote1='$DiferenciaLote1' where IdMedicinaRecetada=".$IdMedicinaRecetada;
							pg_query($UpdateCantidadLote1);
						
					}
					
					
				}
			}//IdEstado =='S'			
			
			
		}// else IdArea==2
	
		$queryUpdate="update farm_medicinarecetada set Cantidad='$Cantidad' where IdMedicinaRecetada='$IdMedicinaRecetada'";
		pg_query($queryUpdate);		
	}//UpdateDosis
	
	function UpdateMedicinaRecetada($IdMedicinaRecetada,$Estado,$IdMedicina){
		if($Estado=='I'){
			/****************************************************/
			/*				RECUPERACION DE EXISTENCIAS			*/
			/****************************************************/
			$querySelect1="select IdArea
						from farm_recetas
						inner join farm_medicinarecetada
						on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
						where IdMedicinaRecetada=".$IdMedicinaRecetada;
			$IdArea=pg_fetch_array(pg_query($querySelect1));
			if($IdArea[0]==2){
				/****************************************************/
				/*				AREA CONSULTA EXTERNA				*/
				/****************************************************/
				$querySelect2="select CantidadLote1, Lote1, CantidadLote2, Lote2
							from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
				$resp2=pg_fetch_array(pg_query($querySelect2));
				$CantidadLote1=$resp2["CantidadLote1"];
				$IdLote1=$resp2["Lote1"];
				$CantidadLote2=$resp2["CantidadLote2"];
				$IdLote2=$resp2["Lote2"];
					/*****		LOTE 1		*****/
					$querySelect3="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote1;
					$resp3=pg_fetch_array(pg_query($querySelect3));
					$Existencia_new1=$CantidadLote1+$resp3["Existencia"];
					$queryUpdate1="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new1' where IdLote=".$IdLote1;
						pg_query($queryUpdate1);
					
					/*****		LOTE 2		*****/
					if($IdLote2!='' and $IdLote2!=NULL and $IdLote2!='0'){
						$querySelect4="select Existencia from farm_medicinaexistenciaxarea where IdLote=".$IdLote2;
						$resp4=pg_fetch_array(pg_query($querySelect4));
						$Existencia_new2=$CantidadLote1+$resp4["Existencia"];
					$queryUpdate2="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new2' where IdLote=".$IdLote2;
							pg_query($queryUpdate2);
					
					}//SI HAY DATOS DEL LOTE 2
					
			}else{
				/****************************************************/
				/*					LAS DEMAS AREAS					*/
				/****************************************************/
				$querySelect2="select CantidadLote1, Lote1, CantidadLote2, Lote2
							from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
				$resp2=pg_fetch_array(pg_query($querySelect2));
				$CantidadLote1=$resp2["CantidadLote1"];
				$IdLote1=$resp2["Lote1"];
				$CantidadLote2=$resp2["CantidadLote2"];
				$IdLote2=$resp2["Lote2"];
					/*****		LOTE 1		*****/
					$querySelect3="select Existencia from farm_entregamedicamento where IdLote=".$IdLote1;
					$resp3=pg_fetch_array(pg_query($querySelect3));
					$Existencia_new1=$CantidadLote1+$resp3["Existencia"];
					$queryUpdate1="update farm_entregamedicamento set Existencia='$Existencia_new1' where IdLote=".$IdLote1;
						pg_query($queryUpdate1);
					
					/*****		LOTE 2		*****/
					if($IdLote2!='' and $IdLote2!=NULL and $IdLote2!='0'){
						$querySelect4="select Existencia from farm_entregamedicamento where IdLote=".$IdLote2;
						$resp4=pg_fetch_array(pg_query($querySelect4));
						$Existencia_new2=$CantidadLote1+$resp4["Existencia"];
					$queryUpdate2="update farm_entregamedicamento set Existencia='$Existencia_new2' where IdLote=".$IdLote2;
							pg_query($queryUpdate2);
					
					}//SI HAY DATOS DEL LOTE 2
			}//AREA NO ES C EXT		
			
			/*****************************************************/
			
			$queryUpdate="update farm_medicinarecetada set IdEstado='I', CantidadLote1='0', Lote1='0', CantidadLote2='0', Lote2='0' where IdMedicinaRecetada='$IdMedicinaRecetada'";
			pg_query($queryUpdate);
			
		}else{
			
					$querySelectArea="select IdArea, farm_medicinarecetada.IdEstado 
									from farm_recetas 
									inner join farm_medicinarecetada
									on farm_medicinarecetada.IdReceta=farm_Recetas.IdReceta
									where IdMedicinaRecetada=".$IdMedicinaRecetada;
		$IdArea=pg_fetch_array(pg_query($querySelectArea));
		
		/********	MANTENIMIENTO DE EXISTENCIAS ********/
			if($IdArea[0]==2){
			/********************************************************/
			/*				AREA DE CONSULTA EXTERNA				*/
			/********************************************************/
				$querySelect1="select Existencia, farm_lotes.IdLote
							from farm_medicinaexistenciaxarea
							inner join farm_lotes
							on farm_lotes.IdLote=farm_medicinaexistenciaxarea.IdLote
							where IdMedicina='$IdMedicina'
							and Existencia <> '0'
							order by FechaVencimiento
							limit 1";
				$resp1=pg_fetch_array(pg_query($querySelect1));
				$IdLote=$resp1["IdLote"];
				
				$queryCantidad="select Cantidad,IdEstado from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
				$rowCantidad=pg_fetch_array(pg_query($queryCantidad));
				$Cantidad=$rowCantidad[0];
				
				
					if($resp1["Existencia"]<$Cantidad){
						$Restante=$Cantidad-$resp["Existencia"];
						$queryUpdate1="update farm_medicinaexistenciaxarea set Existencia='0' where IdLote=".$IdLote;
						pg_query($queryUpdate);
							
							$CantidadLote1=$resp1["Existencia"];
							$Lote1=$resp1["IdLote"];
						
							$queryUpdate2="update farm_medicinarecetada set CantidadLote1='$CantidadLote1' , Lote1='$Lote1' where IdMedicinaRecetada='$IdMedicinaRecetada'";
							pg_query($queryUpdate2);
							
							
							/*********	MANIPULACION DEL SEGUNDO LOTE	*********/
							$resp2=pg_fetch_array(pg_query($querySelect1));
							
							$Existencia_new=$resp2["Existencia"]-$Restante;
							$Lote2=$resp2["IdLote"];
						$queryUpdate3="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new' where IdLote=".$Lote2;
						$queryUpdate4="update farm_medicinarecetada set CantidadLote2='$Restante', Lote2='$Lote2' 
									where IdMedicinaRecetada='$IdMedicinaRecetada'";
							pg_query($queryUpdate3);
							pg_query($queryUpdate4);			
							/***************************************************/
						
					}else{//SI EXISTENCIA < CANTIDAD
					
						/*****************	CANTIDAD < EXISTENCIA *****************/
						$Existencia_new=$resp1["Existencia"]-$Cantidad;
						
						$queryUpdate1="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new' where IdLote=".$IdLote;
						pg_query($queryUpdate1);
						
						$queryUpdate2="update farm_medicinarecetada set CantidadLote1='$Cantidad', Lote1='$IdLote' where IdMedicinaRecetada='$IdMedicinaRecetada'";
						pg_query($queryUpdate2);	
						
						/*************************************************/
					}//ELSE EXISTENCIA < CANTIDAD
				
			}else{
				/************************************************************************/
				/*				TODAS LAS AREAS QUE NO SON CONSULTA EXTERNA 			*/				
				/************************************************************************/
				$querySelect="select Existencia,farm_Lotes.IdLote
							from farm_entregamedicamento
							inner join farm_lotes
							on farm_lotes.IdLote=farm_entregamedicamento.IdLote
							where IdLote='$IdMedicina'
							and Existencia <> 0
							order by FechaVencimiento
							limit 1";
				$resp=pg_fetch_array(pg_query($querySelect));
					$IdLote=$resp["IdLote"];
					
				$queryCantidad="select Cantidad,IdEstado from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
				$rowCantidad=pg_fetch_array(pg_query($queryCantidad));
				$Cantidad=$rowCantidad[0];

				
					if($resp["Existencia"]< $Cantidad){
						$Restante=$Cantidad-$resp["Existencia"];
						$queryUpdate1="update farm_entregamedicamento set Existencia='0' where IdLote=".$resp[1];
						pg_query($queryUpdate);
							
							$CantidadLote1=$resp["Existencia"];
							$Lote1=$resp["IdLote"];
						
							$queryUpdate2="update farm_medicinarecetada set CantidadLote1='$CantidadLote1' , Lote1='$Lote1' where IdReceta='$IdMedicinaRecetada'";
							pg_query($queryUpdate2);
							
							
							/*********	MANIPULACION DEL SEGUNDO LOTE	*********/
							$resp2=pg_fetch_array(pg_query($querySelect));
							
							$Existencia_new=$resp2["Existencia"]-$Restante;
							$Lote2=$resp2["IdLote"];
							$queryUpdate3="update farm_entregamedicamento set Existencia='$Existencia_new' where IdLote=".$resp2[1];
							$queryUpdate4="update farm_medicinarecetada set CantidadLote2='$Restante', Lote2='$Lote2' where IdMedicinaRecetada='$IdMedicinaRecetada'";
							pg_query($queryUpdate3);
							pg_query($queryUpdate4);			
							
							
							/***************************************************/
							
					}else{
	
					/************************************************/
						$Existencia_new=$resp["Existencia"]-$Cantidad;
	
						
						$queryUpdate1="update farm_entregamedicamento set Existencia='$Existencia_new' where IdLote=".$IdLote;
						pg_query($queryUpdate1);
						
						$queryUpdate2="update farm_medicinarecetada set CantidadLote1='$Cantidad', Lote1='$IdLote' where IdMedicinaRecetada=".$IdMedicinaRecetada;
						pg_query($queryUpdate2);	
						
						
					}//Cantidad < Existencia
				
		/************************************************/
			}//Else IdArea==2
		
		
		
			$queryUpdate="update farm_medicinarecetada set IdEstado='S' where IdMedicinaRecetada='$IdMedicinaRecetada'";
				pg_query($queryUpdate);
		}
		
		
	}//UpdateMedicinaRecetada
	
	
	function ObtenerCodigoFarmacia($IdMedico){
		$querySelect="select CodigoFarmacia
					from mnt_empleados
					where IdEmpleado='$IdMedico'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//CodigoFarmacia
	
	
	function ObtenerDatosMedico($CodigoFarmacia){
		$querySelect="select mnt_empleados.IdEmpleado, mnt_empleados.NombreEmpleado
					from mnt_empleados
					where mnt_empleados.CodigoFarmacia='$CodigoFarmacia'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp);
	}//ObtenerDatosMedico
	
	/*	ACTUALIZACION DE INFORMACION	*/
        
 	function ActualizarExpediente($IdHistorialClinico,$IdNumeroExp){
				
		$query="update sec_historial_clinico set IdNumeroExp='$IdNumeroExp' where IdHistorialClinico='$IdHistorialClinico'";
		pg_query($query);	
		
	}
        
        
	function ActualizarArea($IdArea,$IdReceta,$IdFarmacia,$Fecha,$IdEstablecimiento,$IdModalidad){
	    //MANEJO DE EXISTENCIAS EN CASO DE CAMBIO DE AREA
		$SQL="select IdMedicinaRecetada,IdArea,Cantidad,IdMedicina
			from farm_medicinarecetada
			inner join farm_recetas
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			where farm_medicinarecetada.IdReceta=".$IdReceta."
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad";
		$resp=pg_query($SQL);
		
		while($row=pg_fetch_array($resp)){
		   
			//se elimina el medicamento despachado de la area anterior y se aumentan existencias
			$this->AumentarInventario($row["IdMedicinaRecetada"],$row["IdArea"],$IdEstablecimiento,$IdModalidad);
			
			//se le atribuye a la futura area el cargo del medicamento a cambiar de area
			$this->ActualizarInventario($row["IdMedicina"],$row["IdMedicinaRecetada"],$row["Cantidad"],$IdArea,
                                                    $Fecha,$IdEstablecimiento,$IdModalidad);
		   
		}
		

	    //**************************************************

		$queryH="select IdHistorialClinico from farm_recetas 
                         where IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento 
                         and IdModalidad=$IdModalidad";
		$respH=pg_fetch_array(pg_query($queryH));
			$IdNumeroExp='0'.$IdFarmacia;
		    $SQL="update sec_historial_clinico set IdNumeroExp='$IdNumeroExp' 
                          where IdHistorialClinico=".$respH[0]."
                          and IdEstablecimiento=$IdEstablecimiento
                          and IdModalidad=$IdModalidad";
			pg_query($SQL);
		$query="update farm_recetas set IdArea='$IdArea',IdFarmacia='$IdFarmacia' 
                        where IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		pg_query($query);
		$query2="select Area from mnt_areafarmacia where IdArea='$IdArea'";
		$resp=pg_fetch_array(pg_query($query2));
		
		$query3="select Farmacia from mnt_farmacia where IdFarmacia=".$IdFarmacia;
		$resp2=pg_fetch_array(pg_query($query3));
		
		$salida="<a href='#' onClick='javascript:Correcciones(\"NombreArea\");'>".strtoupper($resp[0])."</a>~";
		$salida.="<strong>".strtoupper($resp2[0])."</strong><input type='hidden' id='IdAreaActual' value='".$IdArea."'>";

		return($salida);		
	}//actualizaicon de area
	

function ActualizarAreaOrigen($IdArea,$IdReceta,$IdEstablecimiento,$IdModalidad){
		$query="update farm_recetas set IdAreaOrigen='$IdArea' where IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		pg_query($query);

		$query2="select Area from mnt_areafarmacia where IdArea='$IdArea'";
		$resp=pg_fetch_array(pg_query($query2));
		
		$salida="<a href='#' onClick='javascript:Correcciones(\"NombreAreaOrigen\");'>".strtoupper($resp[0])."</a>
		";

		return($salida);

}


	function ObtenerIdFarmacia($IdArea){
		$query="select IdFarmacia 
				from mnt_areafarmacia
                                
				where IdArea=".$IdArea;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}

	
	function ActualizarMedico($IdHistorialClinico,$IdMedico,$IdEstablecimiento,$IdModalidad){
		if($IdMedico!=''){
		$query="update sec_historial_clinico set IdEmpleado='$IdMedico' 
                        where IdHistorialClinico='$IdHistorialClinico' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		pg_query($query);
		}
		$query2="select NombreEmpleado from mnt_empleados inner join sec_historial_clinico shc on shc.IdEmpleado=mnt_empleados.IdEmpleado where IdHistorialClinico=".$IdHistorialClinico." and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		$resp=pg_fetch_array(pg_query($query2));
		$salida="<a href='#' onClick='javascript:Correcciones(\"NombreMedico\");'>".$resp[0]."</a>";
		return($salida);
	}
	
	function ActualizarEspecialidad($IdHistorialClinico,$IdSubServicio,$Codigo,$IdEstablecimiento,$IdModalidad){
		if($IdSubServicio!=''){	
		$query="update sec_historial_clinico set IdSubServicioxEstablecimiento='$IdSubServicio' 
                        where IdHistorialClinico='$IdHistorialClinico' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		pg_query($query);
		}

		$query2="select msse.IdSubServicioxEstablecimiento,NombreSubServicio, NombreServicio as Ubicacion
                        from mnt_subservicio
                        inner join mnt_subservicioxestablecimiento msse
                        on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                        inner join mnt_servicioxestablecimiento mse
                        on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                        inner join mnt_servicio ms
                        on ms.IdServicio=mse.IdServicio
                        inner join sec_historial_clinico shc
                        on shc.IdSubServicioxEstablecimiento=msse.IdSubServicioxEstablecimiento
                        where IdHistorialClinico='$IdHistorialClinico'
			and msse.IdEstablecimiento=$IdEstablecimiento
                        and msse.IdModalidad=$IdModalidad
                        and msse.CodigoFarmacia='$Codigo'";
		$resp=pg_fetch_array(pg_query($query2));
		if($resp[2]!='' and $resp[2]!=NULL){
                    $Especialidad=$resp[2].' -> '.$resp[1];
                    
                }else{
                    $Especialidad=$resp[1];
                    
                }
		$respuesta="<a href='#' onClick='javascript:Correcciones(\"Especialidad\");'>".strtoupper($Especialidad)."</a>";
		return($respuesta);
	}
	
	function ObtenerArea($IdEstablecimiento,$IdModalidad){
		$query="select mafxe.IdArea, Area, case IdFarmacia when 1 then '(Farm. Central)' when 2 then '(Con. Ext.)' when 3 then '(Emergencia)' end as Origen
			from mnt_areafarmacia
                        inner join mnt_areafarmaciaxestablecimiento mafxe
                        on mafxe.IdArea=mnt_areafarmacia.IdArea
			where mafxe.IdArea != 7 and mafxe.IdArea != 12
			and mafxe.Habilitado='S'	
			and IdFarmacia <>0
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad
			order by IdFarmacia";

		$resp=pg_query($query);
		return($resp);
	}
	
	function CambiarFecha($IdReceta,$FechaNueva,$IdHistorialClinico,$IdEstablecimiento,$IdModalidad){
		$query="update farm_recetas set Fecha='$FechaNueva' where IdReceta=".$IdReceta." and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		$query2="update farm_medicinarecetada set FechaEntrega='$FechaNueva' where IdReceta=".$IdReceta." and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		$query3="update sec_historial_clinico set FechaConsulta='$FechaNueva' where IdHistorialClinico=".$IdHistorialClinico." and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		pg_query($query);
		pg_query($query2);
		pg_query($query3);
	}
	
	
	function Cierre($Fecha,$IdEstablecimiento,$IdModalidad){
		$sql="SELECT AnoCierre
                      FROM farm_cierre
                      WHERE  AnoCierre=substring('".$Fecha."',1,4)::int
                      AND IdEstablecimiento=".$IdEstablecimiento."
                      AND IdModalidad=$IdModalidad";
		$resp=pg_query($sql);
		return($resp);		
	}//Cierre
	

	function CierreMes($Fecha,$IdEstablecimiento,$IdModalidad){
		$sql="SELECT MesCierre
                      FROM farm_cierre
                      WHERE MesCierre=substring('$Fecha',1,7)
                      AND IdEstablecimiento=".$IdEstablecimiento."
                      AND IdModalidad=$IdModalidad";
		$resp=pg_query($sql);
		return($resp);		
	}//CierreMes


//**************	MANEJO DE EXISTENCIAS POR LOTES		**********************************
	
	//Actualizacion de inventario e identificacion de lote utilizado por medicamento
	
	function ActualizarInventario($IdMedicina,$IdMedicinaRecetada,$Cantidad,$IdArea,$Fecha,$IdEstablecimiento,$IdModalidad){
		$queryLote="select fl.IdLote,Existencia,FechaVencimiento, fme.IdExistencia
			from farm_lotes fl
			inner join farm_medicinaexistenciaxarea fme
			on fme.IdLote=fl.IdLote
			where fme.IdMedicina=$IdMedicina
			and Existencia <> 0
			and left('$Fecha',7) <= left(FechaVencimiento,7)
			and fme.IdArea=$IdArea
                        and fme.IdEstablecimiento=$IdEstablecimiento
                        and fme.IdModalidad=$IdModalidad
			order by FechaVencimiento asc, IdExistencia asc";
		$lotes=pg_query($queryLote);
                
                while($lotesA=pg_fetch_array($lotes)){
                        if($Cantidad <= $lotesA["Existencia"]){
                            //****** Si la cantidad de medicamento no exede el total del primer lote a descagar...
                            $IdLote=$lotesA["IdLote"];
                            $existencia_old=$lotesA["Existencia"];
                            $IdExistenciaTabla=$lotesA["IdExistencia"];
                            // se obtiene la nueva exitencia
                            $existencia_new=$existencia_old-$Cantidad;

                            //se actualiza la existencia del lote en uso
                            $actualiza="update farm_medicinaexistenciaxarea set Existencia='$existencia_new' 
                                        where IdExistencia='$IdExistenciaTabla' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
                            pg_query($actualiza);

                            //se ingresa el lote utilizado
                            $query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                        values('$IdMedicinaRecetada','$IdLote','$Cantidad',$IdEstablecimiento,$IdModalidad)";
                            pg_query($query);

                        //Se termina el lazo porque el lote en cuestion suple la demanda restante
                        break;
                        }else{

                            //Primer lote a agotar...
                            $IdLote=$lotesA["IdLote"];
                            $existencia_old=$lotesA["Existencia"];
                            $IdExistenciaTabla=$lotesA["IdExistencia"];

                            //Medicina que aun falta por despachar
                            $restante2=$Cantidad-$existencia_old;
                            //Se cierra el lote con existencia = 0
                            $actualiza="update farm_medicinaexistenciaxarea set Existencia='0' 
                                        where IdExistencia='$IdExistenciaTabla' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
                            pg_query($actualiza);

                            //se ingresa el lote utilizado
                            $query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                  values('$IdMedicinaRecetada','$IdLote','$existencia_old',$IdEstablecimiento,$IdModalidad)";
                            pg_query($query);

                            $Cantidad=$restante2;
                        }//else de la comparacion de restante vs existencia

                }// Recorrido de los demas lotes con existencia
                
                
                /* funcion de Carlos Funetes.
		$lotesA=pg_fetch_array($lotes);
		if($Cantidad <= $lotesA["Existencia"]){
		//****** Si la cantidad de medicamento no exede el total del primer lote a descagar...
			
			$IdLote=$lotesA["IdLote"];
			$existencia_old=$lotesA["Existencia"];
			$IdExistenciaTabla=$lotesA["IdExistencia"];
			
			$existencia_new=$existencia_old-$Cantidad;
			
			$actualiza="update farm_medicinaexistenciaxarea set Existencia='$existencia_new' 
                                    where IdExistencia='$IdExistenciaTabla'
                                    and IdEstablecimiento=$IdEstablecimiento
                                    and IdModalidad=$IdModalidad";
			pg_query($actualiza);
			
			//se ingresa el lote utilizado
			$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad)
                                                              values('$IdMedicinaRecetada','$IdLote','$Cantidad',$IdEstablecimiento,$IdModalidad)";
			pg_query($query);
			
			
		
		}else{
		//****** Si la existencia del lote es menor a lo que se descargara, se debe utilizar el segundo lote...
			
			//Primer lote a agotar...
			$IdLote=$lotesA["IdLote"];
			$existencia_old=$lotesA["Existencia"];
			$IdExistenciaTabla=$lotesA["IdExistencia"];
			
				//Medicina que aun falta por despachar
				$restante=$Cantidad-$existencia_old;
				
				
				
			//Se cierra el lote con existencia = 0
				$actualiza="update farm_medicinaexistenciaxarea set Existencia='0' 
                                            where IdExistencia='$IdExistenciaTabla'
                                            and IdEstablecimiento=$IdEstablecimiento
                                            and IdModalidad=$IdModalidad";
				pg_query($actualiza);
				
				//se ingresa el lote utilizado
			$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                              values('$IdMedicinaRecetada','$IdLote','$existencia_old',$IdEstablecimiento,$IdModalidad)";
			pg_query($query);
				
			//Se recorren los siguiente lotes... Modo iterativo
			while($lotesA=pg_fetch_array($lotes)){
				if($restante <= $lotesA["Existencia"]){
				//****** Si la cantidad de medicamento no exede el total del primer lote a descagar...
				$IdLote=$lotesA["IdLote"];
				$existencia_old=$lotesA["Existencia"];
				$IdExistenciaTabla=$lotesA["IdExistencia"];
				
				$existencia_new=$existencia_old-$restante;
					
					//se actualiza la existencia del lote en uso
					$actualiza="update farm_medicinaexistenciaxarea set Existencia='$existencia_new' 
                                                    where IdExistencia='$IdExistenciaTabla'
                                                    and IdEstablecimiento=$IdEstablecimiento
                                                    and IdModalidad=$IdModalidad";
					pg_query($actualiza);
				
					//se ingresa el lote utilizado
					$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad)
                                                                              values('$IdMedicinaRecetada','$IdLote','$restante',$IdEstablecimiento,$IdModalidad)";
					pg_query($query);
				
					//Se termina el lazo porque el lote en cuestion suple la demanda restante
					break;
				}else{
				
				//Primer lote a agotar...
				$IdLote=$lotesA["IdLote"];
				$existencia_old=$lotesA["Existencia"];
				$IdExistenciaTabla=$lotesA["IdExistencia"];
				
				//Medicina que aun falta por despachar
					$restante2=$restante-$existencia_old;
				//Se cierra el lote con existencia = 0
					$actualiza="update farm_medicinaexistenciaxarea set Existencia='0' 
                                                    where IdExistencia='$IdExistenciaTabla'
                                                    and IdEstablecimiento=$IdEstablecimiento
                                                    and IdModalidad=$IdModalidad";
					pg_query($actualiza);
					
					//se ingresa el lote utilizado
					$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                              values('$IdMedicinaRecetada','$IdLote','$existencia_old',$IdEstablecimiento,$IdModalidad)";
					pg_query($query);
					
					$restante=$restante2;
					
				
				}//else de la comparacion de restante vs existencia
				
			}// Recorrido de los demas lotes con existencia
			
						
		}//else de cantidad vs existencia si no suple la demanda el primer lote     Funcion de Carlos Fuentes.         */ 
		
		
	}//actualizar inventario
	
	
	
	//MANEJO DE LOTES CUANDO SE ELIMINA UNA RECETA POR CORRECCION
	
	function AumentarInventario($IdMedicinaRecetada,$IdArea,$IdEstablecimiento,$IdModalidad){
		$query="select CantidadDespachada,IdLote,IdMedicinaDespachada 
			from farm_medicinadespachada
			where IdMedicinaRecetada=".$IdMedicinaRecetada."
                        and IdEstablecimiento=".$IdEstablecimiento."
                        and IdModalidad=$IdModalidad";
		$resp=pg_query($query);
		
		while($row=pg_fetch_array($resp)){
			$CantidadDespacha=$row["CantidadDespachada"];
			$IdLoteDespachado=$row["IdLote"];
			$IdMedicinaDespachada=$row["IdMedicinaDespachada"];
		
		//Obtencion de existencias actuales del lote utilizado
			$queryExistencia="select Existencia, IdExistencia
				from farm_medicinaexistenciaxarea
				where IdArea='$IdArea' and IdLote='$IdLoteDespachado'
                                and IdEstablecimiento=$IdEstablecimiento 
                                and IdModalidad=$IdModalidad
				order by IdExistencia asc";
			$datos=pg_fetch_array(pg_query($queryExistencia));
			
		//Aumento de existencia
			$Nueva_Existencia=$CantidadDespacha+$datos["Existencia"];
			$IdExistenciaTabla=$datos["IdExistencia"];
			
		//Ingreso de Nueva Existencia
			
			$query2="update farm_medicinaexistenciaxarea set Existencia='$Nueva_Existencia'
				where IdExistencia='$IdExistenciaTabla'
                                and IdEstablecimiento=$IdEstablecimiento
                                and IdModalidad=$IdModalidad";
			pg_query($query2);
			
		// Eliminacion de movimiento de despacho
			$AnulacionDespacho="delete from farm_medicinadespachada 
					where IdMedicinaDespachada=".$IdMedicinaDespachada."
                                        and IdEstablecimiento=$IdEstablecimiento
                                        and IdModalidad=$IdModalidad";
			pg_query($AnulacionDespacho);
			
			
		}//Recorrido de farm_medicinadespachada	
		
		
		
	}//aumento de existencias por eliminacion de recetas...
	
	
	
	//***********	Actualizacion de inventario cuando se cambia la Cantidad de medicamento introducido *************
	
	function ActualizacionInventarioCantidad($IdMedicinaRecetada,$NuevaCantidad,$IdArea,$IdEstablecimiento,$IdModalidad){
	//Obtencion de Cantidad Antigua
	$query="select IdMedicina,Cantidad from farm_medicinarecetada where IdMedicinaRecetada=".$IdMedicinaRecetada;
	$datos=pg_fetch_array(pg_query($query));
	//Primera parte cuando se aumenta la Cantidad
		if($datos["Cantidad"] < $NuevaCantidad){
			
			$IdMedicina=$datos["IdMedicina"];
			$CantidadAnterior=$datos["Cantidad"];
		    //Calculo de la entrega extra
			$extra=$NuevaCantidad-$CantidadAnterior;
			
		    $this->ActualizarInventario($IdMedicina,$IdMedicinaRecetada,$extra,$IdArea);
		
		}
	//**************************************************
	//Segunda Parte cuando se disminuye la Cantidad
		if($NuevaCantidad < $datos["Cantidad"]){
			$CantidadAnterior=$datos["Cantidad"];
		    //Calculo del medicamento a disminuir y aumentar en la existencia
			$restante=$CantidadAnterior-$NuevaCantidad;
		    //Obtencion de lotes ordenados por mayor existencia
		    //Son lotes que realmente se utilizaron para esa receta introducida...
			
			$queryLotes="select IdMedicinaDespachada, CantidadDespachada, farm_medicinadespachada.IdLote, Existencia
			from farm_medicinadespachada
			inner join farm_lotes
			on farm_lotes.IdLote=farm_medicinadespachada.IdLote
			inner join farm_medicinaexistenciaxarea
			on farm_medicinaexistenciaxarea.IdLote=farm_lotes.IdLote
			
			where IdMedicinaRecetada='$IdMedicinaRecetada'
			
			order by Existencia desc";	
			
			$resp=pg_query($queryLotes);
			
			while($row=pg_fetch_array($resp)){
				
				$IdMedicinaDespachada=$row["IdMedicinaDespachada"];
				
			    if($restante < $row["CantidadDespachada"]){
			    //Cuando la cantidad a disminuir es menor a la despachada por el primer lote
				$IdLote=$row["IdLote"];
				$CantidadDespachada=$row["CantidadDespachada"];
			    //Calculo de restante [Medicina realmente despachada]
				$restante2=$CantidadDespachada-$restante;
				
			    //Disminucion de la medicina despachada en farm_medicinadespachada
				$actualizaDespacho="update farm_medicinadespachada 
						set CantidadDespachada='$restante2'
						where IdMedicinaDespachada='$IdMedicinaDespachada'";
				pg_query($actualizaDespacho);
				
			    //Aumento de existencias en lote utilizado por el moviemitno anterior
				$ExistenciaAnterior=$row["Existencia"];
				$ExistenciaNueva=$ExistenciaAnterior+$restante;
				
			    //Actualizacion de Existencia
				$actualizaExistencia="update farm_medicinaexistenciaxarea
						set Existencia='$ExistenciaNueva'
						where IdLote='$IdLote'";
				pg_query($actualizaExistencia);
				
				break;
			    }else{
			    //Cuando la cantidad a disminuir es mayor a la cantidad utilizada 
			    //por el primer Lote. Se debe eliminar el movimiento descrito por la receta
			    //en farm_medicinadespachada
				$IdLote=$row["IdLote"];
				$CantidadDespachada=$row["CantidadDespachada"];
				$restante2=$restante-$CantidadDespachada;
				$prueba=1;
					if($restante2==0){
					   //En dado caso sea exacto al movimiento del lote el restante2 sera = restante
						$prueba=0;
						$restante2=$restante;
					}
			    //Aumento de la existencia del lote utilizado
				$ExistenciaAnterior=$row["Existencia"];
				$ExistenciaNueva=$ExistenciaAnterior+$CantidadDespachada;
				
			    //Actualizacion de la existencia del lote en cuestion
				$actualizaExistencia="update farm_medicinaexistenciaxarea
						set Existencia='$ExistenciaNueva'
						where IdLote='$IdLote'";	
				pg_query($actualizaExistencia);
				
			    //Eliminacion del movimiento de farm_medicinadespachada
				$eliminaMovimiento="delete from farm_medicinadespachada where IdMedicinaDespachada=".$IdMedicinaDespachada;
				pg_query($eliminaMovimiento);
				
				if($prueba==0){
					break;
				}else{
					$restante=$restante2;
				}
			    }
			
			}//recorrido de lotes
			
		}
	//***************************************************
	}//actualizacion de inventario por cambio de cantidad
	
	
	
//**************	FIN DE MANEJO DE LOTES Y EXISTENCIAS	**********************************

function ObtenerAreaReceta($IdReceta,$IdEstablecimiento,$IdModalidad){
   $SQL="select IdArea 
         from farm_recetas 
         where IdReceta=".$IdReceta."
         and IdEstablecimiento=$IdEstablecimiento
         and IdModalidad=$IdModalidad";
   $resp=pg_fetch_array(pg_query($SQL));
   return($resp[0]);
}


function ObtenerAreaOrigenReceta($IdReceta){
   $SQL="select IdAreaOrigen from farm_recetas where IdReceta=".$IdReceta;
   $resp=pg_fetch_array(pg_query($SQL));
   return($resp[0]);
}


function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
   $SQL="select DivisorMedicina from farm_divisores 
         where IdMedicina=".$IdMedicina." and IdEstablecimiento=".$IdEstablecimiento." 
         and IdModalidad=$IdModalidad";
   $resp=pg_query($SQL);
   return($resp);
}

function ObtenerExistencia($IdMedicina,$IdArea,$Fecha,$IdEstablecimiento,$IdModalidad){
	$SQL="SELECT sum(Existencia) as Existencia
 	FROM farm_medicinaexistenciaxarea fmea
        INNER JOIN farm_lotes fl ON fmea.IdLote = fl.IdLote
	WHERE IdMedicina=".$IdMedicina."
	AND IdArea=".$IdArea."
        AND fmea.IdEstablecimiento=".$IdEstablecimiento."
        AND fmea.IdModalidad = ".$IdModalidad."
        AND left('$Fecha',7) <= left(FechaVencimiento,7)";
        /* SELECT sum(Existencia) as Existencia
 	FROM farm_medicinaexistenciaxarea 
	where IdMedicina=".$IdMedicina."
	and IdArea=".$IdArea 
        AND FechaVencimiento >= '$Fecha'*/
	$resp=pg_fetch_array(pg_query($SQL));
        if($resp[0]!='' and $resp[0]!=NULL)
        {
            if($row=pg_fetch_array($this->ValorDivisor($IdMedicina)))
            {
                $respuesta=number_format($resp[0]*$row[0],0,'.','');
            }else{
                $respuesta=$resp[0];
            }
        }else{
            $respuesta=0;
        }
	return($respuesta);
}

}//Clase RecetasProceso


?>