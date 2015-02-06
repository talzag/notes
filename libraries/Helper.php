<?php

class Helper {
/* 	Function to test globalities are working properly   */
	public static function returnString() {
		return "string";
	}
/* 	Change object into an array ready to use */
	public static function object_to_array($obj) {
/* 		Log::info($obj); */
	}

	public static function createSMS($total,$business_id) {
		Log::info("creating SMS");
		Log::info($total);
		Log::info($business_id);

		$user_args = array("session_secret"=>"353b2aa9ca8d223fdf15bc54a23c285cd26d1f5f","business_id"=>$business_id);
		$user_id = json_decode(Helper::fetchdata(Config::get('app.node_url') . "users","get",$user_args))->data[0]->user_id;
		Log::info($user_id);

		// get back accounts for that users
		$account_args = array("session_secret"=>"353b2aa9ca8d223fdf15bc54a23c285cd26d1f5f","business_id"=>$business_id);
		$credit_id = json_decode(Helper::fetchdata(Config::get('app.node_url') . "bankaccounts","get",$account_args))->data[0]->bank_account_id;
		Log::info($credit_id);

		// get the api key
		$key_args = array("session_secret"=>"353b2aa9ca8d223fdf15bc54a23c285cd26d1f5f","business_id"=>$business_id);
		$api_key = json_decode(Helper::fetchdata(Config::get('app.node_url') . "businesskeys","get",$key_args))->data[0]->api_key;

		// get the payment key
		$args = array("session_secret"=>"353b2aa9ca8d223fdf15bc54a23c285cd26d1f5f","total"=>$total,"credit_id"=>$credit_id,"api_key"=>$api_key);
		$payment_key = Helper::fetchdata(Config::get('app.node_url') . "payments","put",$args);
		Log::info($payment_key);
		return json_decode($payment_key)->data->payment_key;
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
/* 		$fields_string = ""; */
		// get the data ready
/* 		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }rtrim($fields_string, '&'); */
		$ch = curl_init();
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
