<?php namespace Primathon\Idea\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Primathon\Idea\Generators\ModelGenerator as ModelGenerator;

class GenerateModelCommand extends BaseGeneratorCommand {

	/**
	 * Model generator instance.
	 *
	 * @var Primathon\Idea\Generators\ControllerGenerator
	 */
	protected $modelGenerator;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:model';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a new model from an Idea file';


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ModelGenerator $modelGenerator)
	{
		parent::__construct();
		$this->modelGenerator = $modelGenerator;
	}

	/**
	 * Instantiate the subgenerator, pass the Command class, set the Idea, and generate the file
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->modelGenerator->setCommand($this);
		$this->modelGenerator->setIdea($this->argument('idea'));
		$this->modelGenerator->generate();
	}

}
