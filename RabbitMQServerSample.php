<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function login($user,$pass){
	//TODO validate user credentials
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
		case "get_data"://assume I'm DB
			$stmt = $db->prepare("SELECT * from TABLE where query = :query");
			$n = $stmt->execute(array(":query"=>$req['query']));
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($data){//TODO manually resync with API
				return $data;
			}
			require_once("function.php");
			$data = Client::get_news($req['query']);
			//process data to import into db
			$d = json_decode($data, true);
			$stmt = $db->prepare("INSERT INTO TABLE (d1,d2,d3) values(:d1, :d2, :d3)");
			$params = array(
				":d1" => $d['data1'],
				":d2" => $d['data2'],
				":d3" => $d['data3']
			);
			$stmt->execute($params);
			//voila

			return $data;
		case "echo":
			return array("return_code"=>'0', "message"=>"Echo: " .$req["message"]);
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
