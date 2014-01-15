<?php
include('../Clases/class2.php');
class Actualiza{
function ObtenerMedicinaInformacion($IdMedicina,$Lote){
	$querySelect="select farm_lotes.id as IdLote,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,
				farm_lotes.PrecioLote,monthname(farm_lotes.FechaVencimiento) as mes,
				year(farm_lotes.FechaVencimiento) as ano, Existencia,
				farm_unidadmedidas.Descripcion,farm_unidadmedidas.UnidadesContenidas as Divisor
				from farm_catalogoproductos
				inner join farm_entregamedicamento
				on farm_entregamedicamento.IdMedicina=farm_catalogoproductos.Id
				inner join farm_lotes
				on farm_lotes.Id=farm_entregamedicamento.IdLote
				inner join farm_unidadmedidas
				on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida
				where farm_catalogoproductos.Id='$IdMedicina'
				and left(to_char(FechaVencimiento,'YYYY-MM-DD'),7) >= left(TO_CHAR(current_date,'YYYY-MM-DD'),7)
				and farm_lotes.Lote='$Lote'";
	$resp=pg_fetch_array(pg_query($querySelect));
	return($resp);
}//ObtenerMedicinaInformacion






}//clase Actualiza


?>