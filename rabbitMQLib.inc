<?php
require_once(__DIR__ . "/vendor/autoload.php");
require_once('get_host_info.inc');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class rabbitMQServer
{
	private $machine = "";
	public  $BROKER_HOST;
	private $BROKER_PORT;
	private $USER;
	private $PASSWORD;
	private $VHOST;
	private $exchange;
	private $queue;
	private $routing_key = '*';
	private $exchange_type = "topic";
	private $auto_delete = false;
	function __construct($machine, $server = "rabbitMQ"){
		$this->machine = getHostInfo($server,array($machine));

		$this->BROKER_HOST   = $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT   = $this->machine[$server]["BROKER_PORT"];
		$this->USER     = $this->machine[$server]["USER"];
		$this->PASSWORD = $this->machine[$server]["PASSWORD"];
		$this->VHOST = $this->machine[$server]["VHOST"];
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"])){
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		if (isset( $this->machine[$server]["AUTO_DELETE"])){
			$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		}
		$this->exchange = $this->machine[$server]["EXCHANGE"];
		$this->queue = $this->machine[$server]["QUEUE"];
	}
	function get_connection(){
		return new AMQPStreamConnection($this->BROKER_HOST, $this->BROKER_PORT,
					$this->USER, $this->PASSWORD);
	}
	function process_message($req){
		echo "processing message \n";
		// send the ack to clear the item from the queue
		/*if ($msg->delivery_info['routing_key'] !== "*"){
			echo "routing_key: " . $msg->delivery_info['routing_key'];

			return;
		}*/
		$deliveryTag = $req->get('delivery_tag');
		//$this->conn_queue->basic_ack($deliveryTag);
		try{

			if ($req->get('reply_to')){
				// message wants a response
				// process request
				$body = $req->body;
				$payload = json_decode($body, true);
				//$payload['message'] = "Echo " . $payload['message'];
				$response;
				if (isset($this->callback)){
					$response = call_user_func($this->callback,$payload);
				}
				$conn = $this->get_connection();
				$channel = $conn->channel();
				$msg = new AMQPMessage(
					(string)json_encode($response),
					array('correlation_id'=>$req->get('correlation_id'))
				);
				echo "Replying to " . $req->get('reply_to') . "\n";
				$req->delivery_info['channel']->basic_publish(
					$msg,
					'',
					$req->get('reply_to')
				);
				return;
			}
		}
		catch(Exception $e)
		{
			// ampq throws exception if get fails...
            echo "error: rabbitMQServer: process_message: exception caught: ".$e;
		}
		// message does not require a response, send ack immediately
		$body = $msg->body;
		$payload = json_decode($body);
		if (isset($this->callback)){
			call_user_func($this->callback,$payload);
		}
		echo "processed one-way message\n";
	}
	function process_requests($callback){
		try{
			echo "processing request\n";
			$this->callback = $callback;
			$conn = $this->get_connection();
			$channel = $conn->channel();
			$this->conn_queue = $channel;
			$channel->queue_declare($this->queue, false, false, false, true);
			echo "Consuming queue\n";
			$channel->basic_qos(null, 1, null);
			$channel->basic_consume($this->queue, '',
			false, true, false, false, array($this, 'process_message'));

			// Loop as long as the channel has callbacks registered
			while (count($this->conn_queue->callbacks) && $this->conn_queue->is_consuming())
			{
				$this->conn_queue->wait();
			}
		}
		catch (Exception $e)
		{
			trigger_error("Failed to start request processor: ".$e,E_USER_ERROR);
		}
	}
}
class rabbitMQClient
{
	private $machine = "";
	public  $BROKER_HOST;
	private $BROKER_PORT;
	private $USER;
	private $PASSWORD;
	private $VHOST;
	private $exchange;
	private $queue;
	private $routing_key = '*';
	private $response_queue = array();
	private $exchange_type = "topic";
	private $channel;
	function __construct($machine, $server = "rabbitMQ")
	{
		$this->machine = getHostInfo($server, array($machine));
		$this->BROKER_HOST   = $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT   = $this->machine[$server]["BROKER_PORT"];
		$this->USER     = $this->machine[$server]["USER"];
		$this->PASSWORD = $this->machine[$server]["PASSWORD"];
		$this->VHOST = $this->machine[$server]["VHOST"];
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"]))
		{
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		if (isset( $this->machine[$server]["AUTO_DELETE"]))
		{
			$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		}
		$this->exchange = $this->machine[$server]["EXCHANGE"];
		$this->queue = $this->machine[$server]["QUEUE"];
		$this->consume();
	}
	function get_connection(){
		return new AMQPStreamConnection($this->BROKER_HOST, $this->BROKER_PORT,
					$this->USER, $this->PASSWORD);
	}
	function process_response($response){
		$uid = $response->get('correlation_id');
		if (!isset($this->response_queue[$uid])){
		  echo  "unknown uid\n";
		  return true;
		}
		$this->channel->basic_ack($response->get('delivery_tag'));

		$body = $response->body;
		$payload = json_decode($body);
		var_dump($payload);
		if (!isset($payload)){
			$payload = "[empty or invalid response]";
		}
		$this->response_queue[$uid] = $payload;
		return false;
	}
	function consume(){
		$conn = $this->get_connection();
		$this->channel = $conn->channel();
		$this->callback_queue = $this->queue . '.response';
		$this->channel->queue_declare($this->callback_queue, false, false, true, false);
		/*list($this->callback_queue, ,) = $this->channel->queue_declare(
				"",
				false,
				false,
				true,
				false
			);*/
		$this->channel->basic_consume($this->callback_queue, '', false, true, false, false,
		array($this, 'process_response'));
	}
	function send_request($message){
		$uid = uniqid();
		$json_message = json_encode($message);
		try{
			$this->response_queue[$uid] = null;
			$this->correlation_id = $uid;
			//create message and properties

			$msg = new AMQPMessage($json_message, array(
				'correlation_id'=> $this->correlation_id,
				'content_type'=>'application/json',
				'reply_to' => $this->callback_queue)
			);

			echo "sending message\n";

			$this->channel->basic_publish($msg, '', $this->queue);
			$this->response_queue[$uid] = "waiting";
			while($this->response_queue[$uid] === "waiting"){
				$this->channel->wait();
			}
			$response = $this->response_queue[$uid];
			unset($this->response_queue[$uid]);
			return $response;
		}
		catch(Exception $e)
		{
			die("failed to send message to exchange: ". $e->getMessage()."\n");
		}
	}
	/**
	  @brief send a one-way message to the server.  These are
	  auto-acknowledged and give no response.
	  @param message the body of the request.  This must make sense to the
	  server
	 */
	function publish($message)
	{//TODO update
		$json_message = json_encode($message);
		try
		{
			$conn = $this->get_connection();
			$channel = $channel->channel();
			$channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, false, true);



			$conn->connect();
			$channel = new AMQPChannel($conn);
			$exchange = new AMQPExchange($channel);
			$exchange->setName($this->exchange);
			$exchange->setType($this->exchange_type);
			$this->conn_queue = new AMQPQueue($channel);
			$this->conn_queue->setName($this->queue);
			$this->conn_queue->bind($exchange->getName(),$this->routing_key);
			return $exchange->publish($json_message,$this->routing_key);
		}
		catch(Exception $e)
		{
			die("failed to send message to exchange: ". $e->getMessage()."\n");
		}
	}
}
?>
