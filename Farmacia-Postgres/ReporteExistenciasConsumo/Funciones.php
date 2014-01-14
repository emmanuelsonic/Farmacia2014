<?php

/* 	EN PRODUCCION SE DEBE FILTRAR POR IDAREA	 */

function QueryExterna($IdFarmacia, $IdArea, $grupoTerapeutico, $medicina, $IdEstablecimiento, $IdModalidad) {

    if ($IdFarmacia == 0) {
        if ($grupoTerapeutico != 0) {
            $comp = "where  mnt_grupoterapeutico.IdTerapeutico='$grupoTerapeutico'";
        } else {
            $comp = "";
        }
        if ($medicina != 0) {
            $comp2 = "and farm_catalogoproductos.IdMedicina='$medicina'";
        } else {
            $comp2 = "";
        }

        $querySelect = "select distinct farm_catalogoproductos.IdMedicina,Codigo,Nombre,Concentracion,FormaFarmaceutica, Presentacion
				from farm_catalogoproductos
				inner join mnt_grupoterapeutico
				on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
					
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina
				" . $comp . "
				" . $comp2 . "
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				order by farm_catalogoproductos.Codigo";
    }

    if ($IdFarmacia != 4 and $IdFarmacia != 0) {

        if ($IdFarmacia != 0) {
            $comp3 = "where mf.IdFarmacia='$IdFarmacia'";
        } else {
            $comp3 = "";
        }
        if ($IdArea != 0) {
            $comp4 = "and maf.IdArea='$IdArea'";
        } else {
            $comp4 = "";
        }

        if ($grupoTerapeutico != 0) {
            $comp = "and  mnt_grupoterapeutico.IdTerapeutico='$grupoTerapeutico'";
        } else {
            $comp = "";
        }
        if ($medicina != 0) {
            $comp2 = "and farm_catalogoproductos.IdMedicina='$medicina'";
        } else {
            $comp2 = "";
        }


        $querySelect = "select distinct farm_catalogoproductos.IdMedicina,Codigo,Nombre,Concentracion,FormaFarmaceutica,Presentacion
				from farm_catalogoproductos
				inner join mnt_grupoterapeutico
				on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
					
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina
					
				inner join farm_medicinaexistenciaxarea fmexa
				on fmexa.IdMedicina=fcpe.IdMedicina
				inner join mnt_areafarmacia maf
				on maf.IdArea=fmexa.IdArea
				inner join mnt_farmacia mf
				on mf.IdFarmacia=maf.IdFarmacia
				" . $comp4 . "
				" . $comp3 . "
				" . $comp . "
				" . $comp2 . "
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
                                and fmexa.IdEstablecimiento=$IdEstablecimiento
                                and fmexa.IdModalidad=$IdModalidad
                                
				order by farm_catalogoproductos.Codigo";
    }
    if ($IdFarmacia == 4) {
// MEDICAMENTO EN BODEGA
        if ($grupoTerapeutico != 0) {
            $comp = "where  mnt_grupoterapeutico.IdTerapeutico='$grupoTerapeutico'";
        } else {
            $comp = "";
        }
        if ($medicina != 0) {
            $comp2 = "and farm_catalogoproductos.IdMedicina='$medicina'";
        } else {
            $comp2 = "";
        }


        $querySelect = "select distinct farm_catalogoproductos.IdMedicina,Codigo,Nombre,Concentracion,FormaFarmaceutica,Presentacion
				from farm_catalogoproductos
				inner join mnt_grupoterapeutico
				on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
					
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina
					
				" . $comp . "
				" . $comp2 . "
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				order by farm_catalogoproductos.Codigo";
    }


    $resp = pg_query($querySelect);
    return($resp);
}

//queryExterna

function ObtenerReporteGrupoTerapeutico($IdFarmacia, $IdArea, $GrupoTerapeutico, $IdMedicina, $IdEstablecimiento, $IdModalidad) {
//**Query para un GrupoTerapeutico especifico y una Medicina Especifica
//Del Query Elimine mnt_medicinarecetada.Cantidad, a la par de farm_medicinarecetada.*,
    if ($IdFarmacia == 0) {
        $selectQuery = "select fme.IdMedicina,sum(fme.Existencia)/UnidadesContenidas as Total, Descripcion
	
			from farm_medicinaexistenciaxarea fme
			inner join farm_catalogoproductos
			on fme.IdMedicina=farm_catalogoproductos.IdMedicina
		
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join farm_unidadmedidas
			on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.IdUnidadMedida
			inner join farm_lotes l
			on l.IdLote=fme.IdLote	
								
				
				inner join mnt_areafarmacia maf
				on maf.IdArea=fme.IdArea
				inner join mnt_farmacia mf
				on mf.IdFarmacia=maf.IdFarmacia

			where mnt_grupoterapeutico.IdTerapeutico='$GrupoTerapeutico' 
			and farm_catalogoproductos.IdMedicina='$IdMedicina' 
			and left(FechaVencimiento,7) >= left(curdate(),7)
                        and fme.IdEstablecimiento=$IdEstablecimiento
                        and fme.IdModalidad=$IdModalidad
                        and l.IdEstablecimiento=$IdEstablecimiento
                        and l.IdModalidad=$IdModalidad
			group by fme.IdMedicina

                        union

                        select fem.IdMedicina,sum(Existencia)/UnidadesContenidas as Total, Descripcion
			from farm_entregamedicamento fem
			inner join farm_catalogoproductos
			on fem.IdMedicina=farm_catalogoproductos.IdMedicina
		
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join farm_unidadmedidas
			on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.IdUnidadMedida

			inner join farm_lotes l
			on l.IdLote=fem.IdLote

			where mnt_grupoterapeutico.IdTerapeutico='$GrupoTerapeutico' 
			and farm_catalogoproductos.IdMedicina='$IdMedicina' 

			and left(FechaVencimiento,7) >= left(curdate(),7)
                        and fem.IdEstablecimiento=$IdEstablecimiento
                        and fem.IdModalidad=$IdModalidad
                        and l.IdEstablecimiento=$IdEstablecimiento
                        and l.IdModalidad=$IdModalidad
			group by fem.IdMedicina";
    }


    if ($IdFarmacia != 4 and $IdFarmacia != 0) {

        if ($IdFarmacia != 0) {
            $comp = "and mf.IdFarmacia=" . $IdFarmacia;
        } else {
            $comp = "";
        }
        if ($IdArea != 0) {
            $comp2 = "and maf.IdArea=" . $IdArea;
        } else {
            $comp2 = "";
        }

        $selectQuery = "select fme.IdMedicina,sum(fme.Existencia)/UnidadesContenidas as Total, Descripcion
	
			from farm_medicinaexistenciaxarea fme
			inner join farm_catalogoproductos
			on fme.IdMedicina=farm_catalogoproductos.IdMedicina
		
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join farm_unidadmedidas
			on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.IdUnidadMedida
			inner join farm_lotes l
			on l.IdLote=fme.IdLote	
								
				
				inner join mnt_areafarmacia maf
				on maf.IdArea=fme.IdArea
				inner join mnt_farmacia mf
				on mf.IdFarmacia=maf.IdFarmacia

			where mnt_grupoterapeutico.IdTerapeutico='$GrupoTerapeutico' 
			and farm_catalogoproductos.IdMedicina='$IdMedicina' 
			" . $comp . "
			" . $comp2 . "
			and left(FechaVencimiento,7) >= left(curdate(),7)
                        and fme.IdEstablecimiento=$IdEstablecimiento
                        and fme.IdModalidad=$IdModalidad
                        and l.IdEstablecimiento=$IdEstablecimiento
                        and l.IdModalidad=$IdModalidad
			group by fme.IdMedicina";
    }

    if ($IdFarmacia == 4) {
//	EXISTENCIA EN BODEGA
        $selectQuery = "select fem.IdMedicina,sum(Existencia)/UnidadesContenidas as Total, Descripcion
			from farm_entregamedicamento fem
			inner join farm_catalogoproductos
			on fem.IdMedicina=farm_catalogoproductos.IdMedicina
		
			inner join mnt_grupoterapeutico
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join farm_unidadmedidas
			on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.IdUnidadMedida

			inner join farm_lotes l
			on l.IdLote=fem.IdLote

			where mnt_grupoterapeutico.IdTerapeutico='$GrupoTerapeutico' 
			and farm_catalogoproductos.IdMedicina='$IdMedicina' 

			and left(FechaVencimiento,7) >= left(curdate(),7)
                        and fem.IdEstablecimiento=$IdEstablecimiento
                        and fem.IdModalidad=$IdModalidad
                        and l.IdEstablecimiento=$IdEstablecimiento
                        and l.IdModalidad=$IdModalidad
			group by fem.IdMedicina";
    }

    $resp = pg_query($selectQuery);
    return($resp);
}

//fin de ObtenerReporteGrupoTerapeutico

function FechaBase() {
    $SQL = "select left(adddate(current_date,interval -1 month),7)";
    $resp = pg_fetch_array(pg_query($SQL));
    return($resp[0]);
}

function LotesMedicamento($IdFarmacia, $Medicina, $IdEstablecimiento, $IdModalidad) {
    if ($IdFarmacia == 0) {
        $SQL = "select distinct l.* 
                from farm_medicinaexistenciaxarea  fmexa
                inner join farm_lotes l
                on fmexa.IdLote=l.IdLote
                where IdMedicina=$Medicina
                and left(FechaVencimiento,7) >= left(curdate(),7)
                and l.IdEstablecimiento=$IdEstablecimiento
                and l.IdModalidad=$IdModalidad
                and fmexa.IdEstablecimiento=$IdEstablecimiento
                and fmexa.IdModalidad=$IdModalidad
                
                union 

                select distinct l.* 
                from farm_entregamedicamento  fmexa
                inner join farm_lotes l
                on fmexa.IdLote=l.IdLote
                where IdMedicina=$Medicina
                and left(FechaVencimiento,7) >= left(curdate(),7)
                and l.IdEstablecimiento=$IdEstablecimiento
                and l.IdModalidad=$IdModalidad
                and fmexa.IdEstablecimiento=$IdEstablecimiento
                and fmexa.IdModalidad=$IdModalidad
                order by FechaVencimiento asc";
    }

    if ($IdFarmacia != 4 and $IdFarmacia != 0) {
        $SQL = "select distinct l.* 
                from farm_medicinaexistenciaxarea  fmexa
                inner join farm_lotes l
                on fmexa.IdLote=l.IdLote
                where IdMedicina=" . $Medicina . "
                and left(FechaVencimiento,7) >= left(curdate(),7)
                and l.IdEstablecimiento=$IdEstablecimiento
                and l.IdModalidad=$IdModalidad
                and fmexa.IdEstablecimiento=$IdEstablecimiento
                and fmexa.IdModalidad=$IdModalidad
                order by FechaVencimiento asc";
    }

    if ($IdFarmacia == 4) {
        $SQL = "select distinct l.* 
                from farm_entregamedicamento  fmexa
                inner join farm_lotes l
                on fmexa.IdLote=l.IdLote
                where IdMedicina=" . $Medicina . "
                and left(FechaVencimiento,7) >= left(curdate(),7)
                and l.IdEstablecimiento=$IdEstablecimiento
                and l.IdModalidad=$IdModalidad
                and fmexa.IdEstablecimiento=$IdEstablecimiento
                and fmexa.IdModalidad=$IdModalidad
                order by FechaVencimiento asc";
    }

    $resp = pg_query($SQL);
    return($resp);
}

function ObtenerRecetasSatisfechas($IdReceta, $IdMedicina, $FechaInicio, $FechaFin, $IdArea, $Bandera, $IdMedico) {
    /* Bandera = IdSubEspeacialidad utilizado en reporte por especialidad */
    if ($Bandera == 0) {
        $querySelect = "select count( farm_recetas.IdReceta) as TotalSatisfechas 
			  from farm_medicinarecetada
			  inner join farm_recetas
			  on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
			  and (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
			  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			  and Fecha between '$FechaInicio' and '$FechaFin' 
			  and farm_recetas.IdArea='$IdArea'
			  ";
    } else {

        if ($IdMedico == '0') {
            $querySelect = "select distinct count(farm_recetas.IdReceta) as TotalSatisfechas 
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
		  and (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
		  and mnt_subespecialidad.IdSubEspecialidad='$Bandera'
		  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
		  and Fecha between '$FechaInicio' and '$FechaFin' 
		  and farm_recetas.IdArea='$IdArea'
		  ";
        } else {
            $querySelect = "select distinct count(farm_recetas.IdReceta) as TotalSatisfechas 
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
		  and (farm_medicinarecetada.IdEstado='S' or farm_medicinarecetada.IdEstado='')
		  and mnt_subespecialidad.IdSubEspecialidad='$Bandera'
		  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
		  and Fecha between '$FechaInicio' and '$FechaFin' 
		  and farm_recetas.IdArea='$IdArea'
		  and mnt_empleados.IdEmpleado='$IdMedico' 
		  ";
        }
    }
    $resp = pg_fetch_array(pg_query($querySelect));

    return($resp[0]);
}

//satisfechas
//Para Insatisfechas
function ObtenerRecetasInsatisfechas($IdReceta, $IdMedicina, $FechaInicio, $FechaFin, $IdArea, $Bandera, $IdMedico) {
    if ($Bandera == 0) {
        $querySelect = "select distinct count(farm_recetas.IdReceta) as TotalInsatisfechas 
			  from farm_medicinarecetada 
			  inner join farm_recetas
			  on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			  where farm_medicinarecetada.IdMedicina='$IdMedicina' 
			  and farm_medicinarecetada.IdEstado='I'
			  and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			  and Fecha between '$FechaInicio' and '$FechaFin' 
			  and farm_recetas.IdArea='$IdArea'
			  ";
    } else {

        if ($IdMedico == '0') {
            $querySelect = "select distinct count(farm_recetas.IdReceta) as TotalInsatisfechas 
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
				  and farm_recetas.IdArea='$IdArea'
				 ";
        } else {
            $querySelect = "select distinct count(farm_recetas.IdReceta) as TotalInsatisfechas 
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
				  and farm_recetas.IdArea='$IdArea' 
				  and mnt_empleados.IdEmpleado='$IdMedico' 
				  ";
        }//else IF medico
    }

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

function NumeroRecetasTotal($IdMedicina, $IdArea, $FechaInicio, $FechaFin) {
    $querySelect = "select  count(farm_recetas.IdReceta)as TotalRecetas
				from farm_recetas 
				inner join farm_medicinarecetada
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				where farm_recetas.IdArea='$IdArea'
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and Fecha between '$FechaInicio' and '$FechaFin'";
    $resp = pg_fetch_array(pg_query($querySelect));
    return($resp[0]);
}

function SumatoriaMedicamento($IdFarmacia, $IdArea, $IdMedicina, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {

    if ($IdFarmacia != 0) {
        $comp = " and IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }
    if ($IdArea != 0) {
        $comp2 = "and IdAreaOrigen=" . $IdArea;
    } else {
        $comp2 = "";
    }

    $querySelect = "select  (sum(CantidadDespachada)/UnidadesContenidas) as TotalMedicamento, PrecioLote,
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
			and farm_medicinarecetada.IdMedicina='$IdMedicina'
			and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
			and Fecha between '$FechaInicio' and '$FechaFin'
			" . $comp . "
			" . $comp2 . "
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad
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

function ObtenerAreasFarmacia($IdFarmacia, $IdArea, $FechaInicio, $FechaFin) {

    if ($IdFarmacia == '0') {
        $query = "select distinct mnt_areafarmacia.IdArea,Area 
				from mnt_areafarmacia 
				inner join farm_recetas
				on farm_recetas.IdArea=mnt_areafarmacia.IdArea
				where Fecha  between '$FechaInicio' and '$FechaFin'
				and Habilitado='S'";
    }
    if ($IdFarmacia != 0 and $IdArea == 0) {
        $query = "select distinct mnt_areafarmacia.IdArea,Area 
				from mnt_areafarmacia 
				inner join farm_recetas
				on farm_recetas.IdArea=mnt_areafarmacia.IdArea
				where Fecha  between '$FechaInicio' and '$FechaFin'
				and farm_recetas.IdFarmacia='$IdFarmacia'
				and Habilitado='S'";
    }

    if ($IdArea != 0) {
        $query = "select IdArea,Area from mnt_areafarmacia where IdArea=" . $IdArea;
    }

    $resp = pg_query($query);
    return($resp);
}

function Insatisfecha($IdFarmacia, $IdArea, $IdMedicina, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {

    if ($IdFarmacia != 0) {
        $comp = " and fr.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }
    if ($IdArea != 0) {
        $comp2 = "and fr.IdArea=" . $IdArea;
    } else {
        $comp2 = "";
    }

    $SQL = "select count(fmr.IdReceta) as Total
		from farm_medicinarecetada fmr
		inner join farm_recetas fr
		on fr.IdReceta=fmr.IdReceta
		inner join sec_historial_clinico shc
		on shc.IdHistorialClinico=fr.IdHistorialClinico
		
		where fr.IdEstado='E'
		and fmr.IdEstado='I'
		and Fecha between '$FechaInicio' and '$FechaFin'
		" . $comp . "
		" . $comp2 . "
		and IdMedicina= $IdMedicina
                and fmr.IdEstablecimiento=$IdEstablecimiento
                and fmr.IdModalidad=$IdModalidad
                and shc.IdEstablecimiento=$IdEstablecimiento
                and shc.IdModalidad=$IdModalidad
                ";
    $resp = pg_fetch_array(pg_query($SQL));
    return($resp[0]);
}

function InsatisfechasEstimadas($IdMedicina, $FechaInicial, $FechaFinal, $IdEstablecimiento, $IdModalidad) {


    $SQL = "select *, datediff(FechaFin,FechaInicio) as DiferenciaDias
		from farm_periododesabastecido
		where (FechaInicio between '$FechaInicial' and '$FechaFinal' or FechaFin between '$FechaInicial' and '$FechaFinal')
		and IdMedicina=$IdMedicina
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad
                ";
    $resp = pg_query($SQL);
    return ($resp);
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