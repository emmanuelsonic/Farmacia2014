<?php  session_start();?>
<style type="text/css">
<!--
#Layer1 {
	position:absolute;
	left:330px;
	top:154px;
	width:422px;
	height:117px;
	z-index:1;
}
-->
</style>
<div id="Layer1"><?php 
echo'<link rel="stylesheet" type="text/css" href="default.css" media="screen" />';
echo '<h1><img src="images/carga.gif" />INICIANDO SESION</h1>';
?></div>
<?php
include 'Clases/class.php';
$usuario=$_REQUEST["usuario"];
$contra=$_REQUEST["contra"];
$contra=md5($contra);
$db=new conexion();
$logear="SELECT * FROM fos_user_user where username='$usuario' and password='$contra'";
$result3 =$db->consulta($logear);
if($row = pg_fetch_array($result3, null, PGSQL_ASSOC)){
	 			 		$id=$row["id"];         
						$nombre=$row["firstname"];
						$nick=$row["username"];
						$farmacia=$row["idfarmacia"];
						$nivel=$row["nivel"]; 
						$datos=$row["datos"];
						$reporte=$row["reportes"];
						$Administracion=$row["administracion"];
						$primera=$row["primeravez"];
						$IdArea=$row["idarea"];
						$IdEstadoCuenta=$row["estadocuenta"];
						$IdEstablecimiento=$row["id_establecimiento"];
                                                $IdModalidad=$row["idmodalidad"];
					    //$NombreFarmacia=$row["Farmacia"];



//$resp=mysql_query("select Farmacia from mnt_farmacia where IdFarmacia='$farmacia'");
$query="select Nombre,IdTipoFarmacia,TipoExpediente from mnt_establecimiento 
        inner join mnt_modalidadxestablecimiento 
        on mnt_modalidadxestablecimiento.IdEstablecimiento=mnt_establecimiento.IdEstablecimiento 
        where mnt_establecimiento.IdEstablecimiento=".$IdEstablecimiento;
$query2="select Area from mnt_areafarmacia
        inner join mnt_areafarmaciaxestablecimiento 
        on mnt_areafarmaciaxestablecimiento.IdArea=mnt_areafarmacia.Id
        where mnt_areafarmaciaxestablecimiento.IdArea=$IdArea and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";

$NombreEstablecimiento1=pg_fetch_array($db->consulta($query));
$Area=pg_fetch_array($db->consulta($query2));

$db->consulta("update fos_user_user set conectado='S' where Id=".$id);
$db->consulta("update fos_user_user set last_login=now() where Id=".$id);		
		

	$NombreEstablecimiento=$NombreEstablecimiento1["Nombre"];
	$IdTipoFarmacia=$NombreEstablecimiento1["IdTipoFarmacia"];
	$TipoExpediente=$NombreEstablecimiento1["TipoExpediente"];
	

		

	$Area=$Area[0];
if($IdEstadoCuenta=='D'){?>
	<script language="javascript">
	window.location='des.php?Cuenta=1';
	</script>
<?php }

//$row=mysql_fetch_array($resp);
$_SESSION["farmacia"]='';

if($nivel==1){
//***** nivel de administrador ********
$_SESSION["IdFarmacia2"]=0;
}else{
$_SESSION["IdFarmacia2"]=$farmacia;
}

$_SESSION["IdArea"]=$IdArea;
//PERMISOS DE USUARIO*********************
//$_SESSION["count"]=1;
$_SESSION["Datos"]=$datos;
$_SESSION["Reportes"]=$reporte;
$_SESSION["Administracion"]=$Administracion;

//******ESTABLECIMIENTO EN EL QUE LABORA
$_SESSION["IdEstablecimiento"]=$IdEstablecimiento;
$_SESSION["NombreEstablecimiento"]=$NombreEstablecimiento;
$_SESSION["TipoFarmacia"]=$IdTipoFarmacia;
$_SESSION["TipoExpediente"]=$TipoExpediente;
$_SESSION["Area"]=$Area;
$_SESSION["IdModalidad"]=$IdModalidad;

//***************************************
$_SESSION["ID"]=$id;
$_SESSION["IdPersonal"]=$id;
$_SESSION["nick"]=$nick;
$_SESSION["Login"]=$nick;


//****************************************

//Cuando el personal es de despacho
$_SESSION["conteo"]=0;
$_SESSION["conteoAux"]=0;
//*****************************************

$_SESSION["nivel"]=$nivel;//obtencion de nivel de seguridad
$_SESSION["Nivel"]=$nivel;
//Obtencion de primera vez
$_SESSION["primera"]=$primera;
//tipos de usuarios


if($nivel=='1'){
$_SESSION["tipo_usuario"]="Administrador";
}
if($nivel=='2'){
$_SESSION["tipo_usuario"]="Co-Administrador";
}
if($nivel=='3'){
$_SESSION["tipo_usuario"]="Personal Farmacia";
}
if($nivel=='4'){
$_SESSION["tipo_usuario"]="Personal Farmacia";
}
if($nivel=='5'){
$_SESSION["tipo_usuario"]="Bodega";
}

//tipos de usuarios
$_SESSION["nombre"]=$nombre;


/*QUERY PARA DETERMINAR SI ESTE DIA YA SE HISO LA COMPROBACION DE EXISTENCIAS*/
//$FechaModificacion=mysql_fetch_array(mysql_query("select FechaModifica from farm_modificavirtual where IdAreaModifica='$IdArea' and FechaModifica=curdate()"));

//if($FechaModificacion[0]==NULL and $nivel=='3'){
//$queryModifica="update farm_modificavirtual set FechaModifica=curdate() where IdAreaModifica='$IdArea'";
//mysql_query($queryModifica);
    conexion::desconectar();
?>
<script language="javascript">
//window.location='ExistenciaVirtual/ExistenciaVirtualPrincipal.php'
</script>
<?php //}//fin de if IdAreas
/*FIN DE CUERPO DE MANEJO DE EXISTENCIA VIRTUAL*/

/*
	JUEGO DE NIVELES
   1 Y 2: ADMINISTRADO Y CO-ADMINISTRADOR
       3: TECNICO DE FARMACIA [VENTANILLAS]
       4: CENTRO DE COMPUTO [DIGITADORES DE FARMACIA]


*/

if($nivel==1 or $nivel==2){

?>
<script language="javascript">
   window.location='Principal/index.php';
</script>
<?php
}

if($nivel==3){ ?>
<script language="javascript">
    window.location='Principal/index2.php';
</script>
<?php 
}



if($nivel==4){ ?>
<script language="javascript">
    window.location='IngresoRecetasTodas/IntroduccionRecetasPrincipal.php';
</script>
<?php
}

if($nivel==5){ ?>
<script language="javascript">
     window.location='Principal/index.php';
</script>
<?php
}


} //if si Existen Datos
else{

?>
 <script LANGUAGE="JavaScript">
 window.location="signIn.php?bandera=1"
  </script>
<?php
}

?>
