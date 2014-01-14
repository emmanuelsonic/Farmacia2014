<?php
include($path.'../Clases/class.php');
class RecetasAbiertas{
    function CierreMes($Periodo,$IdEstablecimiento,$IdModalidad){
        $query="select MesCierre from farm_cierre 
                where MesCierre='".$Periodo."'
                and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
        $resp=pg_fetch_array(pg_query($query));
        return($resp[0]);
    }
    
    function ListadoRecetasAbiertas($Periodo,$IdEstablecimiento,$IdModalidad){
        $query="SELECT CorrelativoAnual,username,Area,Fecha, fr.Id
                FROM farm_recetas fr
                INNER JOIN fos_user_user fuu
                ON fuu.Id=fr.IdPersonalIntro
                INNER JOIN mnt_areafarmacia maf
                ON maf.Id=fr.IdArea
                WHERE LEFT(to_char(Fecha,'YYYY-MM-DD'),7)='".$Periodo."'
                AND IdEstado <> 'E'
                AND fr.IdEstablecimiento=$IdEstablecimiento
                AND fr.IdModalidad=$IdModalidad";
            $resp=pg_query($query);
            return($resp);
    }
    
    function FinalizarReceta($IdReceta,$IdEstablecimiento,$IdModalidad){
        $query="update farm_recetas set IdEstado='E' 
                where IdReceta=".$IdReceta."
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
        pg_query($query);
    }
}//Clase Reporte Farmacias
?>