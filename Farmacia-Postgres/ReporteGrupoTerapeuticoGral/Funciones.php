<?php 
/*	EN PRODUCCION SE DEBE FILTRAR POR IDAREA	*/
function QueryExterna($IdFarmacia,$grupoTerapeutico, $medicina, $IdArea,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad){
if($grupoTerapeutico!=0){$comp="and mnt_grupoterapeutico.IdTerapeutico='$grupoTerapeutico'";}else{$comp="";}
if($medicina!=0){$comp2="and farm_catalogoproductos.IdMedicina='$medicina'";}else{$comp2="";}


$querySelect="select distinct farm_catalogoproductos.IdMedicina,Codigo,Nombre,Concentracion,FormaFarmaceutica, Presentacion
			from farm_catalogoproductos
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdMedicina=farm_catalogoproductos.IdMedicina
			inner join farm_recetas
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta

				
				
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina

			
			where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and farm_recetas.IdAreaOrigen='$IdArea'
			and farm_recetas.IdFarmacia='$IdFarmacia'
			and Fecha between '$FechaInicio' and '$FechaFin'
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and fcpe.IdEstablecimiento=$IdEstablecimiento
                        and fcpe.IdModalidad=$IdModalidad
			".$comp."
			".$comp2."
			order by farm_catalogoproductos.Codigo";


$resp=pg_query($querySelect);
return($resp);
}//queryExterna


function ObtenerReporteGrupoTerapeutico($IdFarmacia,$GrupoTerapeutico,$IdMedicina,$FechaInicio,$FechaFin,$IdArea,$IdEstablecimiento,$IdModalidad){
//**Query para un GrupoTerapeutico especifico y una Medicina Especifica
//Del Query Elimine mnt_medicinarecetada.Cantidad, a la par de farm_medicinarecetada.*,

$selectQuery="select distinct farm_recetas.IdReceta, 
			farm_unidadmedidas.Descripcion,farm_unidadmedidas.UnidadesContenidas as Divisor
			from farm_recetas
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
			inner join farm_catalogoproductos
			on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join farm_unidadmedidas
			on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.IdUnidadMedida
			
			where mnt_grupoterapeutico.IdTerapeutico='$GrupoTerapeutico' 
			and farm_medicinarecetada.IdMedicina='$IdMedicina' 
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and Fecha between '$FechaInicio' and '$FechaFin' 
			and farm_recetas.IdAreaOrigen='$IdArea' 
			and farm_recetas.IdFarmacia='$IdFarmacia'
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
			order by farm_catalogoproductos.IdMedicina";
$resp=pg_query($selectQuery);
return($resp);
}//fin de ObtenerReporteGrupoTerapeutico



function ObtenerRecetasSatisfechas($IdFarmacia,$IdReceta,$IdMedicina,$FechaInicio,$FechaFin,$IdArea,$Bandera,$IdMedico,$IdEstablecimiento,$IdModalidad){
/*Bandera = IdSubEspeacialidad utilizado en reporte por especialidad*/
if($Bandera==0){
$querySelect="select count( farm_recetas.IdReceta) as TotalSatisfechas 
			  from farm_medicinarecetada
			  inner join farm_recetas
			  on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
			  and (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
			  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			  and Fecha between '$FechaInicio' and '$FechaFin'
                          and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                          and farm_recetas.IdModalidad=$IdModalidad
			  and farm_recetas.IdAreaOrigen='$IdArea'
			  and farm_recetas.IdFarmacia='$IdFarmacia'
			  ";
}else{

	if($IdMedico=='0'){
$querySelect="select distinct count(farm_recetas.IdReceta) as TotalSatisfechas 
			from farm_recetas
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
			inner join farm_catalogoproductos
			on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			inner join mnt_empleados
			on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
			
		  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
		  and (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
		  and sec_historial_clinico.IdSubServicioxEstablecimiento='$Bandera'
		  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
		  and Fecha between '$FechaInicio' and '$FechaFin' 
                  and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                  and sec_historial_clinico.IdModalidad=$IdModalidad
                  and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                  and farm_recetas.IdModalidad=$IdModalidad
		  and farm_recetas.IdAreaOrigen='$IdArea'
		  and farm_recetas.IdFarmacia='$IdFarmacia'
		  ";
    }else{
$querySelect="select distinct count(farm_recetas.IdReceta) as TotalSatisfechas 
			from farm_recetas
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
			inner join farm_catalogoproductos
			on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			inner join mnt_empleados
			on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
			
		  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
		  and (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
		  and sec_historial_clinico.IdSubServicioxEstablecimiento='$Bandera'
		  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
                  and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                  and sec_historial_clinico.IdModalidad=$IdModalidad
                  and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                  and farm_recetas.IdModalidad=$IdModalidad
		  and Fecha between '$FechaInicio' and '$FechaFin' 
		  and farm_recetas.IdAreaOrigen='$IdArea'
		  and mnt_empleados.IdEmpleado='$IdMedico' 
		  and farm_recetas.IdFarmacia='$IdFarmacia'
		  ";	
	}
}
$resp=pg_fetch_array(pg_query($querySelect));

return($resp[0]);
}//satisfechas


//Para Insatisfechas
function ObtenerRecetasInsatisfechas($IdFarmacia,$IdReceta,$IdMedicina,$FechaInicio,$FechaFin,$IdArea,$Bandera,$IdMedico,$IdEstablecimiento,$IdModalidad){
if($Bandera==0){
$querySelect="select distinct count(farm_recetas.IdReceta) as TotalInsatisfechas 
			  from farm_medicinarecetada 
			  inner join farm_recetas
			  on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
			  and farm_medicinarecetada.IdEstado='I'
			  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			  and Fecha between '$FechaInicio' and '$FechaFin' 
                          and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                          and farm_recetas.IdModalidad=$IdModalidad
			  and farm_recetas.IdAreaOrigen='$IdArea'
			  and farm_recetas.IdFarmacia='$IdFarmacia'
			  ";}
else{

	if($IdMedico=='0'){
		$querySelect="select distinct count(farm_recetas.IdReceta) as TotalInsatisfechas 
					from farm_recetas
					inner join farm_medicinarecetada
					on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
					inner join mnt_grupoterapeutico
					on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
					inner join sec_historial_clinico
					on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
					inner join mnt_empleados
					on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
					inner join mnt_subespecialidad
					on mnt_subespecialidad.IdSubEspecialidad=mnt_empleados.IdSubEspecialidad
				  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
				  and farm_medicinarecetada.IdEstado='I'
				  and mnt_subespecialidad.IdSubEspecialidad='$Bandera'
				  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				  and Fecha between '$FechaInicio' and '$FechaFin' 
				  and farm_recetas.IdAreaOrigen='$IdArea'
				  and farm_recetas.IdFarmacia='$IdFarmacia'
				 ";
    }else{
		$querySelect="select distinct count(farm_recetas.IdReceta) as TotalInsatisfechas 
					from farm_recetas
					inner join farm_medicinarecetada
					on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
					inner join mnt_grupoterapeutico
					on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
					inner join sec_historial_clinico
					on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
					inner join mnt_empleados
					on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
					inner join mnt_subespecialidad
					on mnt_subespecialidad.IdSubEspecialidad=mnt_empleados.IdSubEspecialidad
				  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
				  and farm_medicinarecetada.IdEstado='I'
				  and mnt_subespecialidad.IdSubEspecialidad='$Bandera'
				  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				  and Fecha between '$FechaInicio' and '$FechaFin' 
				  and farm_recetas.IdAreaOrigen='$IdArea' 
				  and mnt_empleados.IdEmpleado='$IdMedico' 
				  and farm_recetas.IdFarmacia='$IdFarmacia'
				  ";
		}//else IF medico
}

$resp=pg_fetch_array(pg_query($querySelect));

return($resp[0]);
}//Insatisfechas


function verificaSatisfecha($IdMedicina,$IdReceta){
	if ($IdReceta==0){
		$querySelect="select * from farm_medicinarecetada where IdMedicina='$IdMedicina' and (IdEstado='S' or IdEstado='')";
	}else{
		$querySelect="select * from farm_medicinarecetada where IdReceta='$IdReceta' and IdMedicina='$IdMedicina' and (IdEstado='S' or IdEstado='')";
	}
	$resp=pg_query($querySelect);
	return($resp);
}//verificaSatisfechos


function NumeroRecetasTotal($IdMedicina,$IdArea,$FechaInicio,$FechaFin){
	$querySelect="select  count(farm_recetas.IdReceta)as TotalRecetas
				from farm_recetas 
				inner join farm_medicinarecetada
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				where farm_recetas.IdAreaOrigen='$IdArea'
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and Fecha between '$FechaInicio' and '$FechaFin'";
	$resp=pg_fetch_array(pg_query($querySelect));
	return($resp[0]);
}



function SumatoriaMedicamento($IdFarmacia,$IdMedicina,$IdArea,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad){
	$querySelect="select  (sum(CantidadDespachada)/UnidadesContenidas) as TotalMedicamento, PrecioLote,
		    ((sum(CantidadDespachada)/UnidadesContenidas)*PrecioLote) as Costo,Lote,PrecioLote
			from farm_recetas 
			inner join farm_medicinarecetada
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			inner join farm_medicinadespachada md
			on md.IdMedicinaRecetada = farm_medicinarecetada.IdMedicinaRecetada
			inner join farm_lotes l
			on l.IdLote = md.IdLote
			inner join farm_catalogoproductos cp
			on cp.IdMedicina = farm_medicinarecetada.IdMedicina
			inner join farm_unidadmedidas um
			on um.IdUnidadMedida = cp.IdUnidadMedida
			where (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
			and farm_recetas.IdAreaOrigen='$IdArea'
			and farm_recetas.IdFarmacia='$IdFarmacia'
			and farm_medicinarecetada.IdMedicina='$IdMedicina'
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and Fecha between '$FechaInicio' and '$FechaFin'
                        and md.IdEstablecimiento=$IdEstablecimiento
                        and md.IdModalidad=$IdModalidad
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and l.IdEstablecimiento=$IdEstablecimiento
                        and l.IdModalidad=$IdModalidad
			group by md.IdLote";
	$resp=pg_query($querySelect);
	return($resp);
}

function ObtenerPrecioMedicina($IdMedicina,$Ano){
		$query="select Precio
				from farm_preciosxano
				where IdMedicina='$IdMedicina'
				and Ano	='$Ano'";
		$resp=pg_fetch_array(pg_query($query));
		if($resp[0]!=NULL){$Respuesta=$resp[0];}else{$Respuesta=0;}
		return($Respuesta);
}

function ObtenerAreasFarmacia($IdFarmacia,$IdArea,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad){
	if($IdArea==0){$comp="";}else{$comp=" and IdAreaOrigen=".$IdArea;}
		$query="select distinct mnt_areafarmacia.id as IdArea,Area,farm_recetas.IdFarmacia
				from mnt_areafarmacia 
				inner join farm_recetas
				on farm_recetas.IdAreaOrigen=mnt_areafarmacia.Id
				where farm_recetas.IdFarmacia='$IdFarmacia'
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
				".$comp."
				and Fecha  between '$FechaInicio' and '$FechaFin'";
	
	
	$resp=pg_query($query);
	return($resp);
}

	function InsatisfechasEstimadas($IdMedicina,$FechaInicial,$FechaFinal){
	   $SQL="select *
		from farm_periododesabastecido
		where (FechaInicio between '$FechaInicial' and '$FechaFinal' or FechaFin between '$FechaInicial' and '$FechaFinal')
		and IdMedicina=".$IdMedicina;
	   $resp=pg_query($SQL);
	   return ($resp);
	}

	function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
	   $SQL="select DivisorMedicina 
                    from farm_divisores 
                    where IdMedicina=$IdMedicina
                   and IdEstablecimiento=$IdEstablecimiento
                   and IdModalidad=$IdModalidad";
	   $resp=pg_query($SQL);
	   return($resp);
    	}

?>