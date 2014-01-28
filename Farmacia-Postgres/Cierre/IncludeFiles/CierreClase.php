<?php
include('../../Clases/class.php');
class Proceso{
	function Verificar($Ano,$IdEstablecimiento,$IdModalidad){
		$query="select firstname,to_char(FechaHoraReg,'dd-mm-YYYY') as Fecha
				from fos_user_user fuu
				inner join farm_cierre
				on farm_cierre.IdUsuarioReg=fuu.Id
				
				where AnoCierre='$Ano'
                                and farm_cierre.IdEstablecimiento=".$IdEstablecimiento." 
                                and farm_cierre.IdModalidad=$IdModalidad";
		$resp=pg_query($query);
		return($resp);
	}

	function Cierre($Ano,$IdPersonal,$IdEstablecimiento,$IdModalidad){
		$query="insert into farm_cierre (AnoCierre,IdUsuarioReg,FechaHoraReg,IdEstablecimiento,IdModalidad) 
                                          values('$Ano','$IdPersonal',now(),$IdEstablecimiento,$IdModalidad)";
		pg_query($query);
		
	}//Cierre


	function VerificarPeriodo($Periodo,$IdEstablecimiento,$IdModalidad){
		$query="select firstname,to_char(FechaHoraReg,'dd-%mm-%YYYY') as Fecha
				from fos_user_user fuu
				inner join farm_cierre
				on farm_cierre.IdUsuarioReg=fuu.Id
				
				where MesCierre='$Periodo'
                                and farm_cierre.IdEstablecimiento=".$IdEstablecimiento."
                                and farm_cierre.IdModalidad=$IdModalidad";
		$resp=pg_query($query);
		return($resp);
	}

	function CierreMes($Periodo,$IdPersonal,$IdEstablecimiento,$IdModalidad){
		$query="insert into farm_cierre (MesCierre,IdUsuarioReg,FechaHoraReg,IdEstablecimiento,IdModalidad) 
                                          values('$Periodo','$IdPersonal',now(),$IdEstablecimiento,$IdModalidad)";
		pg_query($query);
		
	}//Cierre


	
}//clase	


?>