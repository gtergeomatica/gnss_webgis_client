<?php
session_start();
include 'conn.php';
include 'interval.php';
$name=$_GET['n'];

$cod=$_GET['p'];


//echo "Bisogna attendere 3 secondi<br>";

//sleep(10);

//echo "Ok sono pronto <br>";

if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	//$idcivico=$_GET["id"];
	$query="select
	l.name, 
(select avg(lon.lon)  from (select lon, lat from demo_rfi.posizioni where ip=a.ip order by data desc limit 10) lon) as lon,
(select avg(lat.lat)  from (select lon, lat from demo_rfi.posizioni where ip=a.ip order by data desc limit 10) lat) as lat
		from (select ip , max(data) as data from demo_rfi.posizioni group by ip) a 
		join demo_rfi.posizioni b on a.ip= b.ip and a.data = b.data
		left join demo_rfi.ip_list l ON a.ip=l.ip
		where l.name='".$name."' ";
		$query= $query . "and b.data > (SELECT current_timestamp at time zone 'UTC'- (".$interval." ||' minutes')::interval) and b.data < (SELECT current_timestamp at time zone 'UTC'+ (10*".$interval." ||' minutes')::interval);"; // da metter 0.2 (2 decimi di minuto)
    
    //echo $query."<br>";

	//$query_g="SELECT id, ST_AsGeoJson(geom) as geo, rischio, criticita, 
//descrizione, localizzazione, note FROM segnalazioni.v_segnalazioni_lista WHERE id_lavorazione is null;";

	$result = pg_query($conn, $query);

	while($r_g = pg_fetch_assoc($result)) {
    	$lat = $r_g['lat'];
		//echo "<br>";
		//echo $lat;
		//echo "<br>";
		$lon = $r_g['lon'];
    	//$rows[] = $rows[]. "<a href='puntimodifica.php?id=" . $r["NAME"] . "'>edit <img src='../../famfamfam_silk_icons_v013/icons/database_edit.png' width='16' height='16' alt='' /> </a>";
	}
	//echo ']}';
	
	
	
	$query="INSERT INTO demo_rfi.punti_aree (name, lat, lon, cod) 
	VALUES ('".$name."', ".$lat.", ".$lon.",'".$cod."');";
	
	echo $query;
	echo "<br>";
	
	$result = pg_query($conn, $query);


	$check=0;
	$query="SELECT cod FROM demo_rfi.punti_aree WHERE name='".$name."' and cod='".$cod."';";
	$result = pg_query($conn, $query);
	while($r = pg_fetch_assoc($result)) {
		$check=1;
	}
	
	if($check==1) {
		echo "OK";
	} else {
		echo "ERROR";
	}


	pg_close($conn);

}



?>
