<?php


require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RpcServer
{

	private $connection;
	private $channel;
	private $callback_queue;
	private $response;
	private $corr_id;
	private $queue;

	public function __construct($queue_name)
	{
		require_once(__DIR__ . '/lib/configrmq.php');
		$this->connection = new AMQPStreamConnection(
			$brokerhost,
			$brokerport,
			$brokeruser,
			$brokerpass
		);

		$this->channel = $this->connection->channel();
		$this->$queue = $this->channel->queue_declare(
			$queue_name,
			false,
			false,
			false,
			false
		);
	}

	public function getData($function)
	{

		$this->channel->basic_qos(null, 1, null);

		$this->channel->basic_consume(
			$this->queue,
			'',
			false,
			false,
			false,
			false,
			array(
				$this,
				'onReceive'
			)
		);

		while ($this->channel->is_open()) {
			$this->channel->wait();
		}
	}


	public function onReceive($req)
	{
		echo "[x] Awaiting RPC requests";
		$n = $req->body();
		$consumed_data = json_decode($n, true);
		$return_msg = json_encode(getData($consumed_data));
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
	}


	public function quit()
	{
		$this->connection->close();
		$this->channel->close();
	}
}
