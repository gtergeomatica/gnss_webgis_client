<?php

include 'conn.php';
include 'token_telegram.php';
include 'send_message.php';


if(!$conn) {
	die('Connessione fallita !<br />');
} else {
	$query="UPDATE demo_rfi.pericolo SET \"end\"='now()' where \"end\" is null;";
	$result = pg_query($conn, $query);
}





for ($i = 0; $i < count($chatid); ++$i) {
	sendMessage($chatid[$i],"Fine situazione pericolo. Puoi uscire dall'area di sicurezza.",$token);
	//echo $chatid[$i];
}


header("location: ./index.php");

?>