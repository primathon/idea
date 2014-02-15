<?php namespace Primathon\Idea\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BaseGeneratorCommand extends Command {

	/**
	 * The console command name.
	 * Set in individual subclasses
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The console command description.
	 * Set in individual subclasses
	 *
	 * @var string
	 */
	protected $description;


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('idea', InputArgument::REQUIRED, 'Idea source file to generate model.'),
		);
	}


	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
//	protected function getOptions()
//	{
//		return array(
//			array('path',     null, InputOption::VALUE_OPTIONAL, 'Path to the models directory.', app_path() . '/models'),
//			array('template', null, InputOption::VALUE_OPTIONAL, 'Path to template.', __DIR__.'/../Generators/templates/model.txt')
//		);
//	}

}

