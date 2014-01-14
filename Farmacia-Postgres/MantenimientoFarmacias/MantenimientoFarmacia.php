<?php include('../Titulo/Titulo.php');

if(!isset($_SESSION["Administracion"])){?>
<script language="JavaScript">
alert('Debe iniciar sesion!');
window.location='../signIn.php';
</script>
<?php 
}

if($_SESSION["Administracion"]!=1){
?>
<script language="JavaScript">
alert('No posee suficientes privilegios para utilizar esta herramienta!');
window.location='../IngresoRecetasTodas/IntroduccionRecetasPrincipal.php';
</script>
<?php	
}
include('../Clases/class.php');
function ComboFarmacias(){
   conexion::conectar();
		//if($_SESSION["TipoFarmacia"]==1){$comp=" and IdFarmacia <> 4";}else{$comp="";}
	$SQL="select distinct mf.Id as idFarmacia,Farmacia 
              from mnt_farmacia mf
              inner join mnt_farmaciaxestablecimiento mfe
              on mfe.IdFarmacia=mf.Id
              where mfe.HabilitadoFarmacia ='S'
              and mfe.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]."
              and mfe.IdModalidad=".$_SESSION["IdModalidad"];
	$resp=pg_query($SQL);
   conexion::desconectar();

	$combo="<select id='IdFarmacia' name='IdFarmacia' onchange='CargarAreas(this.value);'>
		<option value='0'>[SELECCIONE]</option>";
	    while($row=pg_fetch_array($resp)){
		$combo.="<option value='".$row["idfarmacia"]."'>".$row["farmacia"]."</otion>";
	    }
	$combo.="</select>";
	return($combo);
}


function Farmacias(){
	conexion::conectar();
	
   $SQL="select id as IdFarmacia,Farmacia, 
         ( select HabilitadoFarmacia 
           from mnt_farmaciaxestablecimiento 
           where IdFarmacia=mnt_farmacia.Id 
           and IdEstablecimiento=".$_SESSION["IdEstablecimiento"]."
           and IdModalidad=".$_SESSION["IdModalidad"].") as HabilitadoFarmacia
         from mnt_farmacia";
   $resp=pg_query($SQL);
	conexion::desconectar();
	$out="<table width='50%'>
		<tr><td>Numero</td><td>Farmacia</td><td align=center>Habilitado</td></tr>
		<tr><td colspan=3><hr></td></tr>";
	while($row=pg_fetch_array($resp)){
            
            
	
	   if($row["habilitadofarmacia"]=='S'){$check="<input type='checkbox' id='".$row["idfarmacia"]."' name='".$row["idfarmacia"]."' onclick='Habilitar(".$row["idfarmacia"].",2)' checked=true>";}else{$check="<input type='checkbox' id='".$row["idfarmacia"]."' name='".$row["idfarmacia"]."' onclick='Habilitar(".$row["idfarmacia"].",1)'>";}
	   //if($row["IdFarmacia"]==4){$check="<input id='".$row["IdFarmacia"]."' type='checkbox' checked=true disabled=true>";}

	   $out.="<tr><td>".$row["idfarmacia"]."</td><td><span id='spanExt".$row["idfarmacia"]."'><span id='span".$row["idfarmacia"]."' onclick='CambioNombre(".$row["idfarmacia"].")'>".$row["farmacia"]."</span></span></td><td align=center>".$check."</td></r>";
	}
	$out.="</table>";
   return($out);
}

?>
<html>

<head><title>Mantenimiento de Farmacias</title>
<link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
<script language="JavaScript" src="IncludeFiles/ManttoFarmacia.js"></script>
<script language="JavaScript" src="../trim.js"></script>
<?php head();?>
</head>
<?php Menu();?>
<br><br>

<center>
<table width="50%">
   <tr class="MYTABLE"><td align="center"><strong>CONFIGURACION DE FARMACIA</strong></td></tr>
   <tr><TD class="FONDO" align="center"><?php echo Farmacias();?></TD></tr>


    <tr class="MYTABLE"><td align="center">Mantenimiento de Areas</td></tr>
    <tr class="FONDO"><td align="center">Farmacia: <span id="ComboFarmacia"><?php echo ComboFarmacias();?></span></td></tr>
    <tr class="FONDO"><td ><div id="acciones" align="center">&nbsp;</div></td></tr>
    <tr class="MYTABLE"><td >&nbsp;</td></tr>
</table>



</center>
</html>