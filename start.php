<?php

include 'conn.php';
include 'token_telegram.php';
include 'send_message.php';


if(!$conn) {
	die('Connessione fallita !<br />');
} else {
	$query="INSERT INTO demo_rfi.pericolo(start) VALUES ('now()');";
	$result = pg_query($conn, $query);
}


for ($i = 0; $i < count($chatid); ++$i) {
	sendMessage($chatid[$i],"\xE2\x9A\xA0 Inizio situazione pericolo. \xE2\x9A\xA0
 \xE2\x80\xBC	Raggiungi area di sicurezza",$token);
	echo $chatid[$i];
}


header("location: ./index.php");

?>