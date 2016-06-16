<?php namespace PiekJ\B302Authentication;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'b302-auth:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Installs the authentication packages.';

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
		$this->info('Installing lud/club');
		$this->call('club:users-table');
		$this->call('auth:reminders-table');

		$this->info('Installing Zizaco/entrust');
		$this->call('entrust:migration')

		$this->call('migrate');
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
