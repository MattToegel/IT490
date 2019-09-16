
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
echo "<pre>" . var_dump($this->machine) . "</pre>";
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
	}
	function get_connection(){
		return new AMQPStreamConnection($this->BROKER_HOST, $this->BROKER_PORT,
					$this->USER, $this->PASSWORD);
	}
	function process_message($msg){
		// send the ack to clear the item from the queue
		if ($msg->getRoutingKey() !== "*"){
			return;
		}
		$this->conn_queue->ack($msg->getDeliveryTag());
		try{
			if ($msg->getReplyTo()){
				// message wants a response
				// process request
				$body = $msg->getBody();
				$payload = json_decode($body, true);
				$response;
				if (isset($this->callback)){
					$response = call_user_func($this->callback,$payload);
				}
				$conn = $this->get_connection();
				$channel = $conn->channel();
echo "$this->EXCHANGE";
				$channel->exchange_declare($this->EXCHANGE, AMQPExchangeType::DIRECT, false, false, true);
				
				//$channel->queue_declare($this->INPUT_QUEUE, false, false, false, false);
				//$channel->queue_bind($this->OUTPUT_QUEUE, $this->EXCHANGE, $this->routing_key);
				$replykey = $this->routing_key.".response";
				$channel->queue_bind($this->OUTPUT_QUEUE, $this->EXCHANGE, $replykey);
				$channel->basic_publish($response, $this->EXCHANGE, $replykey);
				return;
			/*$channel = new AMQPChannel($conn);
			$exchange = new AMQPExchange($channel);
      $exchange->setName($this->exchange);
      $exchange->setType($this->exchange_type);
			$conn_queue = new AMQPQueue($channel);
			$conn_queue->setName($msg->getReplyTo());
			$replykey = $this->routing_key.".response";
			$conn_queue->bind($exchange->getName(),$replykey);
			$exchange->publish(json_encode($response),$replykey,AMQP_NOPARAM,array('correlation_id'=>$msg->getCorrelationId()));
				return;*/
			}
		}
		catch(Exception $e)
		{
			// ampq throws exception if get fails...
            echo "error: rabbitMQServer: process_message: exception caught: ".$e;
		}
		// message does not require a response, send ack immediately
		$body = $msg->getBody();
		$payload = json_decode($body, true);
		if (isset($this->callback))
		{
			call_user_func($this->callback,$payload);
		}
		echo "processed one-way message\n";
	}
	function process_requests($callback){
		try
		{
			$this->callback = $callback;
      		$conn = $this->get_connection();
			//$conn->connect();
			$channel = $conn->channel();//new AMQPChannel($conn);
			//$exchange = new AMQPExchange($channel);
			//$exchange->setName($this->exchange);
			//$exchange->setType($this->exchange_type);
			$channel->exchange_declare($this->EXCHANGE, AMQPExchangeType::DIRECT
			,false, false, false);
			
			$channel->queue_declare($this->QUEUE);
			$channel->queue_bind($this->QUEUE,$this->EXCHANGE);
			$channel->basic_consume($this->QUEUE, $this->routing_key, false, false, true, false, array($this, 'process_message'));
			/*$this->conn_queue = new AMQPQueue($channel);
			$this->conn_queue->setName($this->queue);
			$this->conn_queue->bind($exchange->getName(),$this->routing_key);
			$this->conn_queue->consume(array($this,'process_message'));*/
			// Loop as long as the channel has callbacks registered
			while (count($channel->callbacks))
			{
				$channel->wait();
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
	function __construct($machine, $server = "rabbitMQ")
	{
		$this->machine = getHostInfo(array($machine));
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
	}
	function get_connection(){
		return new AMQPStreamConnection($this->BROKER_HOST, $this->BROKER_PORT,
					$this->USER, $this->PASSWORD);
	}
	function process_response($response)
	{
		$uid = $response->getCorrelationId();
		if (!isset($this->response_queue[$uid]))
		{
		  echo  "unknown uid\n";
		  return true;
		}
		$this->conn_queue->ack($response->getDeliveryTag());
		$body = $response->getBody();
		$payload = json_decode($body, true);
		if (!(isset($payload)))
		{
			$payload = "[empty response]";
		}
		$this->response_queue[$uid] = $payload;
		return false;
	}
	function send_request($message){
		$uid = uniqid();
		$json_message = json_encode($message);
		try{
			$conn = $this->get_connection();
			$channel = $conn->channel();
			$channel->exchange_declare($this->EXCHANGE, AMQPExchangeType::DIRECT, false, false, true);
			
			//create message and properties
			$msg = new AMQPMessage($json_message, array(
				'correlation_id'=> $uid,
				'content_type'=>'text/json',
				'reply_to' => $this->routing_key . ".response")
			);
			
			$channel->basic_publish($msg, $this->EXCHANGE, $this->routing_key);
			$this->response_queue[$uid] = "waiting";
			$channel->basic_consume($this->QUEUE, $this->routing_key . ".response",
			false, false, true, false, array($this, 'process_response'));
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
	{
		$json_message = json_encode($message);
		try
		{
			$conn = $this->get_connection();
			$channel = $channel->channel();
			$channel->exchange_declare($this->EXCHANGE, AMQPExchangeType::DIRECT, false, false, true);
			
			
			
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
