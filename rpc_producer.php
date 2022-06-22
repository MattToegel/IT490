<?php


require_once(__DIR__ . '/vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RpcClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct()
    {
        require_once(__DIR__ . '/lib/configrmq.php');
        $this->connection = new AMQPStreamConnection(
            $brokerhost,
            $brokerport,
            $brokeruser,
            $brokerpass
        );
        $this->channel = $this->connection->channel();
        list($this->callback_queue,,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );
        $this->channel->basic_consume(
            $this->callback_queue,
            '',
            false,
            true,
            false,
            false,
            array(
                $this,
                'onResponse'
            )
        );
    }

    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($n, $queue_name)
    {
        $this->response = null;
        $this->corr_id = uniqid();
        $data = json_encode($n);
        $msg = new AMQPMessage(
            $data,
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            )
        );
        $this->channel->basic_publish($msg, '', $queue_name);
        while (!$this->response) {
            $this->channel->wait();
        }

        return $this->response;
    }

    public function quit()
    {
        $this->connection->close();
        $this->channel->close();
    }
}
