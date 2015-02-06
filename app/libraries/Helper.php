<?php

class Helper {


	public static function isThisWorking() {
		Log::info("THIS IS WORKING!");
	}
/* 	Execute a CURL request and return the result */
	public static function fetchdata($url, $method, $fields)
	{
/* 		$fields_string = ""; */
		// get the data ready
/* 		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }rtrim($fields_string, '&'); */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		/* Explicit Posts */
		if($method == "post") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
		}
		/* Explicit Gets */
		else if($method == "get") {
			curl_setopt($ch, CURLOPT_URL, $url."?".http_build_query($fields));
			Log::info($url."?".http_build_query($fields));
		}
		/* Explicit Puts */
		else if($method == "put") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
		}
		/* Explicit Deletes */
		else if($method == "delete") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
		}
		/* Default to GETS */
		else {

		}
		$string = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		Log::info($url);
		Log::info($method);
		Log::info($fields);
		Log::info("LOGGING HTTP STATUS");
		Log::info($http_status);
		Log::info($string);
		if($http_status > 399) {
				curl_close($ch);
/* 				Log::info($string); */
				$decode = json_decode($string);
				$code = json_decode($string)->statusCode;
				$final = array("status_code"=>$code,"data"=>$decode);
				$string = json_encode($final);
/* 				Log::info($string); */
				return $string;
		}
		else {
				curl_close($ch);
/* 				Log::info($string); */
				$decode = json_decode($string);
				$final = array("status_code"=>200,"data"=>$decode);
				$string = json_encode($final);
/*  				Log::info($string);  */
				return 	$string;
		}
	}

	public static function fetchlocal($url,$method,$fields)
	{
		Log::info("SELF QUERY");
/* 		$fields_string = ""; */
		// get the data ready
/* 		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }rtrim($fields_string, '&'); */
// 		$domain = $_SERVER['HTTP_HOST'];
// 		$prefix = $_SERVER['HTTPS'] ? 'https://' : 'http://';
		$ch = curl_init("http://localhost/notes/".$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		/* Explicit Posts */
		if($method == "post") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
		}
		/* Explicit Gets */
		else if($method == "get") {
			Log::info("get");
			curl_setopt($ch, CURLOPT_URL, $url."?".http_build_query($fields));
		}
		/* Explicit Puts */
		else if($method == "put") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
		}
		/* Explicit Deletes */
		else if($method == "delete") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
		}
		/* Default to GETS */
		else {

		}
		$string = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $string;

	}
}

?>
