<?php namespace Primathon\Idea\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Primathon\Idea\Generators\ViewsGenerator as ViewsGenerator;

class GenerateViewsCommand extends BaseGeneratorCommand {

	/**
	 * Views generator instance.
	 *
	 * @var Primathon\Idea\Generators\ControllerGenerator
	 */
	protected $viewsGenerator;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:views';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate index/create/edit/show views from an Idea file';


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ViewsGenerator $viewsGenerator)
	{
		parent::__construct();
		$this->viewsGenerator = $viewsGenerator;
	}

	/**
	 * Instantiate the subgenerator, pass the Command class, set the Idea, and generate the file
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->viewsGenerator->setCommand($this);
		$this->viewsGenerator->setIdea($this->argument('idea'));
		$this->viewsGenerator->generate();
	}

}

