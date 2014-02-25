<?php namespace Primathon\Idea;

use Illuminate\Support\ServiceProvider;

use Primathon\Idea\Commands;
use Primathon\Idea\Generators;

class IdeaServiceProvider extends ServiceProvider {

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
		$this->package('primathon/idea');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Run protected registration functions
		$this->registerControllerGenerator();
		$this->registerLangGenerator();
		$this->registerMigrationGenerator();
		$this->registerModelGenerator();
		$this->registerViewsGenerator();

		// Register Artisan commands
		$this->commands(
			'idea.controller',
			'idea.lang',
			'idea.migration',
			'idea.model',
			'idea.views'
		);

	}

	/**
	 * Register idea:model
	 *
	 * @return Commands\GenerateModelCommand
	 */
	protected function registerModelGenerator()
	{
		$this->app['idea.model'] = $this->app->share(function($app) {
			$generator = new Generators\ModelGenerator;
			return new Commands\GenerateModelCommand($generator);
		});
	}

	/**
	 * Register idea:controller
	 *
	 * @return Commands\GenerateControllerCommand
	 */
	protected function registerControllerGenerator()
	{
		$this->app['idea.controller'] = $this->app->share(function($app) {
			$generator = new Generators\ControllerGenerator;
			return new Commands\GenerateControllerCommand($generator);
		});
	}
	
	/**
	 * Register idea:lang
	 *
	 * @return Commands\GenerateLangCommand
	 */
	protected function registerLangGenerator()
	{
		$this->app['idea.lang'] = $this->app->share(function($app) {
			$generator = new Generators\LangGenerator;
			return new Commands\GenerateLangCommand($generator);
		});
	}
	
	/**
	 * Register idea:migration
	 *
	 * @return Commands\GenerateMigrationCommand
	 */
	protected function registerMigrationGenerator()
	{
		$this->app['idea.migration'] = $this->app->share(function($app) {
			$generator = new Generators\MigrationGenerator;
			return new Commands\GenerateMigrationCommand($generator);
		});
	}
	
	/**
	 * Register idea:views
	 *
	 * @return Commands\GenerateViewsCommand
	 */
	protected function registerViewsGenerator()
	{
		$this->app['idea.views'] = $this->app->share(function($app) {
			$generator = new Generators\ViewsGenerator;
			return new Commands\GenerateViewsCommand($generator);
		});
	}
	
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
//	public function provides()
//	{
//		return array();
//	}

}
