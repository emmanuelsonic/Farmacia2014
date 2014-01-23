<?php
include('../../Clases/class.php');
class Actualiza{
function ObtenerMedicinaInformacion($IdMedicina,$Lote){
	$querySelect="select farm_lotes.id as IdLote,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,
				farm_lotes.PrecioLote,monthname(farm_lotes.FechaVencimiento) as mes,
				year(farm_lotes.FechaVencimiento) as ano,farm_medicinaexistenciaxarea.Existencia,
				farm_unidadmedidas.Descripcion,farm_unidadmedidas.UnidadesContenidas as Divisor
				from farm_catalogoproductos
				inner join farm_medicinaexistenciaxarea
				on farm_medicinaexistenciaxarea.IdMedicina=farm_catalogoproductos.Id
				inner join farm_lotes
				on farm_lotes.Id=farm_medicinaexistenciaxarea.IdLote
				inner join farm_unidadmedidas
				on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida
				where farm_catalogoproductos.Id='$IdMedicina'
				and farm_lotes.Lote='$Lote'
                                ";
	$resp=pg_fetch_array(pg_query($querySelect));
	return($resp);
}//ObtenerMedicinaInformacion


function EliminarExistenciaxArea($IdMedicina,$IdExistenciaArea,$IdLote,$IdArea,$IdEstablecimiento){
   $SQL="select id as IdExistencia,Existencia
	from farm_medicinaexistenciaxarea
	where Id=".$IdExistenciaArea."
        and IdEstablecimiento=".$IdEstablecimiento;
   $resp=pg_fetch_array(pg_query($SQL));
   $IdExistenciaArea=$resp["idexistencia"];
   $ExistenciaArea=$resp["existencia"];

     $SQL2="select *
	from farm_entregamedicamento
	where IdMedicina=".$IdMedicina."
	and IdLote=".$IdLote."
        and IdEstablecimiento=".$IdEstablecimiento;

    $resp2=pg_fetch_array(pg_query($SQL2));
	$ExistenciaBodega=$resp2["existencia"];
	$IdEntrega=$resp2["id"];

	$ExistenciaBodegaNueva=$ExistenciaBodega+$ExistenciaArea;
	
    $SQL3="update farm_entregamedicamento set Existencia='$ExistenciaBodegaNueva' where Id='$IdEntrega'";
	pg_query($SQL3);

    $SQL4="delete from farm_medicinaexistenciaxarea where Id=".$IdExistenciaArea;
	pg_query($SQL4);
    $SQL5="update farm_bitacoramedicinaexistenciaxarea set IdExistenciaOrigen=NULL where IdExistenciaOrigen=".$IdExistenciaArea;
	pg_query($SQL5);
}


	function ValorDivisor($IdMedicina,$IdModalidad){
	   $SQL="select DivisorMedicina from farm_divisores where IdMedicina=".$IdMedicina." and IdModalidad=$IdModalidad";
	   $resp=pg_query($SQL);
           
	   return($resp);
    	}

}//clase Actualiza


?>