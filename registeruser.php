<?php
    include(__DIR__ . "/vendor/autoload.php");
    include(__DIR__ . "/config_rmq.php");
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Exchange\AMQPExchangeType;
    require_once(__DIR__ . "/lib/helpers.php");

	$CONSUMER_TAG = 'reg_consumer';

	$connection = new AMQPStreamConnection($BROKER_HOST, $BROKER_PORT, $USER, $PASSWORD, $VHOST);

	$channel = $connection->channel();
	$channel->queue_declare($QUEUE, false, true, false, false);
	$channel->exchange_declare($EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
	$channel->queue_bind($QUEUE, $EXCHANGE);

	function process_message($message) {
		echo json_decode($message);
		echo "\n------\n";
		$message->ack();
	}

	$channel->basic_consume($QUEUE, $CONSUMER_TAG, false, false, false, false, 'process_message');

	function shutdown($channel, $connection) {
		$channel->close();
		$connection->close();
	}

	register_shutdown_function('shutdown', $channel, $connection);

	$channel->consume();

?>