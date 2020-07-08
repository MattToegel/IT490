<?php
require("config.inc");
$source = "bitcoin";
if(isset($argv[1])){
	//$argv[0] is name of script always
	$source = $argv[1];
}
if(isset($_GET["query"])){
	$source = $_GET["query"];
}
$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://newsapi.org/v2/everything?q=$source&apiKey=$api_key",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	//CURLOPT_POSTFIELDS => "apiKey=$api_key&newsSource=$source",
	CURLOPT_HTTPHEADER => array(
		"content-type: application/x-www-form-urlencoded",
		//"x-rapidapi-host: $rapid_api_host",
		//"x-rapidapi-key: $rapid_api_key"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	//echo $response;
	$r = json_encode($response);

	if(isset($_GET["browser"])){

		echo "<pre>" . var_export($r,true)  . "</pre>";
	}
	else{
		echo $r;
	}
}
?>
