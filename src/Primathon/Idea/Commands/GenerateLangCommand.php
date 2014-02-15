<?php namespace Primathon\Idea\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Primathon\Idea\Generators\LangGenerator as LangGenerator;

class GenerateLangCommand extends BaseGeneratorCommand {

	/**
	 * Lang generator instance.
	 *
	 * @var Primathon\Idea\Generators\LangGenerator
	 */
	protected $langGenerator;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:lang';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a new language translation array from an Idea file';


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(LangGenerator $langGenerator)
	{
		parent::__construct();
		$this->langGenerator = $langGenerator;
	}

	/**
	 * Instantiate the subgenerator, pass the Command class, set the Idea, and generate the file
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->langGenerator->setCommand($this);
		$this->langGenerator->setIdea($this->argument('idea'));
		$this->langGenerator->generate();
	}

}


