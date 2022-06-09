<?php
require_once("config.inc");
$source = "The Avengers";
if(isset($argv[1])){
	//$argv[0] is name of script always
	$source = $argv[1];
}
if(isset($_GET["query"])){
	$source = $_GET["query"];
}
$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://opentdb.com/api.php?amount=10&category=11&difficulty=easy",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	//CURLOPT_POSTFIELDS => "apiKey=$api_key&newsSource=$source",
	CURLOPT_HTTPHEADER => array(
		"content-type: application/json",
		//"X-RapidAPI-Host: moviesdatabase.p.rapidapi.com",
		//"X-RapidAPI-Key: 5e30fe309cmsheeda5c4f1e65deep1e94f5jsn1e4b3477cbc8"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo $response . "\n";
	$r = json_encode($response);
	if(isset($_GET["browser"])){

		echo "<pre>" . var_export($r,true)  . "</pre>";
	}
	else{
		echo $r;
	}
}
?>
