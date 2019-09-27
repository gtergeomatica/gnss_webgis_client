<?php
session_start();
include 'conn.php';
include 'interval.php';

if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	//$idcivico=$_GET["id"];
	$query="select count(a.ip)
from (select ip , max(data) as data from demo_rfi.posizioni group by ip) a
join demo_rfi.posizioni b on a.ip = b.ip and a.data = b.data
JOIN demo_rfi.aree c on st_intersects(st_setsrid(st_makepoint(b.lon,b.lat),4326),c.geom)
where c.tipo ilike 'danger' and b.data > (SELECT current_timestamp at time zone 'UTC'- (".$interval." ||' minutes')::interval);"; // da metter 0.2 (2 decimi di minuto)
    //echo $query;
    $result = pg_query($conn, $query);

	
	while($r = pg_fetch_assoc($result)) {
    	$count_danger = $r['count'];
	}
	
	$query="select count(a.ip)
from (select ip , max(data) as data from demo_rfi.posizioni group by ip) a
join demo_rfi.posizioni b on a.ip = b.ip and a.data = b.data
JOIN demo_rfi.aree c on st_intersects(st_setsrid(st_makepoint(b.lon,b.lat),4326),c.geom)
where c.tipo ilike 'safety' and b.data > (SELECT current_timestamp at time zone 'UTC'- (".$interval." ||' minutes')::interval);"; // da metter 0.2 (2 decimi di minuto)
    
    $result = pg_query($conn, $query);

	
	while($r = pg_fetch_assoc($result)) {
    	$count_safety = $r['count'];
	}
	
	$query="select count(a.ip)
from (select ip , max(data) as data from demo_rfi.posizioni group by ip) a
join demo_rfi.posizioni b on a.ip = b.ip and a.data = b.data
where b.data > (SELECT current_timestamp at time zone 'UTC'- (".$interval." ||' minutes')::interval);"; // da metter 0.2 (2 decimi di minuto)
    
    $result = pg_query($conn, $query);

	
	while($r = pg_fetch_assoc($result)) {
    	$count_ip_connessi = $r['count'];
	}
    
}   
 ?>


<!doctype html>
<html>
<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <title>DEMO RFI</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.0.6/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.0.6/dist/MarkerCluster.Default.css" />
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.15.4/dist/bootstrap-table.min.css">	
	 
<style>
        html, body, #map {
            position; absolute;
			width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }
        </style>
</head>
<body>

<div class="container">
<div class="row">
<h1>Demo visualizzatore posizioni e allarmi</h1>

</div>
  <div class="row">
    <div class="col-6">
	
	<!--table id="table" 
	data-toggle="table"
	autoRefresh="true"
	autoRefreshStatus="true"
	data-show-refresh="true" 
	data-auto-refresh="true" 
	data-pagination="true" 
	data-url="./geojson2.php" 
	autoRefreshInterval="3"
	-->
	<table id="table" 
	data-toggle="table"
	data-show-refresh="true" 
	data-url="./geojson2.php" 
	>
	
	
  <thead>
    <tr>
    	<th data-field="name" data-sortable="true">Device</th>
      <th data-field="ip" data-sortable="true">IP</th>
      <th data-field="data" data-sortable="true">Data e ora UTC</th>
      <th data-field="quality" data-sortable="true">Quality</th>
    </tr>
  </thead>
</table>

<script>
  function mounted() {
    $('#table').bootstrapTable()
  }
</script>

	<br>
	
	<!--table id="table2" 
	data-toggle="table"
	autoRefresh="true"
	autoRefreshStatus="true"
	data-show-refresh="true" 
	data-auto-refresh="true" 
	data-pagination="true" 
	autoRefreshInterval="3"
	data-url="./contatori.php" 
	-->
	
	<table id="table2" 
	data-toggle="table"
	data-show-refresh="true" 
	data-url="./contatori.php" 
	>
		
	
  <thead>
    <tr>
    	<th data-field="count_ip_connessi" data-sortable="true">Dispositivi<br>connessi</th>
      <th data-field="count_safety" data-sortable="true">Dispositivi<br>in aree sicurezza</th>
      <th data-field="count_danger" data-sortable="true">Dispositivi<br>in aree pericolo</th>
    </tr>
  </thead>
</table>

<script>
  function mounted() {
    $('#table2').bootstrapTable()
  }
</script>
		
  </div>
	
	
    <div class="col-6">
	<div id="map"></div>
	</div>
	
	
  </div>
</div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.15.4/dist/bootstrap-table.min.js"></script>
  <script src="./bootstrap-table/dist/extensions/auto-refresh/bootstrap-table-auto-refresh.js"></script>


<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet-src.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.0.6/dist/leaflet.markercluster-src.js"></script>
<script src="https://unpkg.com/leaflet.featuregroup.subgroup"></script>
<script src="./leaflet-realtime/dist/leaflet-realtime.js"></script>
<script src="./index.js"></script>



<script>
    
    (function(){
    function refreshTable() {$('#table').bootstrapTable('refresh', {silent: true});}
    setInterval(refreshTable, 3000);
})();


    (function(){
    function refreshTable2() {$('#table2').bootstrapTable('refresh', {silent: true});}
    setInterval(refreshTable2, 3000);
})();
    </script>
</body>
</html>