<?php
include('../../Clases/class.php');
class Proceso{
	function CargarCierres($IdEstablecimiento,$IdModalidad){
		$query="select *
			from farm_cierre
			where left(MesCierre,4)::int not in (select AnoCierre 
							from farm_cierre 
							where (AnoCierre is not null)
                                                        and IdModalidad=$IdModalidad
							)
                        and farm_cierre.IdEstablecimiento=$IdEstablecimiento
                        and farm_cierre.IdModalidad=$IdModalidad
			order by MesCierre
			";
		$resp=pg_query($query);
		return($resp);
	}



	function EliminarCierre($IdCierre,$IdEstablecimiento,$IdModalidad){
		$query="delete from farm_cierre where Id=".$IdCierre." 
                                                and IdEstablecimiento=".$IdEstablecimiento." 
                                                and IdModalidad=$IdModalidad";
		if(pg_query($query)){
			return true;
		}else{
			return false;
		}
		
	}//obtener medicina general


	


	
}//clase	


?>