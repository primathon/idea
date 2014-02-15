<?php namespace Primathon\Idea;

class Writer
{
    /**
     * An associative array defining the paths to all elements of the Generators package
     * @var array
     */
	private $paths;

    /**
     * Initialize the model and migration path
     * @param string $modelPath     The path to the model folder with trailing slash
     * @param string $migrationPath The path to the migration folder with trailing slash
     */
    public function __construct($paths)
	{
		$this->paths = $paths;
    }

    /**
     * Write a laravel migration file
     * @param  string $migrationContent  Content of the migration file
     * @param  string $migrationFilename Filename of the migration file
     */
    public function writeMigration($content, $filename)
    {
        file_put_contents($this->paths['migrations'] . $filename, $content);
    }

} 
