<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<script language="javascript">
window.location='../signIn.php';
</script>
<?php
}else{
require('../Clases/class.php');
$tipoUsuario=$_SESSION["tipo_usuario"];
$nombre=$_SESSION["nombre"];
$nivel=$_SESSION["nivel"];
$nick=$_SESSION["nick"];
$IdArea=$_SESSION["IdArea"];

/*
require('include/conexion.php');
require('include/funciones.php');
require('include/pagination.class.php');
require('include/RecetasClass.php');
$FechaD=new ClassFechaAtras;
$Classquery=new Classquery;
//****obtencion de fechas validas de recetas (3 dias habiles)

$selectNombreFecha="select dayname(curdate()) as NombreFechaActual";
$NombreDiaActual = mysql_query($selectNombreFecha, $link);
$rowNombre=mysql_fetch_array($NombreDiaActual);
$NombreFecha=$rowNombre["NombreFechaActual"];
$FechaAtras=$FechaD->ObtenerFechaAtras($NombreFecha,$link);

//***
$items = 10;
$page = 1;

if(isset($_GET['page']) and is_numeric($_GET['page']) and $page = $_GET['page'])
		$limit = " LIMIT ".(($page-1)*$items).",$items";
	else
		$limit = " LIMIT $items";

if(isset($_GET['q']) and !eregi('^ *$',$_GET['q'])){
		$q = sql_quote($_GET['q']); //para ejecutar consulta
		$busqueda = htmlentities($q); //para mostrar en pantalla
		$Bandera=1;
			
//and month(farm_recetas.Fecha)=month(CURDATE())  Esta sentencia va si las recetas de un mes no pueden dar en otro mes
//a pesar que la vida de una receta sean 3 dias...Ej. 29/02/2008 --->  01/03/2008
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,$FechaAtras,$q);
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,$FechaAtras,$q);
}else{
$Bandera=0;
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,$FechaAtras,"");
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,$FechaAtras,"");
}
    //fecha de vida de una receta son 3 dias habiles
$query = mysql_query($sqlStr.$limit, $link);
$aux = Mysql_Fetch_Assoc(mysql_query($sqlStrAux,$link));*/
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
<title>...:::BUSQUEDA DE RECETAS:::...</title>
<link rel="stylesheet" href="pagination.css" media="screen">
<link rel="stylesheet" href="style.css" media="screen">
<!--<script src="include/buscador.js" type="text/javascript" language="javascript"></script>-->

<!-- **************	AUTOCOMPLETAR	******************* -->
	<script type="text/javascript" src="include/scripts/prototype.js"></script>
	<script type="text/javascript" src="include/scripts/autocomplete.js"></script>
	<link rel="stylesheet" type="text/css" href="include/styles/autocomplete.css" />
<!-- ****************************************************** --> 

<style type="text/css">
<!--
#Layer1 {
	position:absolute;
	left:60px;
	top:224px;
	width:826px;
	height:33px;
	z-index:1;
}

#resultados{
	position:absolute;
	left:44px;
	top:286px;
	width:854px;
	height:156px;
	z-index:2;
}
#Layer2 {
	position:absolute;
	left:400px;
	top:564px;
	width:58px;
	height:31px;
	z-index:3;
}
#Layer6 {	position:absolute;
	left:25px;
	top:105px;
	width:955px;
	height:30px;
	z-index:2;
}
.style1 {color:#0000CC; font-size:11px; font-family:Arial, Helvetica, sans-serif}
.style4 {font-size: 24px}
#Layer4 {	position:absolute;
	left:-199px;
	top:-39px;
	width:55px;
	height:31px;
	z-index:7;
}
#Layer7 {position:absolute;
	left:303px;
	top:39px;
	width:596px;
	height:23px;
	z-index:5;
}
#Layer3 {position:absolute;
	left:-1px;
	top:173px;
	width:836px;
	height:34px;
	z-index:6;
}
-->
</style>
<script language="javascript">
function inicio(){
document.form.q.focus();
}//inicio

</script>
</head>

<body>

<!-- ***************** Encabezado de pagina y menu ***********************-->

<div class="style1" id="Layer6" >
      <?php
$NombreDeFarmacia=$_SESSION["IdFarmacia2"];
encabezado::top($NombreDeFarmacia,$tipoUsuario,$nick,$nombre);

?>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</div>
<div id="Layer7">
  <div id="Layer4"><img src="../images/paisanito.jpg" alt="" width="195" height="94" /></div>
  <span class="style4">Ministerio de Salud P&uacute;blica y Asistencia Social </span></div>
<div id="Layer3">
  <script webstyle4>document.write('<scr'+'ipt src="../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../MenuImages/menuconsultaexterna.js">'+'</scr'+'ipt>');/*img src="MenuImages/MenuConsultaExterna.gif" moduleid="MenuConExt (Project)\MenuConsultaExterna_off.xws"*/</script>
</div>

<!-- *********************************************************************************-->


	
<form name="form" action="buscador_terapeutico.php" onSubmit="return buscar()">
    <div id="Layer1">
	<table width="100%">
	  <tr>
	     <td width="380" align="center" colspan="2"><strong>Busqueda de Recetas Listas </strong></td>
	   </tr>
	   <tr>
	    <td><strong>No. EXPEDIENTE:</strong> </td>
	    <td><input type="text" id="q" name="q" size="50"></td>
	   </tr>
	   <tr>
		<td><strong>Nombre:</strong></td><TD><div id="NombrePaciente"></div></TD>
	   </tr>
	   <tr>
		<td colspan="2">
		<!-- Variables ocultas -->
		<input type="hidden" id='IdArea' name="IdArea" value="<?php echo $IdArea;?>">
		<input type="text" id="IdReceta" name="IdReceta">
		
		</td>
	   </tr>
	</table>
    </div>
</form>
    
<div id="resultados" align="center">
	
</div>



<!-- codigo de autocompletado -->
	<script>
		new Autocomplete('q', function() { 
			var IdArea=document.getElementById('IdArea').value;
			return 'respuesta.php?Bandera=1&q='+this.value+'&IdArea='+IdArea; 
		});
	</script>

<!-- *************************-->


</body>
</html>
<?php 

}//Fin de IF isset de Nivel ?>
