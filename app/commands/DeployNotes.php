<?php

Dotenv::load(__DIR__ .'/../../');

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DeployNotes extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'remote:deploy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "SSH's to a remote machine to pull from git, update requirements, and run migrations.";

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        SSH::run(array(
            'cd '.implode("/", array(getenv('REMOTE_ROOT'), getenv('REMOTE_PROJECT'))),
            'git pull',
            'php composer.phar update',
            'php artisan migrate --force',
        ), function($line) {
            echo $line.PHP_EOL;
        });
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
