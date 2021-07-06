<?php
function register($username, $password){
	//from dbconnection.php
	$stmt = getDB()->prepare("gotta do this one yourself");
	//don't forget the password_hash($password, PASSWORD_BCRYPT)
	$result = $stmt->execute([/*fill in the proper mappings*/]);
	//TODO do proper checking, maybe user doesn't exist
	if($result){
		return array("status"=>200, "message"=>"Did we register successfully?");
	}
	else{
		//must return a proper message so that the app can parse it
		//and display a user friendly message to the user
		return array("status"=>400, "message"=>"do something");
	}
}
?>
