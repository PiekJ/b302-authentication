<?php namespace PiekJ\B302Authentication;

use Config;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateUserCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'b302-auth:create-user';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a default admin user';

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
		$manageUsersPermission = new Permission();
		$manageUsersPermission->name = 'manage_users';
		$manageUsersPermission->display_name = 'Manage users';
		$manageUsersPermission->save();

		$adminRole = new Role();
		$adminRole->name = 'Admin';
		$adminRole->save();

		$adminRole->attachPermission($manageUsersPermission);

		$userModel = Config::get('auth.model');
		$adminUser = new $userModel();
		$adminUser->username = 'Admin';
		$adminUser->email = 'admin@admin.nl';
		$adminUser->password = 'admin';
		$adminUser->password_confirmation  = 'admin';
		$adminUser->confirmation_code = md5(uniqid(mt_rand(), true));
		$adminUser->confirmed = 1;
		$adminUser->save();

		$adminUser->attachRole($adminRole);

		$this->info('User Admin, with email: admin@admin.nl and password: admin, created!');
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
