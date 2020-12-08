<?php
session_start();
include 'conn.php';
include 'interval.php';
$name=$_GET['n'];
$tipo=$_GET['t'];



if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	//$idcivico=$_GET["id"];
	$query="SELECT lat, lon FROM demo_rfi.punti_aree
	WHERE name='".$name."';";
    echo $query."<br>";

	//$query_g="SELECT id, ST_AsGeoJson(geom) as geo, rischio, criticita, 
//descrizione, localizzazione, note FROM segnalazioni.v_segnalazioni_lista WHERE id_lavorazione is null;";

	$result = pg_query($conn, $query);
	
	
	//SELECT ST_MakePolygon( ST_GeomFromText('LINESTRING(75 29,77 29,77 29, 75 29)'));
	$query2="INSERT INTO demo_rfi.aree (geom, tipo) VALUES ( ST_Polygon( ST_GeomFromText('LINESTRING(";
	$i=0;
	while($r_g = pg_fetch_assoc($result)) {
		if ($i == 0){
			$lat1 = $r_g['lat'];
			$lon1 = $r_g['lon'];
			
		} 
		$query2= $query2. " ". $r_g['lon']." " .$r_g['lat'].", ";
		$i=$i+1;
		echo $i;
	}
	
	if ($i<2){
		echo "Inserire almeno 3 punti";
		exit;
	} else {
		$query2= $query2 .$lon1." " .$lat1.")'),4326), '".$tipo."');";
		echo $query2;
		$result = pg_query($conn, $query2);
		pg_close($conn);
	}
}



?>
