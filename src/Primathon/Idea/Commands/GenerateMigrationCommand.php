<?php namespace Primathon\Idea\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Primathon\Idea\Generators\MigrationGenerator as MigrationGenerator;

class GenerateMigrationCommand extends BaseGeneratorCommand {

	/**
	 * Migration generator instance.
	 *
	 * @var Primathon\Idea\Generators\MigrationGenerator
	 */
	protected $migrationGenerator;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:migration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a new migration from an Idea file';


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(MigrationGenerator $migrationGenerator)
	{
		parent::__construct();
		$this->migrationGenerator = $migrationGenerator;
	}

	/**
	 * Instantiate the subgenerator, pass the Command class, set the Idea, and generate the file
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->migrationGenerator->setCommand($this);
		$this->migrationGenerator->setIdea($this->argument('idea'));
		$this->migrationGenerator->generate();
	}

}


