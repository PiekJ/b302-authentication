<?php namespace PiekJ\B302Authentication;

use Illuminate\Foundation\AliasLoader;
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

		$this->commands(
			'command.b302-auth.migration',
			'command.b302-auth.create-user',
			'command.b302-auth.publish'
		);

		include __DIR__ . '/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerDependencies();

		$this->registerCommands();
	}

	private function registerDependencies()
	{
		// Register the needed ServiceProviders
		$this->app->register('Zizaco\Confide\ServiceProvider');
		$this->app->register('Zizaco\Entrust\EntrustServiceProvider');

        // Register the needed aliases
        AliasLoader::getInstance()->alias('Confide', 'Zizaco\Confide\Facade');
        AliasLoader::getInstance()->alias('Entrust', 'Zizaco\Entrust\EntrustFacade');
	}

	private function registerCommands()
	{
		$this->app->bind('command.b302-auth.migration', function() {
			return new MigrationCommand();
		});

		$this->app->bind('command.b302-auth.create-user', function() {
			return new CreateUserCommand();
		});

		$this->app->bind('command.b302-auth.publish', function() {
			return new PublishCommand();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'command.b302-auth.migration',
			'command.b302-auth.create-user',
			'command.b302-auth.publish',
		);
	}

}
