<?php namespace PiekJ\B302Authentication;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Lud\Club\Club;
use Hash;

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
    protected $description = 'Creates a default user with a default role.';

    /**
     * The model to use to create a admin user.
     *
     * @var string
     */
    private $modelName;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->modelName = Club::modelName();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->info('Create default admin user with default rights');
        $adminUser = new $this->modelName();
        $adminUser->email = 'admin@admin.nl';
        $adminUser->password = Hash::make('admin');
        $adminUser->save();

        $adminRole = new Role();
        $adminRole->name = 'Admin';
        $adminRole->save();

        $adminUser->attachRole($adminRole);

        $manageUsersPermission = new Permission();
        $manageUsersPermission->name = 'manage_users';
        $manageUsersPermission->display_name = 'Manage Users';
        $manageUsersPermission->save();

        $adminRole->perms()->sync(array($manageUsersPermission->id));
        $this->info('Default user created !');
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
