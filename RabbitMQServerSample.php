<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function login($user,$pass){
	//TODO validate user credentials
	return true;
}

function register($user,$pass){
//write user to usernames.txt, write passwords to passwords.txt


$userfile = fopen("usernames.txt","w"); $passfile = fopen("passwords.txt","w");
if(strpos(file_get_contents($userfile), $user) !== false) return false;

fwrite($userfile, $user);
fwrite($passfile, $pass);
fclose($userfile);
fclose($passfile);
return true;

}
function request_processor($req){
	echo "Received Request".PHP_EOL;
	echo "<pre>" . var_dump($req) . "</pre>";
	if(!isset($req['type'])){
		return "Error: unsupported message type";
	}
	//Handle message type
	$type = $req['type'];
	switch($type){
		case "login":
			return login($req['username'], $req['password']);
		case "validate_session":
			return validate($req['session_id']);
		case "echo":
			return array("return_code"=>'0', "message"=>"Echo: " .$req["message"]);
		case "register":
			if(register($req['username'], $req['password']) {
			return array("return_code"=>'0', "message"=>"Server received userdata and stored it");
			}
			else
			{
			return array("return_code"=>'0', "message"=>"Server received userdata but either username already exists or userdata invalid");
			}
	}
	return array("return_code" => '0',
		"message" => "Server received request and processed it");
}

$server = new rabbitMQServer("testRabbitMQ.ini", "sampleServer");

echo "Rabbit MQ Server Start" . PHP_EOL;
$server->process_requests('request_processor');
echo "Rabbit MQ Server Stop" . PHP_EOL;
exit();
?>
