function xmlhttp(){
		var xmlhttp;
		try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
		catch(e){
			try{xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
			catch(e){
				try{xmlhttp = new XMLHttpRequest();}
				catch(e){
					xmlhttp = false;
				}
			}
		}
		if (!xmlhttp) 
				return null;
			else
				return xmlhttp;
}//xmlhttp

//******FILTROS DE COMBOS
function cargaContenido(valor,ID){


switch(ID){
   case "farmacia":
	var A = document.getElementById('ComboAreas');
	var Objetivo = "mnt_areafarmacia";
   break;

   case "IdTerapeutico":
	var A = document.getElementById('ComboMedicinas');
	var Objetivo = "farm_catalogoproductos";
   break;

}

var ajax = xmlhttp();
		
	ajax.onreadystatechange=function(){
	    if(ajax.readyState==1){
		A.innerHTML = '<select name="none" id="none" disabled="disabled"><option value="0">[CARGANDO ...]</option></select>';
	    }

	    if(ajax.readyState==4){
		A.innerHTML = ajax.responseText;
	    }
	}

ajax.open("GET","proceso_farmacias.php?valor="+valor+"&Combo="+Objetivo,true);
		ajax.send(null);
		return false;

}
//*******************




function Reportes(){
	var IdFarmacia=document.getElementById('farmacia').value;
	var IdArea=document.getElementById('area').value;
	var IdTerapeutico = document.getElementById('IdTerapeutico').value;
	var IdMedicina = document.getElementById('IdMedicina').value;

	var A = document.getElementById('Reporte');

		//var B = document.getElementById('loading');
		var ajax = xmlhttp();
		
		ajax.onreadystatechange=function(){
				if(ajax.readyState==1){
						A.innerHTML = "<img src='../imagenes/barra.gif' alt='Loading...' />";
					}
				if(ajax.readyState==4){
					    if(ajax.responseText=="ERROR_SESSION"){alert('La sesion ha caducado! \n Inicie sesion nuevamente!'); window.location='../signIn.php';}
						A.innerHTML = ajax.responseText;
						//B.innerHTML = "";
						
					}
			}

ajax.open("GET","Reporte_Existencias.php?IdFarmacia="+IdFarmacia+"&IdArea="+IdArea+"&IdTerapeutico="+IdTerapeutico+"&IdMedicina="+IdMedicina,true);
		ajax.send(null);
		return false;
		
}//Reporte