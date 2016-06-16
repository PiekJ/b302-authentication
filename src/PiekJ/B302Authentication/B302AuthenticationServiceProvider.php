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
		$this->package('piek-j/b302-authentication');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register the needed ServiceProviders
		$this->app->register('Zizaco\Entrust\EntrustServiceProvider');
		$this->app->register('Lud\Club\ClubServiceProvider');

		$this->app->alias('Entrust', 'Zizaco\Entrust\EntrustFacade');

		// Register the install command
		$this->app['command.b302-authentication.install'] = $this->app->share(function($app)
        {
            return new InstallCommand();
        });

        $this->commands(array('command.b302-authentication.install'));
	}

}
