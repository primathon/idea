<?php namespace Primathon\Idea\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Primathon\Idea\Generators\ControllerGenerator as ControllerGenerator;

class GenerateControllerCommand extends BaseGeneratorCommand {

	/**
	 * Controller generator instance.
	 *
	 * @var Primathon\Idea\Generators\ControllerGenerator
	 */
	protected $controllerGenerator;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:controller';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a new controller from an Idea file';


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ControllerGenerator $controllerGenerator)
	{
		parent::__construct();
		$this->controllerGenerator = $controllerGenerator;
	}

	/**
	 * Instantiate the subgenerator, pass the Command class, set the Idea, and generate the file
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->controllerGenerator->setCommand($this);
		$this->controllerGenerator->setIdea($this->argument('idea'));
		$this->controllerGenerator->generate();
	}

}

