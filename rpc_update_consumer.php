<?php

require_once(__DIR__ . '/lib/configrmq.php');
require_once(__DIR__ . "/lib/helpers.php");
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($brokerhost, $brokerport, $brokeruser, $brokerpass);
$channel = $connection->channel();

$channel->queue_declare('update_queue', false, false, false, false);


function userUpdate($n)
{

    echo var_dump($n);
    echo "**Querying database\n";

    $db = getDB();
    $query = "SELECT `password` FROM Users WHERE id = :uid";
    $stmt = $db->prepare($query);
    $params = array(":uid" => $n["uid"]);
    $r = $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_password_hash = $result["password"];
    if (password_verify($n["current_password"], $current_password_hash)) {
        echo "Password Verified";
        unset($result["password"]);
        $updated_password = $n["new_password"];
        if ($updated_password == "") {
            $updated_password = $n["current_password"];
        }
        $updated_password_hash = password_hash($updated_password, PASSWORD_BCRYPT);
        $query = "UPDATE Users SET username = :username, `password` = :updated_password WHERE id = :uid";
        $stmt = $db->prepare($query);
        $params = array(
            ":username" => $n["username"],
            ":updated_password" => $updated_password_hash,
            ":uid" => $n["uid"]
        );
        $r = $stmt->execute($params);
        $e = $stmt->errorInfo();
        $response = null;
        if ($e[0] != "00000") {
            $response = array(
                "status" => "error",
                "message" => $e[2]
            );
        } else {

            $response = array(
                "status" => "success",
                "username" => $n["username"]
            );
        }
    } else {
        $response = array(
            "status" => "error",
            "message" => "Invalid password"
        );
    }

    return $response;
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $n = $req->body;
    $consumed_data = json_decode($n, true);
    $return_msg = json_encode(userUpdate($consumed_data));
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
$channel->basic_consume('update_queue', '', false, false, false, false, $callback);
$channel->wait();


