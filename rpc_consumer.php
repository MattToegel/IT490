<?php

require_once(__DIR__ . "/lib/helpers.php");
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'smit', 'P@78word');
$channel = $connection->channel();

$channel->queue_declare('reg_queue', false, false, false, false);

function fib($n)
{
    $data = json_decode($n, true);
		echo var_dump($data);
        echo "\n**Adding record to DB\n";
		//$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
		$params = array(
			":fname" => $data["fname"],
			":lname" => $data["lname"],
			":email" => $data["email"],
			":username" => $data["username"],
			":bday" => $data["bday"],
			":is_active" => 1,
			":password" => password_hash($data["pass"], PASSWORD_BCRYPT)
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
				'msg' => 'Registration Successful'
			);
		}
		else {
			$response = array(
				'msg' => 'There was a problem with registration'
			);    
		}
        return $response;
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $n = $req->body;
    
    $return_msg = fib($n);
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

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
