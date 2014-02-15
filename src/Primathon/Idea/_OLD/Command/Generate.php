<?php namespace Primathon\Idea\Command;

use \Primathon\Idea\Command\BaseCommand;

class Generate extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:generate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Test command to init Writer functions';

	/**
	 * Execute this command, generating both models and migrations
	 *
	 * @param array $parsed An array of ModelList and MigrationList
	 * @return void
	 */
	protected function runCommand($parsed)
	{
		echo 'success';
		return true;
		// Generate migrations
//		$this->generateMigrations($parsed['migrationList']->all());

		// Generate models
//		$this->generateModels($parsed['modelList']->all());
	}

}
