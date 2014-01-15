<?php session_start();
require('../Clases/class.php');
$query=new queries;

	function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
	   $SQL="select DivisorMedicina from farm_divisores 
                 where IdMedicina=".$IdMedicina." 
                 and IdEstablecimiento=".$IdEstablecimiento." 
                 and IdModalidad=$IdModalidad";
	   $resp=pg_query($SQL);
	   return($resp);
    	}

?>
<html>
<head>
<title>saving...</title>
</head>
<body>
<?php
if(isset($_REQUEST["area"])){$area=$_REQUEST["area"];}
if(isset($_REQUEST["farmacia"])){$farmacia=$_REQUEST["farmacia"];}
if(isset($_GET["Bandera"])){$bandera=$_GET["Bandera"];}else{$bandera=1;}
if(isset($_GET["IdMedicina"])){$IdMedicina=$_GET["IdMedicina"];}else{$IdMedicina=0;}

$IdModalidad=$_SESSION["IdModalidad"];

if($bandera==2){//comprobacion de fechas
	$TestFecha=$_GET["TestFecha"];
	$querySelect="select substr(to_char($TestFecha,'YYYY-MM-DD'),1,7)< substr(to_char(current_date,'YYYY-MM-DD'),1,7)";
	$resp=pg_fetch_row(pg_query($querySelect));
	if($resp!=1){
		echo "SI";
	}else{
		echo "NO";
	}
}

if($bandera!=0 and $bandera!=3){

	if($IdMedicina==0){
		conexion::conectar();
		/* INTRODUCCION DE EXISTENCIAS POR POST DE FORMULARIO */
		echo "<center><h2>GUARDANDO EXISTENCIAS.... <BR></h2><img src='../images/barra.gif'><center>";



		$IdTerapeutico=$_REQUEST["Terapeutico"];

		$querySelect="select distinct farm_catalogoproductos.Id,farm_catalogoproductos.Nombre,farm_catalogoproductos.FormaFarmaceutica,
					farm_catalogoproductos.Concentracion, mnt_areamedicina.IdArea
					from farm_catalogoproductos
					inner join mnt_areamedicina
					on mnt_areamedicina.IdMedicina=farm_catalogoproductos.Id
					where mnt_areamedicina.IdArea='$area'
					and farm_catalogoproductos.IdEstado='H'
                                        and mnt_areamedicina.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]."
                                        and mnt_areamedicina.IdModalidad=$IdModalidad
					and IdTerapeutico=".$IdTerapeutico;
		$resp=pg_query($querySelect);
		
		while($Datos=pg_fetch_array($resp,null,PGSQL_ASSOC)){
			$IdMedicina=$Datos["Id"];
			$Lote_="Lote".$IdMedicina;
			/**/
			$mes="mes".$IdMedicina;
			$ano="ano".$IdMedicina;
				if(isset($_POST[$mes])){
					$mes==$_POST[$mes];
					$ano=$_POST[$ano];
				}else{
					$mes=0;$ano=0;
				}
			
			if($mes!=0 and $ano!=0){	
				$Vencimiento=$ano."-".$mes."-"."25";
			}else{
				$Vencimiento='Fecha Ventto.';
			}
			/**/
			$Precio_="Precio".$IdMedicina;
			/* Obtencion del post de los datos */
			$NuevaExistencia=$_POST[$IdMedicina];
			
			$Lote=$_POST[$Lote_];
			$Lote=strtoupper($Lote);
			
				if(isset($_POST[$Precio_])){$Precio=$_POST[$Precio_];}else{$Precio=0;}
			/* Fin de POST */
			if($NuevaExistencia!='0' and $Lote!='0'){
				$query->AumentaExistencias($area,$IdMedicina,$NuevaExistencia,$Vencimiento,$Lote,$Precio,$_SESSION["IdEstablecimiento"],$IdModalidad);
			}  
		}//while
	conexion::desconectar();


	//echo $IdTerapeutico."->".$area."->".$farmacia;
?>
		<script language="javascript">
			window.location='existencia.php?area=<?php echo $area;?>&farmacia=<?php echo $farmacia;?>&IdTerapeutico=<?php echo $IdTerapeutico;?>';
		</script>
<?php 
/*FIN DE DATOS ENVIAMOS POR POST*/
	}else{
		/* INGRESO DE EXISTENCIA UTILIZANDO EL AJAX */
		conexion::conectar();
		$vencimiento=$_GET["FechaVentto"]; //Informaci�n de Fecha.-
		$NuevaExistencia=$_GET["Existencia"];
		$Lote=$_GET["Lote"];
		$Lote=strtoupper($Lote);
		$Precio=$_GET["PrecioLote"];
			if($NuevaExistencia!='0'){
				$query->AumentaExistencias($area,$IdMedicina,$NuevaExistencia,$vencimiento,$Lote,$Precio,$_SESSION["IdEstablecimiento"],$IdModalidad);
			}
		conexion::desconectar();
	}//else 

}


if($bandera==0){
/* REFRESCAMIENTO DE LA EXISTENCIAS DESPUES DE UTILIZAR AJAX */
conexion::conectar();
$querySelect=" select distinct farm_catalogoproductos.id as IdMedicina,farm_catalogoproductos.Nombre,farm_catalogoproductos.FormaFarmaceutica,
			farm_catalogoproductos.Concentracion, mnt_areamedicina.IdArea,farm_medicinaexistenciaxarea.*,farm_lotes.*,
			farm_unidadmedidas.Descripcion,farm_unidadmedidas.UnidadesContenidas as Divisor
			from farm_catalogoproductos
			inner join mnt_areamedicina
			on mnt_areamedicina.IdMedicina=farm_catalogoproductos.Id
			inner join farm_medicinaexistenciaxarea
			on farm_medicinaexistenciaxarea.IdMedicina=farm_catalogoproductos.Id
			inner join farm_unidadmedidas
			on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida
			inner join farm_lotes
			on farm_lotes.Id=farm_medicinaexistenciaxarea.IdLote
			where mnt_areamedicina.IdArea='$area' 
			and farm_catalogoproductos.Id='$IdMedicina' 
			and farm_medicinaexistenciaxarea.Existencia <> 0
			and left(to_char(FechaVencimiento,'YYYY-MM-DD'),7) > left(to_char(current_date,'YYYY-MM-DD'),7)
			and farm_medicinaexistenciaxarea.IdArea='$area'
                        and farm_medicinaexistenciaxarea.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]."
                        and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
			order by farm_lotes.FechaVencimiento";
$resp=pg_query($querySelect);
$Info='';
while($Datos=pg_fetch_array($resp)){
$Existencia=$Datos["existencia"];
	if($Existencia!= '' and $Existencia!='0' and $Existencia!=NULL){
	
	$Date=explode('-',$Datos["fechavencimiento"]);
	$Fecha=$Date[2]."-".$Date[1]."-".$Date[0];
	$Unidades=$Datos['divisor'];
//$Script='javascript:popUp("ActualizaLotes.php?Lote='.$Datos["Lote"].'&IdMedicina='.$Datos["IdMedicina"].'&IdArea='.$area.'")';
	$Script='';
	
	

$EliminarExistencia="<u><a style='cursor:hand;' onclick='EliminarMedicamentoExistencia(".$Datos["idmedicina"].",".$Datos["id"].",".$Datos["idlote"].",".$area.")'>X</a></u>";


if($respDivisor=pg_fetch_array(ValorDivisor($Datos["idmedicina"],$_SESSION["IdEstablecimiento"],$IdModalidad))){
		$Divisor=$respDivisor[0];

		if($Datos["existencia"] < 1){
			//Si la cantidad a mostrar es menor que 1 es decir menor a un frasco
		   $TransformaEntero=number_format($Datos["existencia"]*$Divisor,0,'.',',');
			$CantidadTransformada=$TransformaEntero.'/'.$Divisor;
		}else{
			//Si la cantidad es mayor a un frasco se realiza este cambio a quebrados
			$CantidadReal=number_format($Datos["existencia"],2,'.',',');	
		$CantidadBase=explode('.',$CantidadReal);
		
		    $Entero=$CantidadBase[0];//Faccion ENTERA

			$Decimal=$CantidadBase[1];
		    if($Decimal==0){$Decimal="";$Quebrado="";}else{
			
			$Quebrado=number_format(($Decimal/100)*$Divisor,0,'.',',');
			$Quebrado='['.$Quebrado.'/'.$Divisor.']';
		    }

			
		$CantidadTransformada=$Entero.' '.$Quebrado;
		}
	   $CantidadIntro=$CantidadTransformada;
		
	}else{
	   $CantidadIntro=$Datos["existencia"]/$Unidades;
	}
	$existencia=$CantidadIntro;

	$Info.= $EliminarExistencia." Existencia: ".$existencia."<br>".
	"Lote: <a onclick='".$Script."'>".$Datos["lote"]."</a><br>".
	"Vencimiento: ".$Fecha."<br><br>";
	}

}//While

$Info.="~";
$Info.= queries::LotesExistencias($IdMedicina,$_SESSION["IdEstablecimiento"],$IdModalidad);
conexion::desconectar();
echo $Info;
}

if($bandera==3){
conexion::conectar();
	$IdLote=$_GET["IdLote"];
	$IdMedicina=$_GET["IdMedicina"];
	$querySelect="select farm_entregamedicamento.IdMedicina,Existencia,UnidadesContenidas, Descripcion
				from farm_entregamedicamento
				inner join farm_lotes
				on farm_lotes.Id=farm_entregamedicamento.IdLote
				inner join farm_catalogoproductos cat
				on cat.Id=farm_entregamedicamento.IdMedicina
				inner join farm_unidadmedidas um
				on um.Id=cat.IdUnidadMedida
				where farm_lotes.Id=".$IdLote."
                                and farm_entregamedicamento.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]." 
                                and farm_entregamedicamento.IdModalidad=$IdModalidad";
	$Datos=pg_fetch_array(pg_query($querySelect));
	if($Datos["existencia"]!='' and $Datos["existencia"]!=NULL){
		$Divisor=$Datos["unidadescontenidas"];
		$Descripcion=$Datos["descripcion"];

	if($respDivisor=pg_fetch_array(ValorDivisor($Datos["idmedicina"],$_SESSION["IdEstablecimiento"],$IdModalidad))){
		$Divisor=$respDivisor[0];

		if($Datos["existencia"] < 1){
			//Si la cantidad a mostrar es menor que 1 es decir menor a un frasco
		   $TransformaEntero=number_format($Datos["existencia"]*$Divisor,0,'.',',');
			$CantidadTransformada=$TransformaEntero.'/'.$Divisor;
		}else{
			//Si la cantidad es mayor a un frasco se realiza este cambio a quebrados
			$CantidadReal=number_format($Datos["existencia"],2,'.',',');	
		$CantidadBase=explode('.',$CantidadReal);
		
		    $Entero=$CantidadBase[0];//Faccion ENTERA

			$Decimal=$CantidadBase[1];
		    if($Decimal==0){$Decimal="";$Quebrado="";}else{
			
			$Quebrado=number_format(($Decimal/100)*$Divisor,0,'.',',');
			$Quebrado='['.$Quebrado.'/'.$Divisor.']';
		    }

			
		$CantidadTransformada=$Entero.' '.$Quebrado;
		}
	   $CantidadIntro=$CantidadTransformada;
		
	}else{
	   $CantidadIntro=$Datos["existencia"]/$Divisor;
	}
	$existencia=$CantidadIntro;
	
		echo "EXISTENCIA DEL LOTE SELECCIONADO: ".$CantidadIntro ." ".$Descripcion;
	}else{
		echo "";
	}

conexion::desconectar();
}


?>

</body>
</html>
