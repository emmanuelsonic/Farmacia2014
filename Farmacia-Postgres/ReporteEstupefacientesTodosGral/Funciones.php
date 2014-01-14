<?php

function Servicios($IdSubEspecialidad, $IdTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdEmpleado, $IdFarmacia) {

    if ($IdTerapeutico != 0) {
        $comp = "and fcp.IdTerapeutico=" . $IdTerapeutico;
    } else {
        $comp = "";
    }
    if ($IdMedicina != 0) {
        $comp2 = "and fcp.IdMedicina=" . $IdMedicina;
    } else {
        $comp2 = "";
    }
    if ($IdEmpleado != '0') {
        $comp3 = "and sec_historial_clinico.IdEmpleado='$IdEmpleado'";
    } else {
        $comp3 = "";
    }
    if ($IdFarmacia != '0') {
        $comp4 = "and farm_recetas.IdFarmacia='$IdFarmacia'";
    } else {
        $comp4 = "";
    }

    switch ($IdSubEspecialidad) {
        case 0:
            $querySelect = "select distinct msse.IdSubServicioxEstablecimiento,NombreServicio as Ubicacion, NombreSubServicio
				from mnt_subservicio mss
                                inner join mnt_subservicioxestablecimiento msse
                                on msse.IdSubServicio=mss.IdSubServicio
				inner join sec_historial_clinico
				on sec_historial_clinico.IdSubServicioxEstablecimiento=msse.IdSubServicioxEstablecimiento
				inner join farm_recetas
				on farm_recetas.IdHistorialClinico=sec_historial_clinico.IdHistorialClinico
                                inner join mnt_servicioxestablecimiento mse
                                on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
				inner join mnt_servicio ms
				on ms.IdServicio=mse.IdServicio

				inner join farm_medicinarecetada fmr
				on fmr.IdReceta=farm_recetas.IdReceta
				inner join farm_catalogoproductos fcp
				on fcp.IdMedicina=fmr.IdMedicina
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=fcp.IdMedicina


				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_recetas.Fecha between '$FechaInicio' and '$FechaFin'
				and fcpe.Estupefaciente='S'
				" . $comp . "
				" . $comp2 . "
				" . $comp3 . "
                                " . $comp4 . "
				order by mse.IdServicio,NombreSubServicio";
            break;
        default:
            $querySelect = "select distinct mnt_subservicio.IdSubServicio, NombreServicio as Ubicacion, NombreSubServicio
				from mnt_subservicio
				inner join sec_historial_clinico
				on sec_historial_clinico.IdSubServicio=mnt_subservicio.IdSubServicio
				inner join farm_recetas
				on farm_recetas.IdHistorialClinico=sec_historial_clinico.IdHistorialClinico
				inner join mnt_servicio ms
				on ms.IdServicio=mnt_subservicio.IdServicio

				inner join farm_medicinarecetada fmr
				on fmr.IdReceta=farm_recetas.IdReceta
				inner join farm_catalogoproductos fcp
				on fcp.IdMedicina=fmr.IdMedicina
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=fcp.IdMedicina

				where farm_recetas.Fecha between '$FechaInicio' and '$FechaFin'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and Estupefaciente='S'
				" . $comp . "
				" . $comp2 . "
				" . $comp3 . "
                                " . $comp4 . "
				and mnt_subservicio.IdSubServicio=" . $IdSubEspecialidad;
            break;
    }
    $resp = pg_query($querySelect);
    return($resp);
}

//Servicios

function NombreTera($grupoTerapeutico, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {

    if ($grupoTerapeutico != '0') {
        $comp3 = "and mnt_grupoterapeutico.Id='$grupoTerapeutico'";
    } else {
        $comp3 = "";
    }
    if ($IdFarmacia != '0') {
        $comp2 = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp2 = "";
    }

    $querySelect = "select distinct mnt_grupoterapeutico.id as IdTerapeutico,GrupoTerapeutico 
                        from mnt_grupoterapeutico
			inner join farm_catalogoproductos
			on farm_catalogoproductos.IdTerapeutico=mnt_grupoterapeutico.Id
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdMedicina=farm_catalogoproductos.Id
			inner join farm_recetas
			on farm_recetas.Id=farm_medicinarecetada.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico

				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.Id

			where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and farm_recetas.Fecha between '$FechaInicio' and '$FechaFin'
			
			and fcpe.Estupefaciente='S'
                        " . $comp2 . "
			" . $comp3 . "
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                        and sec_historial_clinico.IdModalidad=$IdModalidad
                        and fcpe.IdEstablecimiento=$IdEstablecimiento
                        and fcpe.IdModalidad=$IdModalidad
			order by mnt_grupoterapeutico.Id";

//
    $resp = pg_query($querySelect);
//
    return($resp);
}

//nombreTera

function QueryExterna($IdTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
//******todos los grupos terapeuticos
    if ($IdMedicina != '0') {
        $comp2 = "and farm_catalogoproductos.IdMedicina='$IdMedicina'";
    } else {
        $comp2 = "";
    }
    if ($IdFarmacia != '0') {
        $comp4 = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp4 = "";
    }

//******un grupoterapeutico especifico pero todas sus medicinas
    $querySelect = "select distinct farm_catalogoproductos.IdMedicina,Codigo,Nombre,Concentracion,FormaFarmaceutica, Presentacion
			from farm_catalogoproductos
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.Id=farm_catalogoproductos.IdTerapeutico
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdMedicina=farm_catalogoproductos.IdMedicina
			inner join farm_recetas
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
		
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina

			where mnt_grupoterapeutico.Id='$IdTerapeutico'
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and Fecha between '$FechaInicio' and '$FechaFin'
			and fcpe.Estupefaciente='S'
			" . $comp2 . "
                        " . $comp4 . "
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                        and sec_historial_clinico.IdModalidad=$IdModalidad
                        and fcpe.IdEstablecimiento=$IdEstablecimiento
                        and fcpe.IdModalidad=$IdModalidad
			order by farm_catalogoproductos.Codigo";

    $resp = pg_query($querySelect);
    return($resp);
}

//queryExterna

function ObtenerReporteGrupoTerapeutico($GrupoTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {
//**Query para un GrupoTerapeutico especifico y una Medicina Especifica
//Del Query Elimine mnt_medicinarecetada.Cantidad, a la par de farm_medicinarecetada.*,

    $selectQuery = "select distinct farm_unidadmedidas.Descripcion,farm_unidadmedidas.UnidadesContenidas as Divisor
			from farm_unidadmedidas
			inner join farm_catalogoproductos
			on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.IdUnidadMedida
			inner join farm_catalogoproductosxestablecimiento fcpe
                        on fcpe.IdMedicina = farm_catalogoproductos.IdMedicina
			where farm_catalogoproductos.IdMedicina='$IdMedicina'
                        and fcpe.IdEstablecimiento=$IdEstablecimiento
                        and fcpe.IdModalidad=$IdModalidad";
    $resp = pg_query($selectQuery);
    return($resp);
}

//fin de ObtenerReporteGrupoTerapeutico

function ObtenerRecetasSatisfechas($IdReceta, $IdMedicina, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
    /* Bandera = IdSubEspeacialidad utilizado en reporte por especialidad */

    if ($IdFarmacia != '0') {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    $querySelect = "select distinct count(farm_recetas.IdReceta) as TotalSatisfechas 
			  from farm_medicinarecetada
			  inner join farm_recetas
			  on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			  inner join sec_historial_clinico
			  on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			  
			  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
			  and (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
			  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			  and Fecha between '$FechaInicio' and '$FechaFin' 
                          and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                          and farm_medicinarecetada.IdModalidad=$IdModalidad
                          and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                          and farm_recetas.IdModalidad=$IdModalidad
                          and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                          and sec_historial_clinico.IdModalidad=$IdModalidad
                          " . $comp;
    $resp = pg_fetch_array(pg_query($querySelect));

    return($resp[0]);
}

//satisfechas
//Para Insatisfechas
function ObtenerRecetasInsatisfechas($IdReceta, $IdMedicina, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {

    if ($IdFarmacia != '0') {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    $querySelect = "select distinct count(farm_recetas.IdReceta) as TotalInsatisfechas 
			  from farm_medicinarecetada 
			  inner join farm_recetas
			  on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			  inner join sec_historial_clinico
			  on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			  
			  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
			  and farm_medicinarecetada.IdEstado='I'
			  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			  and Fecha between '$FechaInicio' and '$FechaFin' 
                          and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                          and farm_medicinarecetada.IdModalidad=$IdModalidad
                          and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                          and farm_recetas.IdModalidad=$IdModalidad
                          and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                          and sec_historial_clinico.IdModalidad=$IdModalidad
                          " . $comp;
    $resp = pg_fetch_array(pg_query($querySelect));

    return($resp[0]);
}

//Insatisfechas

function verificaSatisfecha($IdMedicina, $IdReceta) {
    if ($IdReceta == 0) {
        $querySelect = "select * from farm_medicinarecetada where IdMedicina='$IdMedicina' and (IdEstado='S' or IdEstado='')";
    } else {
        $querySelect = "select * from farm_medicinarecetada where IdReceta='$IdReceta' and IdMedicina='$IdMedicina' and (IdEstado='S' or IdEstado='')";
    }
    $resp = pg_query($querySelect);
    return($resp);
}

//verificaSatisfechos

function SumatoriaMedicamento($IdMedicina, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {

    if ($IdFarmacia != '0') {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    $querySelect = "select  sum(CantidadDespachada)/UnidadesContenidas as TotalMedicamento, PrecioLote,Lote,
                            (sum(CantidadDespachada)/UnidadesContenidas)*PrecioLote as Costo
                    from farm_recetas 
                    inner join farm_medicinarecetada
                    on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
                    inner join sec_historial_clinico
                    on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico

                    inner join farm_catalogoproductos fcp
                    on fcp.IdMedicina=farm_medicinarecetada.IdMedicina
                    inner join farm_unidadmedidas um
                    on um.IdUnidadMedida=fcp.IdUnidadMedida
                    inner join farm_medicinadespachada md
                    on md.IdMedicinaRecetada=farm_medicinarecetada.IdMedicinaRecetada
                    inner join farm_lotes l
                    on l.IdLote = md.IdLote

                    where (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
                    and farm_medicinarecetada.IdMedicina='$IdMedicina'
                    and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
                    and Fecha between '$FechaInicio' and '$FechaFin'
                    " . $comp . "
                    and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                    and farm_recetas.IdModalidad=$IdModalidad
                    and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                    and farm_medicinarecetada.IdModalidad=$IdModalidad
                    and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                    and sec_historial_clinico.IdModalidad=$IdModalidad
                    and md.IdEstablecimiento=$IdEstablecimiento
                    and md.IdModalidad=$IdModalidad
                    and l.IdEstablecimiento=$IdEstablecimiento
                    and l.IdModalidad=$IdModalidad
                    group by md.IdLote";
    $resp = pg_query($querySelect);
    return($resp);
}

function ObtenerConsumoTotalMedicamento($IdMedicina, $FechaInicio, $FechaFin) {
    $querySelect = "select sum(Cantidad)as Total
			from farm_medicinarecetada
			inner join farm_recetas
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			
			where IdMedicina='$IdMedicina'
			and farm_recetas.Fecha between '$FechaInicio' and '$FechaFin'";
    $resp = pg_fetch_array(pg_query($querySelect));
    return($resp[0]);
}

//ObtenerConsumoTotalMedicamento

function ObtenerPrecioMedicina($IdMedicina, $Ano) {
    $query = "select Precio
				from farm_preciosxano
				where IdMedicina='$IdMedicina'
				and Ano	='$Ano'";
    $resp = pg_fetch_array(pg_query($query));
    if ($resp[0] != NULL) {
        $Respuesta = $resp[0];
    } else {
        $Respuesta = 0;
    }
    return($Respuesta);
}

function ValorDivisor($IdMedicina, $IdEstablecimiento, $IdModalidad) {
    $SQL = "select DivisorMedicina 
            from farm_divisores 
            where IdMedicina= $IdMedicina
            and IdEstablecimiento=$IdEstablecimiento
            and IdModalidad=$IdModalidad";
    $resp = pg_query($SQL);
    return($resp);
}
?>
