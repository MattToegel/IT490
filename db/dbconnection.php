<?php
function getDB(){
	global $db;
	if(!isset($db)){
		//DO NOT COMMIT PRIVATE CREDENTIALS TO A REPOSITORY EVER
		$conn_string = "";//TODO should pull from config or env variables
		$db = new PDO($conn_string, $dbusername, $dbpassword);
	}
	return $db;
}
?>
