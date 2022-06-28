<?php

require_once(__DIR__ . '/lib/configrmq.php');
require_once(__DIR__ . "/lib/helpers.php");
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($brokerhost, $brokerport, $brokeruser, $brokerpass);
$channel = $connection->channel();

$channel->queue_declare('login_queue', false, false, false, false);


function userLogin($n)
{
	$log_file_name = "login_consumer.log";
	echo "\n**Querying database\n";
	write_log("userLogin from: " . $n['user_email'], $log_file_name);

	$db = getDB();
	$query = "SELECT * FROM Users WHERE email = :user_email OR username = :user_email AND `is_active` = 1";
	$stmt = $db->prepare($query);
	$params = array(":user_email" => $n["user_email"]);
	$r = $stmt->execute($params);
	$e = $stmt->errorInfo();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$response = null;
	if ($e[0] != "00000") {
		$response = array(
			"status" => "error",
			"message" => $e[2]
		);
		write_log("userLogin error: " . $e[2], $log_file_name);
	} else {

		if ($result && isset($result["password"]) && $result["is_active"] == 1) {
			$password_hash = $result["password"];
			if (password_verify($n["pass"], $password_hash)) {
				unset($result["password"]);
				$response = array(
					"fname" => $result["fname"],
					"lname" => $result["lname"],
					"username" => $result["username"],
					"email" => $result["email"],
					"id" => $result["id"],
					"bday" => $result["bday"]
				);
				write_log("userLogin success: " . $n["user_email"], $log_file_name);
			} else {
				$response = array(
					"status" => "error",
					"message" => "Invalid password"
				);
				write_log("userLogin error: Invalid password", $log_file_name);
			}
		} else {
			$response = array(
				"status" => "error",
				"message" => "Incorrect credentials"
			);
			write_log("userLogin error: Incorrect credentials", $log_file_name);
		}
	}
	return $response;
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
	$n = $req->body;
	$consumed_data = json_decode($n, true);
	$return_msg = json_encode(userLogin($consumed_data));
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
$channel->basic_consume('login_queue', '', false, false, false, false, $callback);
while (count($channel->callbacks) || $channel->is_consuming() || $channel->is_open()) {
	$channel->wait();
}
$channel->close();
$connection->close();
