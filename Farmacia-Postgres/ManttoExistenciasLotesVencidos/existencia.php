<?php include('../Titulo/Titulo.php');
if(!isset($_SESSION["nivel"])){?>
<script language="javascript">
window.location='../signIn.php';
</script>
<?php
}else{
if($_SESSION["Administracion"]!=1 and $_SESSION["Datos"]!=1){?>
<script language="javascript">
alert('No posee permisos para accesar a esta opcion!');
window.location='../Principal/index.php';
</script>
<?php
}else{
$IdFarmacia2=0;
if($IdFarmacia2!=0){?>
<script language="javascript">window.location='estableceArea.php';</script>
<?php }else{
	
	$nombre=$_SESSION["nombre"];
	$nivel=$_SESSION["nivel"];
	$nick=$_SESSION["nick"];
	$IdFarmacia=$_SESSION["IdFarmacia2"];
	$tipoUsuario=$_SESSION["tipo_usuario"];

	require('../Clases/class2.php');
	conexion::conectar();

function ComboTerapeutico(){
    $SQL="select * from mnt_grupoterapeutico where GrupoTerapeutico <>'--'";
    $resp=pg_query($SQL);
	$combo="<select Id='Terapeutico' name='Terapeutico' onchange='MedicamentoPorGrupo(this.value);'>
		<option value='0'>[SELECCIONE UN GRUPO TERAPEUTICO]</option>";
	while($row=pg_fetch_array($resp)){
	   $combo.="<option value='".$row["IdTerapeutico"]."'>".$row["IdTerapeutico"].' - '.$row["GrupoTerapeutico"]."</option>";
	}
	$combo.="</select>";
	
	return($combo);

}
function ComboAreas(){
    $SQL="select * from mnt_areafarmacia where Habilitado='S' and IdArea <> 7";
    $resp=pg_query($SQL);
	$combo="<select Id='IdArea' name='IdArea' onchange='ActualizarLista(this.value);'>
		<option value='0'>[SELECCIONE UNA AREA]</option>";
	while($row=pg_fetch_array($resp)){
	   $combo.="<option value='".$row["IdArea"]."'>".$row["Area"]."</option>";
	}
	$combo.="</select>";
	
	return($combo);

}

?>
<html>
<head>
<?php head(); ?>
<link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
<title>...:::Actualizacion de Existencias:::...</title>
<script language="javascript" src="guardar.js"></script>
<script language="javascript" src="procesos/calendar.js"></script>
<script language="JavaScript" src="../noCeros.js"></script>
<script language="javascript">
<!--
var nav4 = window.Event ? true : false;
function acceptNum(evt){	
// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57, 46 = '.'
var key = nav4 ? evt.which : evt.keyCode;	
return ((key < 13) || (key >= 48 && key <= 57) || key==46);
}

function acceptNum2(evt){	
// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
var key = nav4 ? evt.which : evt.keyCode;
return ((key < 13) || (key >= 48 && key <= 57 ||(key==46)));
}

function PosicionTerapeutico(IdTerapeutico){
   var Opciones=document.getElementById('Terapeutico').length;
   for(var i=0;i<Opciones;i++){
	if(document.getElementById('Terapeutico')[i].value==IdTerapeutico){
	    document.getElementById('Terapeutico')[i].selected=true;
		MedicamentoPorGrupo(IdTerapeutico);
	    return(false);
	}
   }
}

function PosicionArea(IdArea){
   var Opciones=document.getElementById('IdArea').length;
   for(var i=0;i<Opciones;i++){
	if(document.getElementById('IdArea')[i].value==IdArea){
	    document.getElementById('IdArea')[i].selected=true;
	    return(false);
	}
   }
}
</script>

</head>
<?php $IdTerapeutico=0;
	if(isset($_GET["IdTerapeutico"])){$IdTerapeutico=$_GET["IdTerapeutico"];}
	if(isset($_GET["IdArea"])){$IdArea=$_GET["IdArea"];}?>
<body onload="PosicionTerapeutico(<?php echo $IdTerapeutico;?>);PosicionArea(<?php echo $IdArea;?>);">
<?php Menu(); ?>
<br>

<form id="formulario" name="formulario" action="saving.php" method="post">
<table width="994" border="1">
  <tr class="MYTABLE">
    <th height="78" colspan="6" scope="col"><p>INTRODUCCI&Oacute;N DE EXISTENCIAS VENCIDAS [PRIMERA VEZ]
        </p>
      <div id="botones" align="right">
      <!-- <input type="submit" name="guardar2" value="Guardar" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099">

	  <input type="button" name="cancelar2" value="Cancelar" onClick="javascript:window.location='../inicio.php'" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099"> -->
	  </div></th>
  </tr>
<tr><td align="center" class="FONDO">AREA: <?php echo ComboAreas();?></td></tr>
<tr><td align="center" class="FONDO">GRUPO TERAPEUTICO: <?php echo ComboTerapeutico();?></td></tr>
<tr><td class="FONDO">&nbsp;</td></tr>
  
<tr><td class="FONDO"><div id="Medicamentos" align="center"></div></td></tr>

</table>
   
</form>

</body>
</html>
<?php
conexion::desconectar();
		}//Else $IdFarmacia!=0
}//Fin de IF nivel == 1

}//Fin de IF isset de Nivel

?>