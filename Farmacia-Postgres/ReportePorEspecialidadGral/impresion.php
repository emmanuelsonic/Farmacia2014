<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<script language="javascript">
window.location='../../signIn.php';
</script>
<?php
}else{
if($_SESSION["nivel"]!=1 and $_SESSION["nivel"]!=2){?>
<script language="javascript">
window.location='../../index.php?Permiso=1';
</script>
<?php
}else{
$tipoUsuario=$_SESSION["tipo_usuario"];
$nombre=$_SESSION["nombre"];
$nivel=$_SESSION["nivel"];
$nick=$_SESSION["nick"];
require('../../Clases/class.php');
include('Funciones.php');
$query=new queries;
conexion::conectar();
?>
<html>
<head>
<style type="text/css">
#Layer11 {
	position:absolute;
	left:0px;
	top:0px;
	width:1015px;
	height:232px;
	z-index:1;
}
#Layer61 {position:absolute;
	left:21px;
	top:12px;
	width:847px;
	height:34px;
	z-index:5;
}
#Layer21 {
	position:absolute;
	left:901px;
	top:128px;
	width:52px;
	height:20px;
	z-index:6;
}
#Layer31 {
	position:absolute;
	left:855px;
	top:10px;
	width:63px;
	height:24px;
	z-index:7;
}
@media print {
* { background: #fff; color: #000; }
html { font: 9pt Arial, Helvetica, sans-serif; }
#Layer61, #Layer21, #Layer31 { display: none; }
#nav, #nav2, #about { display: none; }
#footer { display:none;}
#span{ color:#FFFFFF}
@page { size: 8.5in 11in; margin: 0.5cm }
/*P{page-break-after:inherit}*/
}
</style>
<script src="../PrintReport.js" language="javascript"></script>
</head>
<body>
<script language="javascript" src="../../tooltip/wz_tooltip.js"></script>
<?php 
$FechaInicio=explode('-',$_REQUEST["fechaInicio"]);
$FechaFin=explode('-',$_REQUEST["fechaFin"]);
$FechaInicio2=$FechaInicio[2].'-'.$FechaInicio[1].'-'.$FechaInicio[0];
$FechaFin2=$FechaFin[2].'-'.$FechaFin[1].'-'.$FechaFin[0];
$FechaInicio=$_REQUEST["fechaInicio"];
$FechaFin=$_REQUEST["fechaFin"];

/**********************INFORMACION DE REPORTE**********************************/
if(isset($_GET["IdSubEspecialidad"])){$IdSubEspecialidad=$_GET["IdSubEspecialidad"];}else{$IdSubEspecialidad=0;}
if(isset($_GET["select1"])){$IdGrupoTerapeutico=$_GET["select1"];}else{$IdGrupoTerapeutico=0;}
if(isset($_GET["select2"])){$IdMedicina=$_GET["select2"];}else{$IdMedicina=0;}
/******************************************************************************/

?>
<div id="Layer31" align="right">
	<input type="button" id="imprimir" name="imprimir" value="IMPRIMIR" onClick="ImprimirReporte(this.form);" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099">
</p>
	<input type="button" id="cerrar" name="cerrar" value="CERRAR" onClick="javascript:self.close()" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099">	
</div>

<div id="Layer11">

  <table width="967" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="11" align="center">HOSPITAL NACIONAL ROSALES<br>
        <strong>CONSUMO DE MEDICAMENTOS POR SERVICIOS </strong> <br>
        PERIODO DEL: <?php echo"$FechaInicio2";?> AL <?php echo"$FechaFin2";?> .- <br>
        <div align="left">Fecha de Emisi&oacute;n:
          <?php 
$DateNow=date("d/m/Y");
echo"$DateNow";?>
        </div></td>
    </tr>
  </table>
 
 <table width="968" border="1">
<?php
//*************************************
//******************************* QUERIES Y RECORRIDOS
$respServicios=Servicios($IdSubEspecialidad,$FechaInicio,$FechaFin);
while($rowServicios=pg_fetch_array($respServicios)){
	$IdSubEspecialidad=$rowServicios[0];
	$NombreSubEspecialidad=$rowServicios[1];?>
			
			<tr class="MYTABLE">
			  <td colspan="11" align="center" style="background:#666666;"><strong><h3><?php echo $NombreSubEspecialidad;?></h3></strong></td>
			</tr>	

	<?php 
	$nombreTera=NombreTera($IdGrupoTerapeutico,$IdSubEspecialidad,$FechaInicio,$FechaFin);
	while($grupos=pg_fetch_array($nombreTera)){
		$NombreTerapeutico=$grupos["GrupoTerapeutico"];
		$IdTerapeutico=$grupos["IdTerapeutico"];
		?>

			<tr class="MYTABLE">
			  <td colspan="11" align="center" style="background:#CCCCCC;"><strong><h4><?php echo"$NombreTerapeutico";?></h4></strong></td>
			</tr>
				<tr class="FONDO2">
			<th width="37" scope="col">Codigo</th>
			  <th width="182" scope="col">Medicamento</th>
			  <th width="61" scope="col">Concen.</th>
			  <th width="54" scope="col">Prese.</th>
			  <th width="54" scope="col">Recetas</th>
			  <th width="50" scope="col">Satis.</th>
			  <th width="70" scope="col">No Satis.</th>
			  <th width="63">Unidad de Medida</th>
			  <th width="78" scope="col">Precio</th>
			  <th width="135" scope="col">Consumo</th>
			  <th width="136" scope="col">Monto</th>
			</tr>
			<?php
		$resp1=QueryExterna($IdTerapeutico,$IdMedicina,$IdSubEspecialidad,$FechaInicio,$FechaFin);
			while($row=pg_fetch_array($resp1)){
		$GrupoTerapeutico=$IdTerapeutico;
		$Medicina=$row["IdMedicina"];
		$codigoMedicina=$row["Codigo"];
		$NombreMedicina=$row["Nombre"];
		$concentracion=$row["Concentracion"];
		$presentacion=$row["FormaFarmaceutica"];
		
		$Nrecetas=0;//conteo de recetas
		$consumo=0;
		
		
		$respuesta=ObtenerReporteGrupoTerapeutico($IdTerapeutico,$Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad);
			
				if($row2=pg_fetch_array($respuesta)){ /* verificacion de datos */
		$precioActual=0;
		$respuesta2=ObtenerReporteGrupoTerapeutico($IdTerapeutico,$Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad); 
		$Nrecetas=pg_num_rows($respuesta2); 
		//		while($row3=pg_fetch_array($respuesta2)){
		//IdReceta
		$row3=pg_fetch_array($respuesta2);
		$IdReceta=$row3["IdReceta"];
		$Divisor=$row3["Divisor"];//Divisor de conversion
		$UnidadMedida=$row3["Descripcion"];//Tipo de unidad de Medida
		$satisfechas=0;
		$insatisfechas=0;
		
	/*
		$respLotes=ObtenerConsumosMedicamentoLote($Medicina,$IdSubEspecialidad,$FechaInicio,$FechaFin);
		
		
		$Cantidad_1=0;$Cantidad_2=0;$Monto_Total=0;$Monto_Total2=0;$Lote=array();$Lote2_=array();$valor=0;$Cantidad2_=array();$CantidadUnidadMedida=array();$CantidadUnidadMedida2=array();$Cantidad=array();
		
		$i=0;//Posicion inicial de los vectores
		$j=0;
					while($rowLotes=pg_fetch_array($respLotes)){//OBTENGO LOTES RECETAS ETC
						$Cantidad1=$rowLotes["TotalLote1"];
						$Lote1=$rowLotes["Lote1"];
						
						$Cantidad2=$rowLotes["TotalLote2"];
						$Lote2=$rowLotes["Lote2"];
											if($Lote1!=NULL and $Cantidad1!=NULL){
												$Lote[$i]=$Lote1;
												switch($i){
													case 0://Posicion inicial del vector
															$Cantidad[$i]=$Cantidad1;
													break;								
													default://posicion mayor que cero para verificacion de lotes iguales
													$ancla=count($Lote2_);
													if($ancla!=0){
														for($x=0;$x<$ancla;$x++){
															if($Lote2_[$x]==$Lote[$i]){
																$valor=$x+1;
																break;
															}
														}//for
													}
														$TamanoLote=count($Lote);
														if($valor!=0){	
														$valor=$valor-1;
														$Cantidad_nueva=$Cantidad1+$Cantidad2_[$valor];
															$Cantidad[$i]=$Cantidad_nueva;
															$Cantidad2_[$valor]=0;
															$Lote2_[$valor]=0;
															$valor=0;
														}elseif($Lote[$i-1]!=$Lote[$i]){ 
															
																$Cantidad[$i]=$Cantidad1; 
															 
														}else{
															$Cantidad_nueva=$Cantidad1+$Cantidad[$i-1];
															$Cantidad[$i-1]=$Cantidad_nueva;
															$Cantidad[$i]='';
															$Lote[$i]='';
															$i-=1;//Decremento de $i para reutilizar la posicion que esta en blanco
														}
													break;
												}//switch
												$Cantidad_1=$Cantidad_1+$Cantidad1;//Suma de cantidades de medicamento entregados satisfechos
											}//Fin IF Lote1 != NULL
											else{
											 $i=-1;   
											}
									///**********************************************************/////		
									/*		if($Cantidad2!=NULL and $Lote2!=NULL and $Cantidad2!=0 and $Lote2!=0){
												$Lote2_[$j]=$Lote2;
												$Cantidad2_[$j]=0;
												switch($j){
													case 0://Posicion inicial del vector
															$Cantidad2_[$j]=$Cantidad2;
													break;								
													default://posicion mayor que cero para verificacion de lotes iguales
													$ancla=count($Lote2_);
													for($x=0;$x<$ancla;$x++){
														if($Lote2_[$x]==$Lote2_[$j]){
															$valor=$x;
															
														}else{
															$valor2='NO';
														}
													}//for
													
														if($valor2=='NO'){			
															$Cantidad2_[$j]=$Cantidad2;
														}else{
															$Cantidad2_nueva=$Cantidad2+$Cantidad2_[$valor];
															$Cantidad2_[$valor]=$Cantidad2_nueva;
															$Cantidad2_[$j]='';
															$Lote2_[$j]='';
															$j-=1;//Decremento de $i para reutilizar la posicion que esta en blanco
														}
													
													break;
												}//switch
												$Cantidad_2+=$Cantidad2;
											$j++;
											}//Swith LOTES 2
								$i++;//aumento de la posicion del vector
								
					}//while lotes
		$tope=count($Cantidad);//Obtencion de posiciones totales del vector
		for($i=0;$i<$tope;$i++){
		$LoteActual=$Lote[$i];//Se obtiene el Lote
		$PrecioLote[$i]=queries::ObtenerPrecioLote($LoteActual);//Obtencion del precio con el que entra ese lote
		}
		
		$tope2=count($Cantidad2_);//Obtencion de posiciones totales del vector
		if($tope2!=0 || $tope2!=NULL){
			for($j=0;$j<$tope2;$j++){
				if($Lote2_!=0){
					$LoteActual=$Lote2_[$j];//Se obtiene el Lote
					$PrecioLote2[$j]=queries::ObtenerPrecioLote($LoteActual);//Obtencion del precio con el que entra ese lote
				}
			}//fin de for
		}//if tope2
		*/
		/*Obtencion de recetas satifechas e insatisfechas globales parametros ...,0,0)*/
		$sat=ObtenerRecetasSatisfechas($IdReceta,$Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad,0,0);
		$insat=ObtenerRecetasInsatisfechas($IdReceta,$Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad,0,0);
		
		//***********
		//$cantidad=$row3["Cantidad"];
		
		//$VerSatisfecha=verificaSatisfecha($Medicina,$IdReceta,$IdHistorialClinico);
		//if($vector=pg_fetch_array($VerSatisfecha)){$consumo=$consumo+$cantidad;}
					
		//$costo=$consumo*$precioActual;//}//while row3
		$Cantidad_Total=ObtenerConsumoTotalMedicamento($Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad);//CANTIDAD TOTAL DE RECETAS SATIS. Y NO SATIS.
			?>
		
			<tr class="FONDO2">
			  <td style="vertical-align:middle;">&nbsp;<?php echo $codigoMedicina;?></td>
			  <td align="center" style="vertical-align:middle">&nbsp;<?php echo $NombreMedicina;?></td>
			  <td align="center" style="vertical-align:middle;"><?php echo $concentracion;?></td>
			  <td align="center" style="vertical-align:middle;"><?php echo $presentacion;?></td>
			  <td align="center" style="vertical-align:middle;">&nbsp;<?php echo $Nrecetas;?></td>
			  <td align="center" style="vertical-align:middle;">&nbsp;<?php echo $sat;?></td>
			  <td align="center" style="vertical-align:middle;">&nbsp;<?php echo $insat;?></td>
			  <td align="center" style="vertical-align:middle;"><?php echo $UnidadMedida;?></td>
			  <td align="center" style="vertical-align:middle;">&nbsp;<?php /*
			  $Cantidad_de_Lote=0;$j=1;//Comparacion de igualdad
		
			  for($i=0;$i<$tope;$i++){
				  if($PrecioLote[$i]!=NULL){
				  $CodigoLote=queries::CodigoLote($Lote[$i]);
				  $CantidadUnidadMedida[$i]=$Cantidad[$i]/$Divisor;
					echo"Cantidad: ".$CantidadUnidadMedida[$i]."<br>Precio($): ".$PrecioLote[$i]."<br>Lote: ".$CodigoLote."<br><br>";
				  }
			  }//fin de for
			  
			  if($tope2!=0 || $tope2!=NULL){
				  for($j=0;$j<$tope2;$j++){
						  if($Lote2_[$j]!=0){
				  $CodigoLote=queries::CodigoLote($Lote2_[$j]);
				  $CantidadUnidadMedida2[$j]=$Cantidad2_[$j]/$Divisor;
				  
						echo"Cantidad: ".$CantidadUnidadMedida2[$j]."<br>Precio($): ".$PrecioLote2[$j]."<br>Lote: ".$CodigoLote."<br>";
					  }
				  }//fin de for
			  }//if tope2
			  */
			  ?></td>
			  <td align="center" style="vertical-align:middle;"><?php echo $Cantidad_Total/$Divisor;?></td>
			  <td align="center" style="vertical-align:middle;">$&nbsp;<?php /*
			  $tope=count($CantidadUnidadMedida);
				for($i=0;$i<$tope;$i++){
				  $Monto=round($CantidadUnidadMedida[$i]*$PrecioLote[$i],2);
				  $Monto_Total=$Monto_Total+$Monto;
				}//fin de for	  
			  
			  $tope2=count($CantidadUnidadMedida2);
				for($j=0;$j<$tope2;$j++){
					if($Cantidad2_[$j]!=0){
						$Monto2=round($CantidadUnidadMedida2[$j]*$PrecioLote2[$j],2);
					}else{
						$Monto2=0;
					}
				  $Monto_Total2=$Monto_Total2+$Monto2;
				}//fin de for	  
			  
			  echo $Monto_Total+$Monto_Total2;*/
			  ?></td>
			</tr>
		<?php	
				$Cantidad=array();$Cantidad2_=array();
				$PrecioLote=array();$PrecioLote2=array();
				$Lote=array();$Lote2_=array();
				}//if row2
			}//while externo
		?>     
			<tr class="MYTABLE">
			  <td colspan="11">&nbsp;		</td>
			</tr>
		  
		<?php
		
	}//while de nombreTera

}//While Servicios?>
</table>
</div>
</body>
</html>
<?php
conexion::desconectar();
}//Fin de IF nivel == 1

}//Fin de IF isset de Nivel
?>