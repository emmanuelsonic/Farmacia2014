<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<script language="javascript">
window.location='../../signIn.php';
</script>
<?php
}else{
if($_SESSION["nivel"]!=1 and $_SESSION["nivel"]!=2 and $_SESSION["nivel"]!=4){?>
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
$query=new queries;
conexion::conectar();
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../default.css" media="screen" />
<title>...:::Consumo por Grupo Terapeutico:::...</title>
<style type="text/css">
#Layer11 {
	position:absolute;
	left:9px;
	top:255px;
	width:826px;
	height:192px;
	z-index:1;
}
.style1 {color:#0000CC; font-size:11px; font-family:Arial, Helvetica, sans-serif}
#Layer61 {position:absolute;
	left:25px;
	top:105px;
	width:955px;
	height:30px;
	z-index:2;
}
#Layer21 {
	position:absolute;
	left:865px;
	top:44px;
	width:52px;
	height:20px;
	z-index:6;
}
#Layer31 {
	position:absolute;
	left:688px;
	top:2px;
	width:245px;
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
P{page-break-after:inherit}
}
#Layer3 {position:absolute;
	left:2px;
	top:190px;
	width:1001px;
	height:30px;
	z-index:6;
}
.style4 {font-size: 24px}
#Layer41 {position:absolute;
	left:-199px;
	top:-39px;
	width:55px;
	height:31px;
	z-index:7;
}
#Layer71 {position:absolute;
	left:303px;
	top:39px;
	width:596px;
	height:23px;
	z-index:5;
}
</style>
<script language="javascript">
function popUp(URL) {
day = new Date();
id = day.getTime();
//id=document.formulario.fecha.value;
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=1025,height=500,left = 20,top = 100');");
}//popUp
</script> 
</head>
<body>
<script language="javascript" src="../../tooltip/wz_tooltip.js"></script>

<div class="style1" id="Layer61">
  <?php

echo"<strong>Nombre de Usuario:</strong>&nbsp;&nbsp; $nombre </br>
<strong>Tipo de Usuario:</strong>&nbsp;&nbsp;$tipoUsuario<br>
<strong>Nick:</strong>&nbsp;&nbsp;$nick<br>";
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
<div id="Layer3" align="center">
  <?php if($nivel==1){?>
<script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menu_.js">'+'</scr'+'ipt>');/*img src="MenuImages/Menu.gif" moduleid="Default (Project)\Menu_off.xws"*/</script>
  <?php }elseif($nivel==4){?>
    <script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menudigitador.js">'+'</scr'+'ipt>');/*img src="MenuImages/MenuConsultaExterna.gif" moduleid="MenuConExt (Project)\MenuConsultaExterna_off.xws"*/</script>
  <?php }else{?>
<script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menucoadmin.js">'+'</scr'+'ipt>');/*img src="MenuImages/MenuCoAdmin.gif" moduleid="MenuCoAdmin (Project)\MenuCoAdmin_off.xws"*/</script>
  <?php }?>
</div>
<div id="Layer71">
  <div id="Layer41"><img src="../../images/paisanito.jpg" alt="" width="195" height="94" /></div>
  <span class="style4">Ministerio de Salud P&uacute;blica y Asistencia Social </span></div>
<?php 
//html { font: 100%/2.5 Arial, Helvetica, sans-serif; }
$FechaInicio=explode('-',$_REQUEST["fechaInicio"]);
$FechaFin=explode('-',$_REQUEST["fechaFin"]);
$FechaInicio2=$FechaInicio[2].'-'.$FechaInicio[1].'-'.$FechaInicio[0];
$FechaFin2=$FechaFin[2].'-'.$FechaFin[1].'-'.$FechaFin[0];
$FechaInicio=$_REQUEST["fechaInicio"];
$FechaFin=$_REQUEST["fechaFin"];

$IdArea=$_REQUEST["area"];
$resp=pg_query("select Area from mnt_areafarmacia where IdArea='$IdArea'");
$RowArea=pg_fetch_array($resp);
$area=$RowArea[0];
?>
<div id="Layer11">
  <table width="967">
      <tr class="MYTABLE">
      <td colspan="10" align="center">HOSPITAL NACIONAL ROSALES<br>
        <strong>CONSUMO DE MEDICAMENTOS POR GRUPO TERAPEUTICO</strong> <br>
&Aacute;rea: <strong><?php echo $area;?></strong><br>
PERIODO DEL: <?php echo"$FechaInicio2";?> AL <?php echo"$FechaFin2";?> .- <br>
<div align="left">Fecha de Emisi&oacute;n: 
  <?php 
$DateNow=date("d-m-Y");
echo"$DateNow";?></div></td>
    </tr>
  </table>
 <?php
//*****FILTRACION DE MEDICINA Y GRUPOS  Y FECHAS
$grupoTerapeutico=$_REQUEST["select1"];
if(isset($_REQUEST["select2"])){$medicina=$_REQUEST["select2"];}else{$medicina=0;}
?>
<form action="" method="post" name="formulario">
<input name="fechaInicio" type="hidden" value="<?php echo"$FechaInicio";?>">
<input name="fechaFin" type="hidden" value="<?php echo"$FechaFin";?>">
<input name="select1" type="hidden" value="<?php echo"$grupoTerapeutico";?>">
<input name="select2" type="hidden" value="<?php echo"$medicina";?>">
<input name="area" type="hidden" value="<?php echo $IdArea;?>">
<input name="NomArea" type="hidden" value="<?php echo $area;?>">
<div id="Layer31" align="right">
<table>
<tr><td>&nbsp;</td><td>
<input type="button" name="imprimir" value="Vista Previa" onClick="javascript:popUp('impresion.php?select1='+ document.formulario.select1.value +'&select2='+ document.formulario.select2.value +'&fechaInicio='+ document.formulario.fechaInicio.value +'&fechaFin='+ document.formulario.fechaFin.value+'&area='+document.formulario.area.value+'&NomArea='+document.formulario.NomArea.value);" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099">
</td></tr>
<tr><td><input type="text" id="NombreArchivo" name="NombreArchivo" maxlength="50" size="15" value="NombreArchivo" onFocus="if(this.value=='NombreArchivo'){this.value=''}" onBlur="if(this.value==''){this.value='NombreArchivo'}"></td>
<td>
<input type="button" name="Exportar" value="Exportar Excel" onClick="javascript:popUp('ReporteExcelGrupos.php?select1='+ document.formulario.select1.value +'&select2='+ document.formulario.select2.value +'&fechaInicio='+ document.formulario.fechaInicio.value +'&fechaFin='+ document.formulario.fechaFin.value+'&area='+document.formulario.area.value+'&NomArea='+document.formulario.NomArea.value+'&nombreArchivo='+document.formulario.NombreArchivo.value);"  onMouseOver="this.style.color='#CC0000';Tip('Exportar a Excel<br><img src=\'../../images/excel.GIF\'>',TEXTALIGN,'center')" onMouseOut="this.style.color='#000000'; UnTip()" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099">
</td></tr>
<tr><td>&nbsp;</td><td>
<input type="button" name="Regresar" value="Regresar" onClick="javascript:window.location='Rep_GrupoTerapeutico.php'" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099" title="Regresar">
</td></tr>
</table>
</div>
</form>
<table width="968">
<?php
//*************************************
//******************************* QUERIES Y RECORRIDOS
$nombreTera=$query->NombreTera($grupoTerapeutico);
while($grupos=pg_fetch_array($nombreTera)){
$NombreTerapeutico=$grupos["GrupoTerapeutico"];
$IdTerapeutico=$grupos["IdTerapeutico"];
if($NombreTerapeutico!="--"){

$resp=$query->QueryExterna($IdTerapeutico,$medicina,$IdArea,$FechaInicio,$FechaFin);
if($test=pg_fetch_array($resp)){?>
	
    <tr class="MYTABLE">
      <td colspan="11" align="center"><P>
&nbsp;<strong><?php echo"$NombreTerapeutico";?></strong></td>
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
      <th width="78" scope="col">Consumo</th>
      <th width="135" scope="col">Consumo/Lote</th>
      <th width="136" scope="col">Monto</th>
    </tr>
	<?php
$resp1=$query->QueryExterna($IdTerapeutico,$medicina,$IdArea,$FechaInicio,$FechaFin);
	while($row=pg_fetch_array($resp1)){
$GrupoTerapeutico=$IdTerapeutico;
$Medicina=$row["IdMedicina"];
$codigoMedicina=$row["Codigo"];
$NombreMedicina=$row["Nombre"];
$concentracion=$row["Concentracion"];
$presentacion=$row["FormaFarmaceutica"];

$Nrecetas=0;//conteo de recetas
$consumo=0;


$respuesta=$query->ObtenerReporteGrupoTerapeutico($GrupoTerapeutico,$Medicina,$FechaInicio,$FechaFin,$IdArea);
	$Nrecetas=pg_num_rows($respuesta);
		if($row2=pg_fetch_array($respuesta)){ /* verificacion de datos */
$precioActual=0;
$respuesta2=$query->ObtenerReporteGrupoTerapeutico($GrupoTerapeutico,$Medicina,$FechaInicio,$FechaFin,$IdArea);  
//		while($row3=pg_fetch_array($respuesta2)){
//IdReceta
$row3=pg_fetch_array($respuesta2);
$IdReceta=$row3["IdReceta"];
$IdHistorialClinico=$row3["IdHistorialClinico"];
$Divisor=$row3["Divisor"];//Divisor de conversion
$UnidadMedida=$row3["Descripcion"];//Tipo de unidad de Medida
$satisfechas=0;
$insatisfechas=0;

$respLotes=queries::ObtenerConsumosMedicamentoLote($Medicina,$IdArea,$FechaInicio,$FechaFin);


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
									if($Cantidad2!=NULL and $Lote2!=NULL and $Cantidad2!=0 and $Lote2!=0){
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

/*Obtencion de recetas satifechas e insatisfechas globales parametros ...,0,0)*/
$sat=$query->ObtenerRecetasSatisfechas($IdReceta,$Medicina,$FechaInicio,$FechaFin,$IdArea,0,0);
$insat=$query->ObtenerRecetasInsatisfechas($IdReceta,$Medicina,$FechaInicio,$FechaFin,$IdArea,0,0);

//***********
$cantidad=$row3["Cantidad"];

$VerSatisfecha=$query->verificaSatisfecha($Medicina,$IdReceta,$IdHistorialClinico);
if($vector=pg_fetch_array($VerSatisfecha)){$consumo=$consumo+$cantidad;}
			
$costo=$consumo*$precioActual;//}//while row3
$Cantidad_Total=$Cantidad_1+$Cantidad_2;//CANTIDAD TOTAL DE RECETAS SATIS. Y NO SATIS.
	?>

    <tr class="FONDO2">
      <td>&nbsp;<?php echo $codigoMedicina;?></td>
      <td align="center" style="vertical-align:middle">&nbsp;<?php echo $NombreMedicina;?></td>
      <td>&nbsp;<?php echo $concentracion;?></td>
      <td>&nbsp;<?php echo $presentacion;?></td>
	  <td align="center">&nbsp;<?php echo $Nrecetas;?></td>
      <td align="center">&nbsp;<?php echo $sat;?></td>
      <td align="center">&nbsp;<?php echo $insat;?></td>
	  <td align="center"><?php echo $UnidadMedida;?></td>
      <td align="center">&nbsp;<?php echo $Cantidad_Total/$Divisor;?></td>
	  <td align="center">&nbsp;
	  <?php 
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
	  ?>
	 </td>
      <td align="center">$&nbsp;<?php 
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
	  
	  echo $Monto_Total+$Monto_Total2;
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
      <td colspan="11">&nbsp;
</td>
    </tr>
  
<?php
	}//nuevo IF test
}//IF NombreTerapeutico!=--
}//while de nombreTera?>
</table>
</div>
</body>
</html>
<?php
conexion::desconectar();
}//Fin de IF nivel == 1

}//Fin de IF isset de Nivel
?>