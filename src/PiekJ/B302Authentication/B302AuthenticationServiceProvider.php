<?php namespace PiekJ\B302Authentication;

use Illuminate\Support\ServiceProvider;

class B302AuthenticationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('piek-j/b302-authentication', 'b302-auth');

        $this->commands('command.b302-auth.migrate');
        $this->commands('command.b302-auth.create-user');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register the needed ServiceProviders
		$this->app->register('PiekJ\LaravelRbac\LaravelRbacServiceProvider');
		$this->app->register('Lud\Club\ClubServiceProvider');

        // Register the needed alias
		$this->app->alias('Rbac', 'PiekJ\LaravelRbac\RbacFacade');

		// Register the install command
		$this->app['command.b302-auth.migrate'] = $this->app->share(function($app)
        {
            return new MigrateCommand();
        });

        $this->app['command.b302-auth.create-user'] = $this->app->share(function($app)
        {
            return new CreateUserCommand();
        });
	}

    public function provides()
    {
        return array(
            'command.b302-auth.migrate',
            'command.b302-auth.create-user',
        );
    }

}
