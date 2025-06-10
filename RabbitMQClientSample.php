<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new RabbitMQClient('testRabbitMQ.ini', 'testServer');
if(isset($argv[1])){
	if(isset($argv[3])){
		if(strcmp($argv[3], 'register')==0){
			$msg = array("username"=>$argv[1], "password"=>$argv[2], "type"=>"register");
}
}
else
{



	$msg = $argv[1];
}
}
else{
	$msg = array("message"=>"test message", "type"=>"echo");
}

$response = $client->send_request($msg);

echo "client received response: " . PHP_EOL;
print_r($response);
echo "\n\n";

if(isset($argv[0]))
echo $argv[0] . " END".PHP_EOL;
