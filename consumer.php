<?php
	require_once(__DIR__ . "/lib/configrmq.php");
    require_once(__DIR__ . "/vendor/autoload.php");

    use PhpAmqpLib\Connection\AMQPStreamConnection;
    //use PhpAmqpLib\Exchange\AMQPExchangeType;
    require_once(__DIR__ . "/lib/helpers.php");

	//$CONSUMER_TAG = 'reg_consumer';

	$connection = new AMQPStreamConnection($brokerhost, $brokerport, $brokeruser, $brokerpass);

	$channel = $connection->channel();
	$channel->queue_declare('reg_queue', false, false, false, false);
	//$channel->exchange_declare($EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
	//$channel->queue_bind($QUEUE, $EXCHANGE);
    echo "Waiting for registration data...";

	$callback = function($message) {
        echo "Message received... \n";
		//echo $message->body;
		echo "\n------\n";
		$data = json_decode($message->body, true);
		echo $data[":email"];
        echo "**Adding record to DB\n";
		//$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
		$params = array(
			":fname" => $data[":fname"],
			":lname" => $data[":lname"],
			":email" => $data[":email"],
			":username" => $data[":username"],
			":bday" => $data[":bday"],
			":is_active" => $data[":is_active"],
			":password" => $data[":pass"]
		);
		$type = $data[":type"];
		$db = getDB();
		$query = "INSERT INTO Users(fname, lname, email, username, bday, is_active, `password`) ";
		$query .= "VALUES(:fname, :lname, :email, :username, :bday, :is_active, :password)";
		$stmt = $db->prepare($query);
		$r = $stmt->execute($params);
		$e = $stmt->errorInfo();
		if ($e[0] == "00000") {
			echo "\n\nRegistration Successful";
		}
		else {
			echo "There was a problem with registration";
		}
	};

    //$channel->basic_qos(null, 1, null);
	$channel->basic_consume('reg_queue', '', false, true, false, false, $callback);

/* 	function shutdown($channel, $connection) {
		$channel->close();
		$connection->close();
	} */

	//register_shutdown_function('shutdown', $channel, $connection);
    while($channel->is_open()) {
        $channel->wait();
    }
	

?>

