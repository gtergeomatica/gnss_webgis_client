<?php
session_start();
include 'conn.php';
include 'interval.php';
$name=$_GET['n'];




if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	//$idcivico=$_GET["id"];
	$query="DELETE FROM demo_rfi.punti_aree
	WHERE data= (select max(data) from demo_rfi.punti_aree where name='".$name."') and name='".$name."';";
    echo $query."<br>";

	//$query_g="SELECT id, ST_AsGeoJson(geom) as geo, rischio, criticita, 
//descrizione, localizzazione, note FROM segnalazioni.v_segnalazioni_lista WHERE id_lavorazione is null;";

	$result = pg_query($conn, $query);
	
	//echo $query;

	pg_close($conn);

}



?>
