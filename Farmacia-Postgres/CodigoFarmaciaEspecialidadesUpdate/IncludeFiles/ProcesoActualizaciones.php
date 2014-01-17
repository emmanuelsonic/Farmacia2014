<?php session_start();
include('ClaseActualizaciones.php');
$Bandera=$_GET["Bandera"];
conexion::conectar();
$update=new Actualizaciones;

// VAR GENERALES
$IdModalidad=$_SESSION["IdModalidad"];


switch($Bandera){
	case 1:
		$navegador='';
		$pagina=$_GET["pagina"];
		
		$resp=$update->DatosGenerales($pagina,$_SESSION["IdEstablecimiento"],$IdModalidad);
		$Total=$update->Tope($_SESSION["IdEstablecimiento"],$IdModalidad);
		
		/*		NAVEGADOR		*/
		$Tope=ceil($Total/20);
		$Actual=($pagina/20)+1;
		
		if($pagina==0){
			/*	NO HABILITADO	*/
			$navegador.="<input type='button' id='Anterior' name='Anterior' value='< Anterior' onclick='' disabled='disabled'> 1  de ".$Tope." ";
			
		}else{
			$paginaAntes=$pagina-20;
			
						$navegador.="<input type='button' id='Anterior' name='Anterior' value='< Anterior' onclick='javascript:FillGrid(".$paginaAntes.")'> ".$Actual." de ".$Tope." ";
		}
		
		
		if($Actual==$Tope){
			/*	NO HABILITADO */
			$navegador.="<input type='button' id='Adelante' name='Adelante' value='Adelante >' onclick='' disabled='disabled'>";
		}else{
			$paginaDespues=$pagina+20;

						$navegador.="<input type='button' id='Adelante' name='Adelante' value='Adelante >' onclick='javascript:FillGrid(".$paginaDespues.");'>";
		}
		
		/************************/

		
		
		
		$datos="<table width='100%' align='center' cellpadding='3' cellspacing='3'>
		<tr><td colspan='4' align='center'>".$navegador."</td></tr>
		<tr class='MYTABLE'><td colspan='4' align='center'><strong>LISTADO DE ESPECIALIDADES/SERVICIOS</strong></td></tr>
		<tr class='MYTABLE'><td width='13%' align='center'><strong>Codigo</strong></td>
		<td width='28%' align='center'><strong>Especialidad/Servicio</strong></td>
		<td width='22%' align='center'><strong>Estado</strong></td>
		</tr>";
		
		while($row=pg_fetch_array($resp)){
			
			if($row["habilitadofarmacia"]=='H'){
				$CuentaEstado='HABILITADO';
			}else{
				$CuentaEstado='DESHABILITADO';
			}
			
			
			if($row["codigo_farmacia"]==NULL or $row["codigo_farmacia"]=='0' or $row["codigo_farmacia"]==''){
				$CodigoFarmacia='0';
			}else{
				$CodigoFarmacia=$row["codigo_farmacia"];
			}
			
			
			$NombreSubEspecialidad=htmlentities($row["subservicio"]);
			
			
			
			$datos.="<tr class='FONDO'><td align='center' style='vertical-align:middle;'><div id='Contenedor".$row["idsubservicio"]."'><div id='Codigo".$row["idsubservicio"]."' style='border:#000000 dashed thin; width:75px;' onmouseover='this.style.background=\"#CCCC66\"' onmouseout='this.style.background=\"#EEEECC\"' onclick='javascript:CodigoMedico(\"Codigo".$row[0]."\",2)' align='center'>".$CodigoFarmacia."</div></div></td><td>".$NombreSubEspecialidad."</td><td align='center' style='vertical-align:middle;'>".$CuentaEstado."</td></tr>";
			
		}
		
		
		
		$datos.="<tr><td colspan='4' align='center'>".$navegador."</td></tr>
		</table>";
		echo $datos;
	break;
	case 2:
	/*	MUESTRA EL IMPUT PARA ACTUALIZACION DE CODIGO	*/
	$IdMedico=$_GET["IdMedico"];
	$IdEmpleado=$_GET["Medico"];
	/*	OBTENCION DE CODIGO ACTUAL	*/
	$CodigoActual=$update->CodigoActualFarmacia($IdEmpleado,$_SESSION["IdEstablecimiento"],$IdModalidad);
	/********************************/
	echo "<input type='text' id='".$IdMedico."' name='".$IdMedico."' value='".$CodigoActual."' onblur='javascript:CodigoMedico(this.id,3)' size='9'/>";
	
	break;
	case 3:
	/*	ACTUALIZA EL CODIGO DE MEDICO	*/
	$IdMedico=$_GET["IdMedico"];//Identificador del objeto
	$IdEmpleado=$_GET["Medico"];//Codigo MEDXXXX
	$CodigoNuevo=strtoupper($_GET["CodigoNuevo"]);
	
	/*	ACTUALIZACION DEL NUEVO CODIGO	*/
	$resp=$update->VerificaCodigo($IdEmpleado,$CodigoNuevo,$_SESSION["IdEstablecimiento"],$IdModalidad);
	if($resp!=NULL or $resp!=''){
		echo 'N~'.$CodigoNuevo;
	}else{
	$update->ActualizarCodigoFarmacia($IdEmpleado,$CodigoNuevo,$_SESSION["IdEstablecimiento"],$IdModalidad);
	
	/************************************/
	
	/*	DESPLEGAR NUEVO CODIGO	*/
	echo "<div id='".$IdMedico."' style='border:#000000 dashed thin; width:75px;' onmouseover='this.style.background=\"#CCCC66\"' onmouseout='this.style.background=\"#EEEECC\"' onclick='javascript:CodigoMedico(\"".$IdMedico."\",2)' align='center'>".$CodigoNuevo."</div></div>";
	}
	break;
	
	case 4:
	/*	BUSQUEDA DE MEDICO*/
	$NombreEmpleado=strtoupper($_GET["NombreEmpleado"]);
	$CodigoFarmacia=strtoupper($_GET["CodigoFarmacia"]);
	
	/*	INFORMACION DE MEDICO	*/
	$resp=$update->BusquedaMedico($CodigoFarmacia,$NombreEmpleado,$_SESSION["IdEstablecimiento"],$IdModalidad);
	/********************************/
	
	/*	MOSTRAR INFORMACION		*/
	
			$datos="<table width='100%' align='center' cellpadding='3' cellspacing='3'>
		<tr class='MYTABLE'><td colspan='4' align='center'><strong>LISTADO DE MEDICOS</strong></td></tr>
		<tr class='MYTABLE'><td width='13%' align='center'><strong>Codigo</strong></td>
		<td width='37%' align='center'><strong>Especialidad/Servicio</strong></td>
		<td width='22%' align='center'><strong>Estado</strong></td>
		</tr>";
		
		while($row=pg_fetch_array($resp)){
			
			if($row["habilitadofarmacia"]=='H'){
				$CuentaEstado='HABILITADO';
			}else{
				$CuentaEstado='DESHABILITADO';
			}
			
			if($row["codigo_farmacia"]==NULL or $row["codigo_farmacia"]=='0' or $row["codigo_farmacia"]==''){
				$CodigoFarmacia='0';
			}else{
				$CodigoFarmacia=$row["codigo_farmacia"];
			}
			
			$datos.="<tr class='FONDO'><td align='center' style='vertical-align:middle;'><div id='Contenedor".$row[0]."'><div id='Codigo".$row[0]."' style='border:#000000 dashed thin; width:75px;' onmouseover='this.style.background=\"#CCCC66\"' onmouseout='this.style.background=\"#EEEECC\"' onclick='javascript:CodigoMedico(\"Codigo".$row[0]."\",2)' align='center'>".$CodigoFarmacia."</div></div></td><td>".htmlentities($row[3])."</td><td align='center' style='vertical-align:middle;'>".$CuentaEstado."</td></tr>";
			
		}
		
		$datos.="</table>";
	
	echo $datos;
	
	break;
	case 5:
	$IdEmpleado=$_GET["Medico"];
	$Combo=$_GET["Combo"];
	
	/*	 desplegar combo de especialidades 	*/
	echo $update->ComboSubEspecialidades($Combo,$IdEmpleado);
	
	break;
	
	case 6:
	/*	ACTUALIZA EL CODIGO DE MEDICO	*/
	$Combo=$_GET["Combo"];//Identificador del objeto
	$IdEmpleado=$_GET["Medico"];//Codigo MEDXXXX
	$NuevaEspecialidad=$_GET["NuevaEspecialidad"];
	
	if($NuevaEspecialidad!=0){
		/*	ACTUALIZACION DEL NUEVO CODIGO	*/
		$resp=$update->VerificaUbicacionMedico($IdEmpleado,$NuevaEspecialidad);
		if($resp !='4' and ($resp!=NULL or $resp!='')){
		/*	AVISO QUE NO SE PUEDE RELIZAR EL CAMBIO  */
	
			$dato='N~';
			
			$NombreSubEspecialidad=$update->MedicoSubEspecialidad($IdEmpleado);
			if($NombreSubEspecialidad==NULL){
				$NombreSubEspecialidad="NO TIENE ESPECIALIDAD ASIGNADA";
			}
				
				$dato.= "<div id='".$Combo."' style='border:#000000 dashed thin; width:260px;' onmouseover='this.style.background=\"#33CCFF\"' onmouseout='this.style.background=\"#EEEECC\"' onclick='javascript:EspecialidadMedico(\"".$Combo."\",5)' align='center'>".$NombreSubEspecialidad."</div></div>";
	
			echo $dato;
		}else{
		/*	ACTUALIZA LA SUBESPECIALIDAD EN DADO CASO NO SEA UN MEDICO DE CONSULTA EXTERNA	*/
		$update->ActualizarSubEspecialidad($IdEmpleado,$NuevaEspecialidad);
		$NombreSubEspecialidad=$update->SubEspecialidad($NuevaEspecialidad);
		/************************************/
		
		/*	DESPLEGAR NUEVO CODIGO	*/
		echo "<div id='".$Combo."' style='border:#000000 dashed thin; width:260px;' onmouseover='this.style.background=\"#33CCFF\"' onmouseout='this.style.background=\"#EEEECC\"' onclick='javascript:EspecialidadMedico(\"".$Combo."\",5)' align='center'>".$NombreSubEspecialidad."</div></div>";
		}
	
	}else{
	
			$dato='C~';
			
			$NombreSubEspecialidad=$update->MedicoSubEspecialidad($IdEmpleado);
			if($NombreSubEspecialidad==NULL){
				$NombreSubEspecialidad="NO TIENE ESPECIALIDAD ASIGNADA";
			}
				
				$dato.= "<div id='".$Combo."' style='border:#000000 dashed thin; width:260px;' onmouseover='this.style.background=\"#33CCFF\"' onmouseout='this.style.background=\"#EEEECC\"' onclick='javascript:EspecialidadMedico(\"".$Combo."\",5)' align='center'>".$NombreSubEspecialidad."</div></div>";
	
				
			echo $dato;
	}
	break;
	
	case 7:
		/*	MUESTRA COMBO DE ESTADO	*/
		$IdEmpleado=$_GET["Medico"];
		$ComboEstado=$_GET["Estado"];
		
		$EstadoActual=$update->VerificaEstadoMedico($IdEmpleado);
		if($EstadoActual=='H'){
			$combo="<select id='".$ComboEstado."' name='".$ComboEstado."' onblur='EstadoMedico(\"".$ComboEstado."\",8);'>
			<option value='H'>HABILITADO</option>
			<option value='I'>DESHABILITADO</option>
			</select>";
		
		}else{
			$combo="<select id='".$ComboEstado."' name='".$ComboEstado."' onblur='EstadoMedico(\"".$ComboEstado."\",8);'>
			<option value='I'>DESHABILITADO</option>
			<option value='H'>HABILITADO</option>
			</select>";
		
		}
		echo $combo;
		
	break;
	case 8:
		/*	ACTUALIZA ESTADO DE MEDICO	*/
		$ComboEstado=$_GET["Estado"];
		$NuevoEstado=$_GET["NuevoEstado"];
		$IdEmpleado=$_GET["Medico"];
		
		$update->ActualizaEstadoCuenta($IdEmpleado,$NuevoEstado);
		
		$CuentaEstado=$update->VerificaEstadoMedico($IdEmpleado);
			if($CuentaEstado=='H'){
				$CuentaEstado='HABILITADO';
			}else{
				$CuentaEstado='DESHABILITADO';
			}
		
		$dato="<div id='".$ComboEstado."' style='border:#000000 dashed thin; width:200px;' onmouseover='this.style.background=\"#00FF66\"' onmouseout='this.style.background=\"#EEEECC\"' onclick='javascript:EstadoMedico(\"".$ComboEstado."\",7)' align='center'>".$CuentaEstado."</div>";
		
		echo $dato;
		
		
	break;
	
	
	
}//switch
conexion::desconectar();
?>
