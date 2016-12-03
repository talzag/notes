<?php

// $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
// $redirect_uri = $root;
$redirect_uri = "http://localhost/google/login";

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => 'blankslate.io',
		'secret' => 'key-ac3521aff558b6794569885c66a9910f',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'App\User',
		'secret' => '',
	],
	'google' => [
    'client_id' => '841513879584-fl6eokkpeut12lj2g328dmj64eqbc6s7.apps.googleusercontent.com',
    'client_secret' => 'PqQPkpcCMycSyDjg7QT1I-GY',
    'redirect' => $redirect_uri,
],

];
