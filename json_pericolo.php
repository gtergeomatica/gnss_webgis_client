<?php
session_start();
include 'conn.php';
include 'interval.php';

if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	//$idcivico=$_GET["id"];
	$query="select a.ip, a.data, b.quality, st_intersects(st_makepoint(b.lon,b.lat),c.geom) as pericolo
from (select ip , max(data) as data from demo_rfi.posizioni group by ip) a
join demo_rfi.posizioni b on a.ip = b.ip and a.data = b.data
JOIN demo_rfi.aree c on st_intersects(st_makepoint(b.lon,b.lat),c.geom)
where c.tipo ilike 'danger' and b.data > (SELECT current_timestamp at time zone 'UTC'- (".$interval." ||' minutes')::interval);"; // da metter 0.2 (2 decimi di minuto)
    
    //echo $query;

	//$query_g="SELECT id, ST_AsGeoJson(geom) as geo, rischio, criticita, 
//descrizione, localizzazione, note FROM segnalazioni.v_segnalazioni_lista WHERE id_lavorazione is null;";

	$result = pg_query($conn, $query);

	$rows = array();
	while($r_g = pg_fetch_assoc($result)) {
    	$rows[] = $r_g;
    	//$rows[] = $rows[]. "<a href='puntimodifica.php?id=" . $r["NAME"] . "'>edit <img src='../../famfamfam_silk_icons_v013/icons/database_edit.png' width='16' height='16' alt='' /> </a>";
	}
	//echo ']}';
	pg_close($conn);
	
	
	
	if (empty($rows)==FALSE){
		//print $rows;
		print json_encode(array_values(pg_fetch_all($result)));
	} else {
		echo "[{\"WARNING\":'In questo momento non ci sono dati disponibili'}]";
	}

}



?>
