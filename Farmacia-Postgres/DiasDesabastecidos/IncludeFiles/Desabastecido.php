<?php  session_start();
if(!isset($_SESSION["Nivel"])){
 echo "ERROR_SESSION";
}else{

include('ClaseDesabastecido.php');
conexion::conectar();
$proc=new Desabastecimiento;
$IdModalidad=$_SESSION["IdModalidad"];
switch($_GET["Bandera"]){

   case 1:
	$IdMedicina=$_GET["IdMedicina"];
	$FechaInicio=$_GET["FechaInicio"];
	$FechaFin=$_GET["FechaFin"];
	
	
	   if($FechaFin==''){ $FechaFin=date('Y-m-d');}

	//Comprobacion de periodo valido
	$respValida=$proc->ValidacionPeriodo($IdMedicina,$FechaInicio,$FechaFin,$_SESSION["IdEstablecimiento"],$IdModalidad);
	if($existe=pg_fetch_array($respValida)){
	   echo "NO";
	}else{
	
	   //*************************
	   $proc= new Desabastecimiento;
	   $promedio=$proc->InsatisfechasPromedio($IdMedicina,$FechaInicio,$FechaFin,$_SESSION["IdEstablecimiento"],$IdModalidad);
	   if($row=pg_fetch_array($promedio)){
	      echo "Recetas Insatisfechas: ".$row["prominsatisfechas"]." <br> Promedio de Recetas Diarias: ".$row["promediodiarecetas"];

	      $proc->IngresarDatosInsatisfecha($IdMedicina,$FechaInicio,$FechaFin,$row["prominsatisfechas"],$row["promediodiarecetas"],$_SESSION["IdEstablecimiento"],$IdModalidad);
	   }else{
	      echo "Error ...";
	   }

	}

   break;

}


conexion::desconectar();
}
?>