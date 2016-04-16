<?php

Dotenv::load(__DIR__ .'/../../../');

return array(

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => getenv('DATABASE_HOST') ?: 'localhost',
			'database'  => getenv('DATABASE_NAME') ?: 'notes',
			'username'  => getenv('DATABASE_USER') ?: 'root',
			'password'  => getenv('DATABASE_PASSWORD') ?: 'root',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => getenv('DATABASE_HOST') ?: 'localhost',
			'database' => getenv('DATABASE_NAME') ?: 'homestead',
			'username' => getenv('DATABASE_USER') ?: 'homestead',
			'password' => getenv('DATABASE_PASSWORD') ?: 'secret',
			'charset'  => 'utf8',
			'prefix'   => '',
			'schema'   => 'public',
		),

	),

);
