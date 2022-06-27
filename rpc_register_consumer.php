<?php

require_once(__DIR__ . '/lib/configrmq.php');
require_once(__DIR__ . "/lib/helpers.php");
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($brokerhost, $brokerport, $brokeruser, $brokerpass);
$channel = $connection->channel();

$channel->queue_declare('reg_queue', false, false, false, false);


function register($n)
{
	//$data = json_decode($n, true);
	echo var_dump($n);
	echo "\n**Adding record to DB\n";
	//$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
	$params = array(
		":fname" => $n["fname"],
		":lname" => $n["lname"],
		":email" => $n["email"],
		":username" => $n["username"],
		":bday" => $n["bday"],
		":is_active" => $n["is_active"],
		":password" => $n["pass"]
	);
	//$type = $data[":type"];
	$db = getDB();
	$query = "INSERT INTO Users(fname, lname, email, username, bday, is_active, `password`) ";
	$query .= "VALUES(:fname, :lname, :email, :username, :bday, :is_active, :password)";
	$stmt = $db->prepare($query);
	$r = $stmt->execute($params);
	$e = $stmt->errorInfo();
	if ($e[0] == "00000") {
		$response = 0;
	} elseif ($e[0] == "23000") {
		$response = 1;
	} else {
		$response = 2;
	}
	return $response;
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
	$n = $req->body;
	$consumed_data = json_decode($n, true);
	$return_msg = json_encode(register($consumed_data));
	$msg = new AMQPMessage(
		$return_msg,
		array('correlation_id' => $req->get('correlation_id'))
	);

	$req->delivery_info['channel']->basic_publish(
		$msg,
		'',
		$req->get('reply_to')
	);
	$req->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('reg_queue', '', false, false, false, false, $callback);
$channel->wait();
