<?php 

//$ip=$_GET['ip'];
if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$nome=$_GET['n'];

print('Gentile '.$nome. ' il tuo Ip è '.$ip.'<br>');

include 'conn.php';
$page = $_SERVER['PHP_SELF'];
$sec = "10";
?>
<html>
    <head>
    <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page.'?n='.$nome?>'">
    </head>
    <body>
<?php
if(!$conn) {
    die('Connessione fallita !<br />');
} else {
	$query="SELECT ip FROM demo_rfi.ip_list WHERE name='".$nome."';";

	$result = pg_query($conn, $query);
	$check=0;
	
	while($r = pg_fetch_assoc($result)) {
		$check=1;
	}
	
	
	if ($check==1){
		$query="UPDATE demo_rfi.ip_list set ip='".$ip."' WHERE name='".$nome."';";	
	} else {
		$query="INSERT INTO demo_rfi.ip_list (ip,name) VALUES ('".$ip."' ,'".$nome."');";
	}
	echo $query;
	$result = pg_query($conn, $query);
	pg_close($conn);
}



?>


</body>
</html>