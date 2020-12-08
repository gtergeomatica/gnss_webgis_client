<?php
session_start();
include 'conn.php';
include 'interval.php';
$name=$_GET['n'];



if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	//$idcivico=$_GET["id"];
	$query="select l.name, a.ip, a.data, ST_AsGeoJson(st_makepoint(b.lon, b.lat, b.quota)) as geo,
	b.quality
		from (select ip , max(data) as data from demo_rfi.posizioni group by ip) a 
		join demo_rfi.posizioni b on a.ip= b.ip and a.data = b.data
		left join demo_rfi.ip_list l ON a.ip=l.ip
		where ";
    if($name!=''){
      $query= $query. " l.name = '".$name."' and ";
    }
    $query= $query. " b.data > (SELECT current_timestamp at time zone 'UTC'- (".$interval." ||' minutes')::interval)
		and b.data < (SELECT current_timestamp at time zone 'UTC'+ (10*".$interval." ||' minutes')::interval);"; // da metter 0.2 (2 decimi di minuto)
    
    //echo $query."<br>";

	//$query_g="SELECT id, ST_AsGeoJson(geom) as geo, rischio, criticita, 
//descrizione, localizzazione, note FROM segnalazioni.v_segnalazioni_lista WHERE id_lavorazione is null;";

	$result = pg_query($conn, $query);
	$i=0;
	echo '{"type":"FeatureCollection","metadata":{"generated":"GTER",
	"url":"https://demo.gter.it/demo_rfi/geojson.php","title":"Posizioni device"},"features":[';
	#echo $query;
	#exit;
	//$rows = array();
	while($r_g = pg_fetch_assoc($result)) {
		if ($i==0){ 
			echo '{"type":"Feature","properties":{"IP":"'.$r_g["ip"].'","data":"';
			echo $r_g["data"].'","name":"'.$r_g["name"].'","quality":"'.$r_g["quality"].'"},"geometry":';
			echo $r_g["geo"].',"id":"'.$i.'"}';
		} else {
			//echo ",". $r_g["geo"];
			echo ',{"type":"Feature","properties":{"IP":"'.$r_g["ip"].'","data":"';
			echo $r_g["data"].'","name":"'.$r_g["name"].'","quality":"'.$r_g["quality"].'"},"geometry":';
			echo $r_g["geo"].',"id":"'.$i.'"}';
			
		}
		$i=$i+1;
    	//$rows[] = $r;
    	//$rows[] = $rows[]. "<a href='puntimodifica.php?id=" . $r["NAME"] . "'>edit <img src='../../famfamfam_silk_icons_v013/icons/database_edit.png' width='16' height='16' alt='' /> </a>";
	}
	//echo '],"bbox":[8.9280037859999997,44.4086776520000015,0,8.9295140830000008,44.4797064999999989,0]}';
	echo ']}';
	pg_close($conn);
	
	
	



			// GeoJson Postgis: {"type":"Point","coordinates":[8.90092674245687,44.4828501691802]}
			
			
	
	
	
	
	/*$jsonData =json_encode($rows);
	$original_data = json_decode($jsonData, true);
	$features = array();
	foreach($original_data as $key => $value) {
	    $features[] = array(
	        'type' => 'Feature',
	        'properties' => array('time' => $value['data']),
	        'geometry' => array(
	             'type' => 'Point', 
	             'coordinates' => array(
		                  $value['lon'],
		                  $value['lat'], 
	                  1
	             ),
	         ),
	    );
	}
	$new_data = array(
	    'type' => 'FeatureCollection',
	    'features' => $features,
	);
	
	$final_data = json_encode($new_data, JSON_PRETTY_PRINT);
	print_r($final_data);*/
	
	
	
	
	
	
	
	#echo $rows ;
	/*if (empty($rows)==FALSE){
		//print $rows;
		print json_encode(array_values(pg_fetch_all($result)));
	} else {
		echo "[{\"NOTE\":'No data'}]";
	}*/

}



?>
