<?php
class Client{
function get_news($query){
	require_once('path.inc');
        require_once('get_host_info.inc');
        require_once('rabbitMQLib.inc');
        try{
        	$client = new RabbitMQClient('testRabbitMQ.ini', 'testServer');
                $msg = array("query"=>$query, "type"=> "get_news");
                $response = $client->send_request($msg);       
			// $output = array("data"=>json_decode($response, true));
               // error_log(var_export($response,true), 0);
		return $response;
	}
        catch(Exception $e){
        	return $e->getMessage();
	}
 }
}
?>
