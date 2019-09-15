require_once  __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672,'guest','guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);


