<?php session_start();
$IdArea=$_SESSION["IdArea"];

require('../../Clases/class.php');
conexion::conectar();
require('include/funciones.php');
require('include/pagination.class.php');
require('include/MedicosClass.php');
$Classquery=new Classquery;
//****obtencion de fechas validas de recetas (3 dias habiles)
$IdEstablecimiento=$_SESSION["IdEstablecimiento"];
$IdModalidad=$_SESSION["IdModalidad"];
//***
$items = 15;
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
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,$q,$IdEstablecimiento,$IdModalidad);
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,$q,$IdEstablecimiento,$IdModalidad);
}else{
$Bandera=0;
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,"",$IdEstablecimiento,$IdModalidad);
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,"",$IdEstablecimiento,$IdModalidad);
}
$query = pg_query($sqlStr.$limit);
$aux = Pg_Fetch_Assoc(pg_query($sqlStrAux));
?><br>

<?php
		if($aux['total'] and isset($busqueda)){
				//echo "{$aux['total']} Resultado".($aux['total']>1?'s':'')." que coinciden con tu b&uacute;squeda \"<strong>$busqueda</strong>\".";
echo "Resultados que coinciden con tu b&uacute;squeda \"<strong>$busqueda</strong>\".";
			}elseif($aux['total'] and !isset($q)){
				//echo "Total de registros: {$aux['total']}";
			}elseif(!$aux['total'] and isset($q)){
				echo"No hay registros que coincidan con tu b&uacute;squeda \"<strong>$busqueda</strong>\"";
			}
	?><br />

	<?php 
		if($aux['total']>0){
			$p = new pagination;
			$p->Items($aux['total']);
			$p->limit($items);
			if(isset($q))
					$p->target("buscador_medico.php?q=".$q);
				else
					$p->target("buscador_medico.php");
			$p->currentPage($page);
			$p->show();
			echo "\t<table class=\"registros\">\n";
			echo "<tr class=\"titulos\"><td>CODIGO</td><td>NOMBRE DE MEDICO</td></tr>";
$r=0;
 while($row = pg_fetch_assoc($query)){
	if(isset($page)){
echo "\t\t<tr class=\"row$r\"><td align='center'>".$row["codigo_farmacia"]."</td>
<td align=\"left\"><a href=\"#\" onclick=\"javascript:UbicarMedico(0,'0','".$row["id"]."','".htmlentities($row["nombreempleado"])."')\">".htmlentities($row["nombreempleado"])."</a></td>
</tr>";
	}//IF
		 
          if($r%2==0)++$r;else--$r;
        } //whle
			echo "\t</table>\n";
			$p->show();
}
  ?>