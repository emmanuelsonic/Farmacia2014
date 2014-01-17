<?php
include('../../Clases/class.php');
class Actualizaciones{
	function DatosGenerales($pagina,$IdEstablecimiento,$IdModalidad){
		
            $querySelect="SELECT mnt_3.id as IdSubServicio,condicion AS HabilitadoFarmacia, codigo_farmacia,
                   CASE
                   WHEN mnt_3.nombre_ambiente IS NOT NULL
                   THEN  
                    CASE WHEN mnt_ser.abreviatura IS NOT NULL
                    THEN ctl_a.nombre ||'-->'|| mnt_ser.abreviatura ||'-->' ||mnt_3.nombre_ambiente
                    ELSE ctl_a.nombre ||'-->' ||mnt_3.nombre_ambiente
                    END

                   ELSE
                    CASE WHEN mnt_ser.abreviatura IS NOT NULL
                    THEN ctl_a.nombre ||'-->'|| mnt_ser.abreviatura ||'-->' || ctl.nombre
                    ELSE ctl_a.nombre ||'-->' || ctl.nombre
                    END
                   END AS SubServicio

                   FROM mnt_aten_area_mod_estab mnt_3
                   INNER JOIN mnt_area_mod_estab mnt_2 on mnt_3.id_area_mod_estab = mnt_2.id
                   INNER JOIN ctl_atencion ctl on mnt_3.id_atencion = ctl.id
                   INNER JOIN ctl_area_atencion ctl_a on mnt_2.id_area_atencion = ctl_a.id
                   LEFT JOIN mnt_servicio_externo_establecimiento mnt_ser_estab on mnt_2.id_servicio_externo_estab = mnt_ser_estab.id
                   LEFT JOIN mnt_servicio_externo mnt_ser on mnt_ser_estab.id_servicio_externo = mnt_ser.id
                   INNER JOIN mnt_modalidad_establecimiento mnt_mod_estab on mnt_2.id_modalidad_estab = mnt_mod_estab.id
                   INNER JOIN ctl_modalidad ctl_mod on mnt_mod_estab.id_modalidad = ctl_mod.id
                   WHERE ctl_mod.id =$IdModalidad
                   AND mnt_3.id_establecimiento =".$IdEstablecimiento."
                   AND mnt_3.Condicion='H'
			order by mnt_3.id
					LIMIT 20 offset $pagina";
            
           /*estado anterior de la consulta 
            $querySelect="select mss.IdSubServicio, msse.Condicion as HabilitadoFarmacia,
                        msse.CodigoFarmacia, concat_ws(' - ',NombreServicio,NombreSubServicio) as SubServicio
			from mnt_subservicio mss
			inner join mnt_subservicioxestablecimiento msse
			on mss.IdSubServicio=msse.IdSubServicio
			inner join mnt_servicioxestablecimiento mse
			on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                        inner join mnt_servicio ms
                        on ms.IdServicio = mse.IdServicio

			where msse.IdEstablecimiento=".$IdEstablecimiento."
                        and msse.IdModalidad=$IdModalidad
			and msse.Condicion='H'
			order by ms.IdServicio,NombreSubServicio
					LIMIT 20 offset $pagina";
            * */
		$resp=pg_query($querySelect);
		return($resp);		
	}//Datos Generales
	
	function BusquedaMedico($CodigoFarmacia,$NombreSubEspecialidad,$IdEstablecimiento,$IdModalidad){
	if($CodigoFarmacia !=''){
		$filtro="mnt_3.Codigo_Farmacia='$CodigoFarmacia'";
	}
	if($NombreSubEspecialidad!=''){
		$filtro="(ctl.nombre ilike '%$NombreSubEspecialidad%' or mnt_3.nombre_ambiente ilike '%$NombreSubEspecialidad%')";
	}
            $querySelect="SELECT mnt_3.id as IdSubServicio,condicion AS HabilitadoFarmacia, codigo_farmacia,
                   CASE
                   WHEN mnt_3.nombre_ambiente IS NOT NULL
                   THEN  
                    CASE WHEN mnt_ser.abreviatura IS NOT NULL
                    THEN ctl_a.nombre ||'-->'|| mnt_ser.abreviatura ||'-->' ||mnt_3.nombre_ambiente
                    ELSE ctl_a.nombre ||'-->' ||mnt_3.nombre_ambiente
                    END
                   ELSE
                    CASE WHEN mnt_ser.abreviatura IS NOT NULL
                    THEN ctl_a.nombre ||'-->'|| mnt_ser.abreviatura ||'-->' || ctl.nombre
                    ELSE ctl_a.nombre ||'-->' || ctl.nombre
                    END
                   END AS SubServicio
                   FROM mnt_aten_area_mod_estab mnt_3
                   INNER JOIN mnt_area_mod_estab mnt_2 on mnt_3.id_area_mod_estab = mnt_2.id
                   INNER JOIN ctl_atencion ctl on mnt_3.id_atencion = ctl.id
                   INNER JOIN ctl_area_atencion ctl_a on mnt_2.id_area_atencion = ctl_a.id
                   LEFT JOIN mnt_servicio_externo_establecimiento mnt_ser_estab on mnt_2.id_servicio_externo_estab = mnt_ser_estab.id
                   LEFT JOIN mnt_servicio_externo mnt_ser on mnt_ser_estab.id_servicio_externo = mnt_ser.id
                   INNER JOIN mnt_modalidad_establecimiento mnt_mod_estab on mnt_2.id_modalidad_estab = mnt_mod_estab.id
                   INNER JOIN ctl_modalidad ctl_mod on mnt_mod_estab.id_modalidad = ctl_mod.id
                   WHERE ctl_mod.id =$IdModalidad
                   AND mnt_3.id_establecimiento =".$IdEstablecimiento."
                   AND mnt_3.Condicion='H'
                   AND ".$filtro."
			order by mnt_3.id";
            echo $NombreSubEspecialidad;
        /*estado anterior de la consulta
            $querySelect="select mss.IdSubServicio, msse.Condicion as HabilitadoFarmacia,
                        msse.CodigoFarmacia, concat_ws(' - ',NombreServicio,NombreSubServicio) as SubServicio
			from mnt_subservicio mss
			inner join mnt_subservicioxestablecimiento msse
			on mss.IdSubServicio=msse.IdSubServicio
			inner join mnt_servicioxestablecimiento mse
			on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                        inner join mnt_servicio ms
                        on ms.IdServicio = mse.IdServicio
			
			where msse.IdEstablecimiento=".$IdEstablecimiento."
                        and msse.IdModalidad=$IdModalidad
			and msse.Condicion='H'
			and ".$filtro."
			order by ms.IdServicio,NombreSubServicio";
         * 
         */
		$resp=pg_query($querySelect);
		return($resp);	}//BusquedaMedico
	
	
	function Tope($IdEstablecimiento,$IdModalidad){
		
            $querySelect=" SELECT count(mnt_3.id)

                   FROM mnt_aten_area_mod_estab mnt_3
                   INNER JOIN mnt_area_mod_estab mnt_2 on mnt_3.id_area_mod_estab = mnt_2.id
                   INNER JOIN ctl_atencion ctl on mnt_3.id_atencion = ctl.id
                   INNER JOIN ctl_area_atencion ctl_a on mnt_2.id_area_atencion = ctl_a.id
                   LEFT JOIN mnt_servicio_externo_establecimiento mnt_ser_estab on mnt_2.id_servicio_externo_estab = mnt_ser_estab.id
                   LEFT JOIN mnt_servicio_externo mnt_ser on mnt_ser_estab.id_servicio_externo = mnt_ser.id
                   INNER JOIN mnt_modalidad_establecimiento mnt_mod_estab on mnt_2.id_modalidad_estab = mnt_mod_estab.id
                   INNER JOIN ctl_modalidad ctl_mod on mnt_mod_estab.id_modalidad = ctl_mod.id
                   WHERE ctl_mod.id =$IdModalidad
                   AND mnt_3.id_establecimiento =$IdEstablecimiento
                   AND mnt_3.Condicion='H'";
            
            /*
            $querySelect="select count(msse.IdSubServicio)
					from mnt_subservicio mss
					inner join mnt_subservicioxestablecimiento msse
					on msse.IdSubServicio=mss.IdSubServicio
					where msse.Condicion='H'
                                        and IdEstablecimiento=$IdEstablecimiento
                                        and IdModalidad=$IdModalidad
					order by NombreSubServicio";
             * */
             
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	
	function CodigoActualFarmacia($IdSubEspecialidad,$IdEstablecimiento,$IdModalidad){
	$querySelect="SELECT mnt_3.codigo_farmacia

                   FROM mnt_aten_area_mod_estab mnt_3
                   INNER JOIN mnt_area_mod_estab mnt_2 on mnt_3.id_area_mod_estab = mnt_2.id
                   INNER JOIN ctl_atencion ctl on mnt_3.id_atencion = ctl.id
                   INNER JOIN ctl_area_atencion ctl_a on mnt_2.id_area_atencion = ctl_a.id
                   LEFT JOIN mnt_servicio_externo_establecimiento mnt_ser_estab on mnt_2.id_servicio_externo_estab = mnt_ser_estab.id
                   LEFT JOIN mnt_servicio_externo mnt_ser on mnt_ser_estab.id_servicio_externo = mnt_ser.id
                   INNER JOIN mnt_modalidad_establecimiento mnt_mod_estab on mnt_2.id_modalidad_estab = mnt_mod_estab.id
                   INNER JOIN ctl_modalidad ctl_mod on mnt_mod_estab.id_modalidad = ctl_mod.id
                   WHERE ctl_mod.id =$IdModalidad
                   AND mnt_3.id_establecimiento =$IdEstablecimiento
                   AND mnt_3.id=$IdSubEspecialidad";	
            /*estado anterior de la consulta
            $querySelect="select msse.CodigoFarmacia
					from mnt_subservicio
					inner join mnt_subservicioxestablecimiento msse
					on msse.IdSubServicio=mnt_subservicio.IdSubServicio
					where mnt_subservicio.IdSubServicio='$IdSubEspecialidad'
					and IdEstablecimiento=".$IdEstablecimiento."
                                        and IdModalidad=$IdModalidad";
             */
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	
	function SubEspecialidad($IdSubEspecialidad){
		$querySelect="select IdSubEspecialidad,NombreSubEspecialidad
					from mnt_subespecialidad
					where IdSubEspecialidad=".$IdSubEspecialidad;
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[1]);		
	}//SubEspecialidad
	
	function MedicoSubEspecialidad($IdEmpleado){
		$querySelect="select NombreSubEspecialidad
					from mnt_subespecialidad
					inner join mnt_empleados
					on mnt_empleados.IdSubEspecialidad=mnt_subespecialidad.IdSubEspecialidad
					where IdEmpleado='$IdEmpleado'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);		
	}//MedicoSubEspecialidad
	
	function ActualizarCodigoFarmacia($IdSubEspecialidad,$CodigoNuevo,$IdEstablecimiento,$IdModalidad){
		
            $queryUpdate="update mnt_aten_Area_mod_estab set Codigo_Farmacia='$CodigoNuevo' 
                            where Id='$IdSubEspecialidad' 
                            and Id_Establecimiento=".$IdEstablecimiento;
            /*
            $queryUpdate="update mnt_subservicioxestablecimiento set CodigoFarmacia='$CodigoNuevo' 
                            where IdSubServicio='$IdSubEspecialidad' 
                            and IdEstablecimiento=".$IdEstablecimiento." and IdModalidad=$IdModalidad";
             */
		pg_query($queryUpdate);
	}//Actualiza Codigo
	
	function VerificaCodigo($IdSubEspecialidad,$CodigoNuevo,$IdEstablecimiento,$IdModalidad){
		$querySelect="SELECT mnt_3.id
                   FROM mnt_aten_area_mod_estab mnt_3
                   INNER JOIN mnt_area_mod_estab mnt_2 on mnt_3.id_area_mod_estab = mnt_2.id
                   INNER JOIN ctl_atencion ctl on mnt_3.id_atencion = ctl.id
                   INNER JOIN ctl_area_atencion ctl_a on mnt_2.id_area_atencion = ctl_a.id
                   LEFT JOIN mnt_servicio_externo_establecimiento mnt_ser_estab on mnt_2.id_servicio_externo_estab = mnt_ser_estab.id
                   LEFT JOIN mnt_servicio_externo mnt_ser on mnt_ser_estab.id_servicio_externo = mnt_ser.id
                   INNER JOIN mnt_modalidad_establecimiento mnt_mod_estab on mnt_2.id_modalidad_estab = mnt_mod_estab.id
                   INNER JOIN ctl_modalidad ctl_mod on mnt_mod_estab.id_modalidad = ctl_mod.id
                   WHERE ctl_mod.id =$IdModalidad
                   AND mnt_3.id_establecimiento =$IdEstablecimiento
                   AND mnt_3.codigo_farmacia=$CodigoNuevo
                   AND mnt_3.id<>$IdSubEspecialidad";
                    

                /*estado consulta anterior
                   $querySelect=" select msse.IdSubServicio 
                              from mnt_subservicio mss
                              inner join mnt_subservicioxestablecimiento msse
                              on mss.IdSubServicio = msse.IdSubServicio
                              where msse.CodigoFarmacia='$CodigoNuevo' 
                              and msse.IdSubServicio <> '$IdSubEspecialidad' 
                              and IdEstablecimiento=".$IdEstablecimiento."
                              and IdModalidad=$IdModalidad";
                 */
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//varificacion de Codigo
	

	
	function VerificaEstadoMedico($IdSubEspecialidad){
		$querySelect="select condicion
					from mnt_aten_area_mod_estab
					where Id='$IdSubEspecialidad'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	
	function ActualizaEstadoCuenta($IdSubEspecialidad,$NuevoEstado){
		$queryUpdate="update mnt_aten_Aera_mod_estab set condicion='$NuevoEstado' where Id='$IdSubEspecialidad'";
		pg_query($queryUpdate);
		
	}
	
}//Clase Actualizaciones

?>
