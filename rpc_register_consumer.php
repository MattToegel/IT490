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
	$log_file_name = "reg_consumer.log";
	write_log("register from: " . $n['email'], $log_file_name);
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
		$response = array(
			"status" => "success",
			"message" => "Record added successfully"
		);
		write_log("register success: " . $n["email"], $log_file_name);
	} elseif ($e[0] == "23000") {
		$response = array(
			"status" => "error",
			"message" => "Email or username already exists"
		);
		write_log("register error: " . $e[2], $log_file_name);
	} else {
		$response = array(
			"status" => "error1",
			"message" => $e[2]
		);
		write_log("register error: " . $e[2], $log_file_name);
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
while (count($channel->callbacks) || $channel->is_consuming() || $channel->is_open()) {
	$channel->wait();
}
$channel->close();
$connection->close();
