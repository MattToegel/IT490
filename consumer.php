<?php
    require_once(__DIR__ . "/vendor/autoload.php");
    //include(__DIR__ . "/config_rmq.php");
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    //use PhpAmqpLib\Exchange\AMQPExchangeType;
    require_once(__DIR__ . "/lib/helpers.php");

	//$CONSUMER_TAG = 'reg_consumer';

	$connection = new AMQPStreamConnection('192.168.1.105', 5672, 'smit', 'P@78word');

	$channel = $connection->channel();
	$channel->queue_declare('reg_queue', false, false, false, false);
	//$channel->exchange_declare($EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
	//$channel->queue_bind($QUEUE, $EXCHANGE);
    echo "Waiting for registration data...";

	$callback = function($message) {
        echo "Message received... \n";
		echo json_decode($message->body, true);
		echo "\n------\n";
        echo "**Sending message back\n";
		//$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
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