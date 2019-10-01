<?php 
//echo "PHP funziona<br>";

$conn = pg_connect("host=127.0.0.1 port=5432 dbname=qgis_gter user=gter password=qgis2016");

if (!$conn) {
        die('Could not connect to DB, please contact the administrator.');
}
else {
        //echo ("Connected to local DB");
}


?>
