<?php

function GrupoTerapeutico($IdMedicina, $IdEstablecimiento, $IdModalidad) {
    $inner = "";
    $where = "";
    if ($IdMedicina == 0) {
        $inner = "inner join farm_catalogoproductos 
                  on farm_catalogoproductos.IdTerapeutico=mnt_grupoterapeutico.Id
                  inner join farm_catalogoproductosxestablecimiento fcpe
                  on fcpe.IdMedicina=farm_catalogoproductos.Id";
        $where = "and fcpe.Estupefaciente='S'
                  and fcpe.IdEstablecimiento=$IdEstablecimiento
                  and fcpe.IdModalidad=$IdModalidad";
    } else {
        $inner = "inner join farm_catalogoproductos 
                  on farm_catalogoproductos.IdTerapeutico=mnt_grupoterapeutico.Id
                  inner join farm_catalogoproductosxestablecimiento fcpe
                  on fcpe.IdMedicina=farm_catalogoproductos.Id";
        $where = "and fcpe.IdMedicina='$IdMedicina' 
                  and fcpe.Estupefaciente='S'
                  and fcpe.IdEstablecimiento=$IdEstablecimiento
                  and fcpe.IdModalidad=$IdModalidad";
    }

    $query = "select distinct mnt_grupoterapeutico.id as IdTerapeutico,GrupoTerapeutico
			from mnt_grupoterapeutico
			" . $inner . "
			where GrupoTerapeutico <>'--'
			" . $where;
    $resp = pg_query($query);
    return($resp);
}

function ObtenerReporteEspecialidades($GrupoTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdSubEspecialidad, $IdMedico, $IdArea, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
    /* Obtener IdMedicamento validado por medio de especialidad y/o medico de un grupo terapeutico */
    //Si hay Area no se necesita Farmacia	
    if ($IdFarmacia == 0 or $IdArea == 0) {
        $FiltroArea = "";
        $FiltroFarmacia = "";
    }
    if ($IdFarmacia != 0) {
        $FiltroFarmacia = "and farm_recetas.IdFarmacia='$IdFarmacia'";
    }
    if ($IdArea != 0 and $IdFarmacia != 0) {
        $FiltroArea = "and farm_recetas.IdAreaOrigen='$IdArea'";
    }


    if ($IdMedico != '0') {
        $Complemento1 = "and sec_historial_clinico.IdEmpleado='$IdMedico'";
    } else {
        $Complemento1 = "";
    }

    if ($IdMedicina != 0) {
        $Complemento2 = "and farm_medicinarecetada.IdMedicina='$IdMedicina'";
    } else {
        $Complemento2 = "";
    }

    if ($IdSubEspecialidad != 0) {
        $FiltroSubEspecialidad = "and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'";
    } else {
        $FiltroSubEspecialidad = "";
    }

    $query = "select distinct farm_medicinarecetada.IdMedicina
			from farm_recetas
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			inner join farm_catalogoproductos
			on farm_catalogoproductos.Id=farm_medicinarecetada.IdMedicina

				
				inner join farm_catalogoproductos fcp
				on fcp.Id=farm_medicinarecetada.IdMedicina
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=fcp.IdMedicina

			
			where farm_recetas.Fecha between '$FechaInicio' and '$FechaFin'
			and farm_catalogoproductos.IdTerapeutico='$GrupoTerapeutico'
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and fcpe.Estupefaciente='S'
			" . $FiltroFarmacia . "
			" . $FiltroArea . "
			" . $FiltroSubEspecialidad . "
			" . $Complemento1 . "
			" . $Complemento2 . "
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad
                        and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                        and sec_historial_clinico.IdModalidad=$IdModalidad
                        and fcpe.IdEstablecimiento=$IdEstablecimiento
                        and fcpe.IdModalidad=$IdModalidad
                        
			order by fcp.Codigo";
    $resp = pg_query($query);
    return($resp);
}

function ObtenerTotalRecetas($IdMedicina, $IdArea, $IdSubEspecialidad, $IdMedico, $FechaInicio, $FechaFin, $IdFarmacia) {
    if ($IdMedico != '0') {
        $Complemento1 = "and sec_historial_clinico.IdEmpleado='$IdMedico'";
    } else {
        $Complemento1 = "";
    }
    if ($IdMedicina != 0) {
        $Complemento2 = "and farm_medicinarecetada.IdMedicina='$IdMedicina'";
    } else {
        $Complemento2 = "";
    }
    if ($IdFarmacia == 0 or $IdArea == 0) {
        $FiltroArea = "";
        $FiltroFarmacia = "";
    }
    if ($IdFarmacia != 0) {
        $FiltroFarmacia = "and farm_recetas.IdFarmacia='$IdFarmacia'";
    }
    if ($IdArea != 0 and $IdFarmacia != 0) {
        $FiltroArea = "and farm_recetas.IdAreaOrigen='$IdArea'";
    }



    if ($IdSubEspecialidad != 0) {
        $FiltroSubEspecialidad = "and sec_historial_clinico.IdSubServicio='$IdSubEspecialidad'";
    } else {
        $FiltroSubEspecialidad = "";
    }

    $query = "select  count(farm_medicinarecetada.IdMedicina) as TotalRecetas
			from farm_recetas 
			inner join farm_medicinarecetada
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			inner join mnt_empleados
			on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
			where  farm_medicinarecetada.IdMedicina='$IdMedicina'
			" . $FiltroFarmacia . "
			" . $FiltroArea . "
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			" . $FiltroSubEspecialidad . "

			and Fecha between '$FechaInicio' and '$FechaFin'
			" . $Complemento1 . "
			" . $Complemento2;

    $resp = pg_fetch_array(pg_query($query));
    return($resp[0]);
}

function ObtenerRecetasSatisfechas($IdMedicina, $FechaInicio, $FechaFin, $IdArea, $IdSubEspecialidad, $IdMedico, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
    if ($IdMedico != '0') {
        $Complemento1 = "and sec_historial_clinico.IdEmpleado='$IdMedico'";
    } else {
        $Complemento1 = "";
    }
    if ($IdMedicina != 0) {
        $Complemento2 = "and farm_medicinarecetada.IdMedicina='$IdMedicina'";
    } else {
        $Complemento2 = "";
    }
    if ($IdFarmacia == 0 or $IdArea == 0) {
        $FiltroArea = "";
        $FiltroFarmacia = "";
    }
    if ($IdFarmacia != 0) {
        $FiltroFarmacia = "and farm_recetas.IdFarmacia='$IdFarmacia'";
    }
    if ($IdArea != 0 and $IdFarmacia != 0) {
        $FiltroArea = "and farm_recetas.IdAreaOrigen='$IdArea'";
    }

    if ($IdSubEspecialidad != 0) {
        $FiltroSubEspecialidad = "and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'";
    } else {
        $FiltroSubEspecialidad = "";
    }

    $query = "select  count(farm_medicinarecetada.IdMedicina) as TotalRecetas
			from farm_recetas 
			inner join farm_medicinarecetada
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			inner join mnt_empleados
			on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
			where farm_medicinarecetada.IdMedicina='$IdMedicina'
			" . $FiltroFarmacia . "
			" . $FiltroArea . "
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and  (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
			" . $FiltroSubEspecialidad . "

			and Fecha between '$FechaInicio' and '$FechaFin'
			" . $Complemento1 . "
			" . $Complemento2 . "
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad
                        and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                        and sec_historial_clinico.IdModalidad=$IdModalidad
                        
                        ";
    $resp = pg_fetch_array(pg_query($query));
    return($resp[0]);
}

function ObtenerRecetasInsatisfechas($IdMedicina, $FechaInicio, $FechaFin, $IdArea, $IdSubEspecialidad, $IdMedico, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
    if ($IdMedico != '0') {
        $Complemento1 = "and sec_historial_clinico.IdEmpleado='$IdMedico'";
    } else {
        $Complemento1 = "";
    }
    if ($IdMedicina != 0) {
        $Complemento2 = "and farm_medicinarecetada.IdMedicina='$IdMedicina'";
    } else {
        $Complemento2 = "";
    }
    if ($IdFarmacia == 0 or $IdArea == 0) {
        $FiltroArea = "";
        $FiltroFarmacia = "";
    }
    if ($IdFarmacia != 0) {
        $FiltroFarmacia = "and farm_recetas.IdFarmacia='$IdFarmacia'";
    }
    if ($IdArea != 0 and $IdFarmacia != 0) {
        $FiltroArea = "and farm_recetas.IdAreaOrigen='$IdArea'";
    }

    if ($IdSubEspecialidad != 0) {
        $FiltroSubEspecialidad = "and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'";
    } else {
        $FiltroSubEspecialidad = "";
    }

    $query = "select  count(farm_medicinarecetada.IdMedicina) as TotalRecetas
			from farm_recetas 
			inner join farm_medicinarecetada
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			inner join mnt_empleados
			on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
			where farm_medicinarecetada.IdMedicina='$IdMedicina'
			" . $FiltroFarmacia . "
			" . $FiltroArea . "
			
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and farm_medicinarecetada.IdEstado='I'
			" . $FiltroSubEspecialidad . "

			and Fecha between '$FechaInicio' and '$FechaFin'
			" . $Complemento1 . "
			" . $Complemento2 . "
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad
                        and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                        and sec_historial_clinico.IdModalidad=$IdModalidad
                        
                        ";
    $resp = pg_fetch_array(pg_query($query));
    return($resp[0]);
}

function SumatoriaMedicamento($IdMedicina, $IdArea, $IdMedico, $IdSubEspecialidad, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
    if ($IdMedico == '0') {
        $Complemento = "";
    } else {
        $Complemento = "and sec_historial_clinico.IdEmpleado='$IdMedico'";
    }

    if ($IdFarmacia == 0 or $IdArea == 0) {
        $FiltroArea = "";
        $FiltroFarmacia = "";
    }
    if ($IdFarmacia != 0) {
        $FiltroFarmacia = "and farm_recetas.IdFarmacia='$IdFarmacia'";
    }
    if ($IdArea != 0 and $IdFarmacia != 0) {
        $FiltroArea = "and farm_recetas.IdAreaOrigen='$IdArea'";
    }

    if ($IdSubEspecialidad != 0) {
        $FiltroSubEspecialidad = "and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'";
    } else {
        $FiltroSubEspecialidad = "";
    }


    $querySelect = "select  sum(CantidadDespachada)/UnidadesContenidas as TotalMedicamento, PrecioLote,Lote,
                            (sum(CantidadDespachada)/UnidadesContenidas)*PrecioLote as Costo
			from farm_recetas 
			inner join farm_medicinarecetada
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			inner join sec_historial_clinico
			on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
			inner join mnt_empleados
			on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
			inner join farm_medicinadespachada md
			on md.IdMedicinaRecetada=farm_medicinarecetada.IdMedicinaRecetada
			inner join farm_lotes l
			on l.IdLote=md.IdLote
			inner join farm_catalogoproductos fcp
			on fcp.IdMedicina = farm_medicinarecetada.IdMedicina
			inner join farm_unidadmedidas um
			on um.IdUnidadMedida=fcp.IdUnidadMedida

			where (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
			" . $FiltroFarmacia . "
			" . $FiltroArea . "
			and farm_medicinarecetada.IdMedicina='$IdMedicina'
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			" . $FiltroSubEspecialidad . "
			and Fecha between '$FechaInicio' and '$FechaFin'
			" . $Complemento . "
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

function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad) {
    $SQL = "select DivisorMedicina 
                from farm_divisores 
                where IdMedicina=$IdMedicina
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
    $resp = pg_query($SQL);
    return($resp);
}

?>
