<?php
session_start();
include 'conn.php';


if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	//$idcivico=$_GET["id"];
	$query="select fid, tipo, ST_AsGeoJson(geom) as geo
		from demo_rfi.aree 
		;"; // da metter 0.2 (2 decimi di minuto)
    
    //echo $query;

	//$query_g="SELECT id, ST_AsGeoJson(geom) as geo, rischio, criticita, 
//descrizione, localizzazione, note FROM segnalazioni.v_segnalazioni_lista WHERE id_lavorazione is null;";

	$result = pg_query($conn, $query);
	$i=0;
	echo '{"type":"FeatureCollection","metadata":{"generated":"GTER",
	"url":"https://demo.gter.it/demo_rfi/geojson.php","title":"Aree a rischio"},"features":[';
	#echo $query;
	#exit;
	//$rows = array();
	while($r_g = pg_fetch_assoc($result)) {
		if ($i==0){ 
			echo '{"type":"Feature","properties":{"fid":"'.$r_g["fid"].'",';
			echo '"tipo":"'.$r_g["tipo"].'"},"geometry":';
			echo $r_g["geo"].',"id":"'.$i.'"}';
		} else {
			//echo ",". $r_g["geo"];
			echo ',{"type":"Feature","properties":{"fid":"'.$r_g["fid"].'",';
			echo '"tipo":"'.$r_g["tipo"].'"},"geometry":';
			echo $r_g["geo"].',"id":"'.$i.'"}';
			
		}
		$i=$i+1;
    	//$rows[] = $r;
    	//$rows[] = $rows[]. "<a href='puntimodifica.php?id=" . $r["NAME"] . "'>edit <img src='../../famfamfam_silk_icons_v013/icons/database_edit.png' width='16' height='16' alt='' /> </a>";
	}
	//echo '],"bbox":[8.9280037859999997,44.4086776520000015,0,8.9295140830000008,44.4797064999999989,0]}';
	echo ']}';
	pg_close($conn);
}



?>
