<?php include('../../Clases/class.php');

class Desabastecimiento{
   function ValidacionPeriodo($IdMedicina,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad){
	$SQL="select *
		from farm_periododesabastecido
		where ('$FechaInicio' between FechaInicio and FechaFin or '$FechaFin' between FechaInicio and FechaFin)
		and IdMedicina=".$IdMedicina." and
		IdEstablecimiento=".$IdEstablecimiento." 
                and IdModalidad=$IdModalidad";
	$resp=pg_query($SQL);	
	return($resp);
   }

   function InsatisfechasPromedio($IdMedicina,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad){
	$SQL="select count(fmr.Id) as TotalRecetas, 
                ceil(extract(days from('$FechaInicio'::date-('$FechaInicio'::date-'1 month'::interval)))::int) as DiasMeses, 
                count(fmr.Id)/
                ceil(extract(days from('$FechaInicio'-('$FechaInicio'::date -'1 month'::interval)))::int) as PromedioDiaRecetas, 
                count(fmr.Id)/
                ceil(extract(days from('$FechaInicio'-('$FechaInicio'::date-'1 month'::interval)))::int* 
                extract(days from('$FechaFin'-('$FechaInicio'::date+'0 month'::interval)))) as PromInsatisfechas 

                from farm_recetas fr 
                inner join farm_medicinarecetada fmr on fr.Id=fmr.IdReceta 
                inner join sec_historial_clinico shc on shc.Id=fr.IdHistorialClinico 
                where (fr.IdEstado='E' or fr.IdEstado='ER') and (fmr.IdEstado='S' or fmr.IdEstado='') and 
                IdMedicina = 2 and shc.IdEstablecimiento=$IdEstablecimiento and 
                fr.IdModalidad=$IdModalidad
                and fr.Fecha between ('$FechaInicio'::date -'1 month'::interval) and '$FechaFin'";

	$resp=pg_query($SQL);
	return($resp);
   }

   function IngresarDatosInsatisfecha($IdMedicina,$FechaInicio,$FechaFin,$InsatisfechasTotal,$PromedioDiaRecetas,$IdEstablecimiento,$IdModalidad){
	$SQL="insert into farm_periododesabastecido (IdMedicina,FechaInicio,FechaFin,PromedioRecetas,PromedioDiarias,IdEstablecimiento, IdModalidad) 
                                              values('$IdMedicina','$FechaInicio','$FechaFin','$InsatisfechasTotal','$PromedioDiaRecetas','$IdEstablecimiento',$IdModalidad)";
	pg_query($SQL);
   }

}

?>