<?php
session_start();
include 'conn.php';
$name=$_GET['n'];

if(!$conn) {
    die('Connessione fallita !<br />');
} else {

	  $query_g="SELECT data, cod, ST_AsGeoJson(ST_SetSRID( ST_Point( lon, lat), 4326)) as geo  
	  FROM demo_rfi.punti_aree WHERE name='".$name."';";
	
	
	// GeoJson Postgis: {"type":"Point","coordinates":[8.90092674245687,44.4828501691802]}
	
	//echo $query_g;
	$i=0;
	echo '{"type":"FeatureCollection","metadata":{"generated":"GTER",
	"url":"https://demo.gter.it/demo_rfi/geojson.php","title":"Punti aree"},"features":[';
	$result_g = pg_query($conn, $query_g);
	while($r_g = pg_fetch_assoc($result_g)) {
	if ($i==0){ 
		echo '{"type": "Feature","properties": {"cod":"'.$r_g["cod"].'", "data":"'.$r_g["data"].'"}, ';
		echo '"geometry":';
		echo $r_g["geo"].'}';
	} else {
		//echo ",". $r_g["geo"];
		echo ',{"type": "Feature","properties": {"cod":"'.$r_g["cod"].'", "data":"'.$r_g["data"].'"}, ';
		echo '"geometry":';
		echo $r_g["geo"].'}';
		
	}
	$i=$i+1;
	}
	echo ']};';
	pg_close($conn);
}



?>
