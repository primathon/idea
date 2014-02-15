<?php
namespace Primathon\Idea\Generators;

use Primathon\Idea\Parser\Parser;

use Illuminate\Filesystem\Filesystem as File;

abstract class BaseGenerator {

	/**
	 * Path to Idea file
	 *
	 * @var string
	 */
	protected $idea;

	/**
	 * Parser object
	 *
	 * @var Parser
	 */
	protected $parser;

	/**
	 * Model object generated from Parser class
	 *
	 * @var Parser\ParserModel
	 */
	protected $model;

	/**
	 * Illuminate Console Command instance
	 *
	 * @var Illuminate\Console\Command
	 */
	protected $command;

	/**
	 * Path to Generators template directory
	 *
	 * @var string
	 */
	protected $templatePath;

	/**
	 * Paths to Laravel object destinations (models, controllers, etc)
	 *
	 * @var array
	 */
	protected $outputPaths;


	/**
	 * Class constructor
	 *
	 * Create Parser
	 * Set templates directory
	 */
	public function __construct()
	{
		$this->parser       = new Parser;
		$this->templatePath = __DIR__ . '/templates/';
		$this->outputPaths   = array(
			'controllers' => '/controllers/',
			'lang'        => '/lang/en/',
			'migrations'  => '/database/migrations/',
			'models'      => '/models/',
//			'routes'      => '/routes.php',       // special case
			'seeds'       => '/database/seeds/',
			'views'       => '/views/',
		);

	}

	/**
	 * Set Idea file
	 *
	 * @param  string $idea
	 */
	public function setIdea($idea)
	{
		$this->idea = $idea;
	}

	public function setCommand($command)
	{
		$this->command = $command;
	}

	/**
	 * Get base template
	 *
	 * @param  string $name
	 * @return string $template
	 */
	protected function getTemplate($name)
	{
		// If it's not in this list, you can't load it.
		$allowed = array(
			'controller', 'lang', 'migration', 'model', 'routes', 'test',
			'view.create', 'view.edit', 'view.index', 'view.show',
			'field.checkbox', 'field.date', 'field.hidden', 'field.select', 'field.text', 'field.text-sm', 'field.textarea',	
		);

		// Check to see if you're loading a valid template
		if (!in_array($name, $allowed))
		{
			$this->command->error("Sorry, but '{$name}' is not a valid template name!");
			return false;
		}

		// Load template and return contents as string
		$template = \File::get($this->templatePath . $name);
		return $template;
	}

}

// end /Primathon/Idea/Generators/BaseGenerator.php
