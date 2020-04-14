<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new RabbitMQClient('testRabbitMQ.ini', 'testServer');
if(isset($argv[1])){
	//$msg = $argv[1];
	$msg = array("query"=>$argv[1], "type"=> "get_news");
}
else{
	//$msg = array("message"=>"test message", "type"=>"echo");
	$msg = array("query"=>"corona", "type"=>"get_news");
}

$response = $client->send_request($msg);

echo "client received response: " . PHP_EOL;
print_r($response);
echo "\n\n";

if(isset($argv[0]))
echo $argv[0] . " END".PHP_EOL;
