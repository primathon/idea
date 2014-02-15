<?php namespace Primathon\Idea\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use \Primathon\Idea\Parser;
use \Primathon\Idea\Writer;
use \Primathon\Idea\Exception\ParseError;

class BaseCommand extends Command {

	/**
	 * Instance of the Idea Parser
	 *
	 * @var \Primathon\Idea\Parser
	 */
	protected $parser;

	/**
	 * Instance of the Idea Writer class
	 *
	 * @var \Primathon\Idea\Writer
	 */
	protected $writer;

	/**
	 * Define where the templates are located
	 *
	 * @var string path
	 */
	protected $templatePath;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Fire parent constructor command
		parent::__construct();

		// Define root template path; specific files are in this directory
		$this->templatePath = __DIR__ . '/../Generators/templates/';

		// Define output paths for the Writer
		$paths = array(
			'models'      => app_path() . '/models/',
			'views'       => app_path() . '/views/',
			'controllers' => app_path() . '/controllers/',
			'seeds'       => app_path() . '/database/seeds/',
			'migrations'  => app_path() . '/database/migrations/',
		);

		$this->writer = new Writer($paths);
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// Check if the input file exists
		$filename = base_path() . '/' . $this->argument('filename');
		if (!file_exists($filename))
		{
			$this->error("The input filename you specified doesn't exist: {$filename}");
		}
		
		// If the file exists, get it contents and parse it
		try {
			$file = \File::get($filename);
			$parser = new Parser();
			$model = $parser->parse($file);
		}
		// Something went wrong? Throw an error and let me know
		catch (ParseError $e)
		{
			$this->error($e->getMessage());
			die();
		}

		// Now, run the desired function of the subclass
		$this->runCommand($model);
	}

	/**
	 * This command has to be overriden in individual commands to either generate
	 * models or migrations or both
	 *
	 * @param  array $parsed An array of ModelList and MigrationList
	 * @return void
	 */
	protected function runCommand($parsed)
	{
		throw new \Exception("The runCommand function has to be overriden.");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('filename', InputArgument::REQUIRED, 'Name of the input Idea file.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
