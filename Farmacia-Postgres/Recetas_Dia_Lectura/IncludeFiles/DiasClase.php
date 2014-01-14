<?php
class Repetitivas{
function ObtenerDatosPacienteRecetaProceso($IdReceta){
$querySelect="select distinct mnt_expediente.IdNumeroExp,concat_ws(', ',CONCAT_WS(' ',mnt_datospaciente.PrimerApellido,mnt_datospaciente.SegundoApellido),CONCAT_WS(' ',mnt_datospaciente.PrimerNombre,mnt_datospaciente.SegundoNombre))as NOMBRE,
mnt_datospaciente.sexo,mnt_empleados.NombreEmpleado,mnt_subservicio.NombreSubServicio,
farm_recetas.IdReceta,NumeroReceta,farm_recetas.IdEstado,farm_recetas.Fecha,farm_recetas.IdHistorialClinico,(year(curdate())-year(mnt_datospaciente.FechaNacimiento))-(right(curdate(),5)<right(mnt_datospaciente.FechaNacimiento,5)) as nac,date_format(sec_historial_clinico.FechaHoraReg,'%d-%m-%Y %h:%i:%s %p') as FechaConsulta,DATE_FORMAT(farm_recetas.Fecha,'%d-%m-%Y') as FechaDeEntrega
from sec_historial_clinico
inner join mnt_expediente
on mnt_expediente.IdNumeroExp=sec_historial_clinico.IdNumeroExp
inner join farm_recetas
on farm_recetas.IdHistorialClinico=sec_historial_clinico.IdHistorialClinico
inner join mnt_datospaciente
on mnt_datospaciente.IdPaciente=mnt_expediente.IdPaciente
inner join mnt_empleados
on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
inner join mnt_subservicio
on mnt_subservicio.IdSubServicio=sec_historial_clinico.IdSubServicio
where farm_recetas.IdReceta='$IdReceta'";
$resp=pg_query($querySelect);
return($resp);
}

function ObtenerDatosPacienteReceta($bandera,$IdHistorialClinico,$IdArea,$NumExpediente){
$FechaAtras=queries::Atras();//Obtiene 3 fechas atras del dia de accion (vida util de recetas en dado caso no son entregadas)

if($bandera==1){
/*MOSTRAR TODAS LAS RECETAS*/
$querySelect="select distinct mnt_expediente.IdNumeroExp,concat_ws(', ',CONCAT_WS(' ',mnt_datospaciente.PrimerApellido,mnt_datospaciente.SegundoApellido),CONCAT_WS(' ',mnt_datospaciente.PrimerNombre,mnt_datospaciente.SegundoNombre))as NOMBRE,
mnt_datospaciente.sexo,mnt_empleados.NombreEmpleado,mnt_subservicio.NombreSubServicio,
farm_recetas.IdReceta,NumeroReceta,farm_recetas.IdEstado,farm_recetas.Fecha,farm_recetas.IdHistorialClinico,(year(curdate())-year(mnt_datospaciente.FechaNacimiento))-(right(curdate(),5)<right(mnt_datospaciente.FechaNacimiento,5)) as nac,sec_historial_clinico.FechaHoraReg as FechaConsulta,DATE_FORMAT(farm_recetas.Fecha,'%d-%m-%Y') as FechaDeEntrega
from sec_historial_clinico
inner join mnt_expediente on mnt_expediente.IdNumeroExp=sec_historial_clinico.IdNumeroExp
inner join farm_recetas on farm_recetas.IdHistorialClinico=sec_historial_clinico.IdHistorialClinico
inner join mnt_datospaciente on mnt_datospaciente.IdPaciente=mnt_expediente.IdPaciente
inner join mnt_empleados on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
inner join mnt_usuarios on mnt_usuarios.IdEmpleado=mnt_empleados.IdEmpleado
inner join mnt_subservicio on mnt_subservicio.IdSubServicio=mnt_usuarios.IdSubServicio
where (farm_recetas.IdEstado='R' or farm_recetas.IdEstado='P') 
and farm_recetas.Fecha between '$FechaAtras' and adddate(curdate(), interval +1 day)
and farm_recetas.IdArea='$IdArea'
AND mnt_ex.IdNumeroExp = '$NumExpediente'
order by farm_recetas.Fecha desc, farm_recetas.NumeroReceta asc";

}else{
/*IMPRESION DE VINETAS*/
$querySelect="select distinct mnt_expediente.IdNumeroExp,concat_ws(', ',CONCAT_WS(' ',mnt_datospaciente.PrimerApellido,mnt_datospaciente.SegundoApellido),CONCAT_WS(' ',mnt_datospaciente.PrimerNombre,mnt_datospaciente.SegundoNombre))as NOMBRE,
mnt_datospaciente.sexo,mnt_empleados.NombreEmpleado,mnt_subservicio.NombreSubServicio,
farm_recetas.IdReceta,NumeroReceta,farm_recetas.IdEstado,farm_recetas.Fecha,farm_recetas.IdHistorialClinico,(year(curdate())-year(mnt_datospaciente.FechaNacimiento))-(right(curdate(),5)<right(mnt_datospaciente.FechaNacimiento,5)) as nac,sec_historial_clinico.FechaHoraReg as FechaConsulta,DATE_FORMAT(farm_recetas.Fecha,'%d-%m-%Y') as FechaDeEntrega
from sec_historial_clinico
inner join mnt_expediente
on mnt_expediente.IdNumeroExp=sec_historial_clinico.IdNumeroExp
inner join farm_recetas
on farm_recetas.IdHistorialClinico=sec_historial_clinico.IdHistorialClinico
inner join mnt_datospaciente
on mnt_datospaciente.IdPaciente=mnt_expediente.IdPaciente
inner join mnt_empleados
on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
inner join mnt_usuarios
on mnt_usuarios.IdEmpleado=mnt_empleados.IdEmpleado
inner join mnt_subservicio
on mnt_subservicio.IdSubServicio=mnt_usuarios.IdSubServicio
where (farm_recetas.IdEstado='R' or farm_recetas.IdEstado='P') 
and farm_recetas.Fecha between '$FechaAtras' and adddate(curdate(),interval -1 day)
and sec_historial_clinico.IdHistorialClinico='$IdHistorialClinico'
and farm_recetas.IdArea='$IdArea'
order by farm_recetas.Fecha desc, farm_recetas.NumeroReceta asc";
}
//Para consulta del mes en where month(FechaConsulta)=month(curdate())...Para despues
$resp=pg_query($querySelect);
return($resp);
}//ObtenerDatosPacienteReceta

function datosReceta($IdReceta,$IdArea){
$querySelect="select farm_recetas.IdReceta,NumeroReceta,farm_recetas.IdEstado,farm_recetas.Fecha,farm_recetas.IdHistorialClinico, farm_catalogoproductos.Nombre as medicina,
farm_catalogoproductos.Concentracion,farm_catalogoproductos.Presentacion,farm_catalogoproductos.IdMedicina, farm_catalogoproductos.FormaFarmaceutica, farm_medicinarecetada.Cantidad,farm_medicinarecetada.Dosis,farm_medicinarecetada.IdEstado,
farm_medicinarecetada.IdEstado as EstadoMedicina
from  farm_recetas
inner join sec_historial_clinico
on farm_recetas.IdHistorialClinico=sec_historial_clinico.IdHistorialClinico
inner join farm_medicinarecetada
on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
inner join farm_catalogoproductos
on farm_medicinarecetada.IdMedicina=farm_catalogoproductos.IdMedicina
where farm_recetas.IdReceta='$IdReceta'";
$respuesta=pg_query($querySelect);
return($respuesta);
}//fin de datosReceta


function MedicinaReceta($IdReceta){
	$querySelect="select farm_medicinarecetada.IdMedicina,farm_medicinarecetada.IdReceta,farm_medicinarecetada.IdEstado,IdHistorialClinico
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				where farm_recetas.IdReceta='$IdReceta'";
	$resp=pg_query($querySelect);
	return($resp);
	
	
}//MedicinaReceta






}//Fin clase Repetitivas
?>