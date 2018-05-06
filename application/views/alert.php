<?php
$CI =& get_instance();
$CI->load->config("socket");
function dorequest($url,$data=[],$secret=null){
	$cr = curl_init();
	curl_setopt($cr,CURLOPT_URL,$url);
	curl_setopt($cr,CURLOPT_RETURNTRANSFER,true);
	error_log("Secret = ".$secret);
	if($secret){
		curl_setopt($cr,CURLOPT_COOKIE,"secret=".$secret.";");
	}
	error_log(urlencode($data));
	if(!empty($data)){
		$dataJson = json_encode($data);
		curl_setopt($cr,CURLOPT_HEADER,true);
		curl_setopt(
			$cr,
			CURLOPT_HTTPHEADER,
			[
				"Content-Type:application/json",
				"Content-Lenght:".strlen($dataJson)
			]
		);
		curl_setopt($cr,CURLOPT_POST,true);
		curl_setopt($cr,CURLOPT_POSTFIELDS,$dataJson);
	}
	$buf = curl_exec($cr);
	if(!$buf){
		error_log(curl_error($cr));
	}
	return [json_decode($buf),$buf];

}
//we are going to use curl to send request to the server dealing with the alerts
$url = $CI->config->item("alert_url").":".$CI->config->item("alert_port")."/alert/new";
$secret = $CI->config->item("alert_secret");
$ret = dorequest($url,$alertData,$secret);
$return = $ret[0];
if($return && isset($return->success) &&(! $return->success)){
	error_log("it didn't like me :(");
} else {
	error_log($ret[1]);
}
