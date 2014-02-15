<?php namespace Primathon\Idea\Command;

use \Illuminate\Filesystem\File;
use \Illuminate\Support\Pluralizer;
use \Primathon\Idea\Command\BaseCommand;

class Controller extends BaseCommand {

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
	protected $description = 'Write controller file';

	/**
	 * Generate migration file
	 *
	 * @param $model
	 * @return void
	 */
	protected function runCommand($idea)
	{
		// Check for existing idea Controller
		$files = \File::files(app_path() . '/controllers');

		$search = Pluralizer::plural($idea->modelName) . 'Controller.php';

		$overwrite = false;
		foreach ($files as $filePath)
		{
			$file = explode('/', $filePath);
			$file = end($file);

			if ($file == $search)
//			if (strstr($filePath, $search) !== false)
			{
				$question = "It appears that this controller already exists! ({$file}) -- do you want to overwrite it?";
				$this->error($question);

				// Overwrite?
				if ($this->confirm('[yes|no] >> '))
				{
					$fileName = $filePath;
					$overwrite = true;
				}

				// Don't overwrite
				else
				{
					$this->info('Aborting...');
					return true;
				}
			}
		}

		// load controller template
		$template = \File::get($this->templatePath . 'controller');

		$className = Pluralizer::plural($idea->modelName) . 'Controller';

		$model  = \Str::lower(Pluralizer::singular($idea->modelName));
		$models = \Str::lower(Pluralizer::plural($idea->modelName));
		$Models = Pluralizer::plural($idea->modelName);
		$Model  = $idea->modelName;

		if (!isset($idea->routesPath)) { $idea->routesPath = $models; }
		if (!isset($idea->viewsPath))  { $idea->viewsPath  = $models; }

		$routesPath = $idea->routesPath;
		$viewsPath  = $idea->viewsPath;

		$primaryKey = $idea->primaryKey;

		$fileName = 'app/controllers/' . $Models . 'Controller.php';

		// fill template with string data
		$vars = array(
			'className', 'Model', 'model', 'Models', 'models', 'fileName', 'routesPath', 'viewsPath', 'primaryKey',
		);
		foreach ($vars as $var)
		{
			$template = str_replace('{{' . $var . '}}', $$var, $template);
		}

		// Are you overwriting an existing file?
		if ($overwrite)
		{
			$this->info("Overwriting {$fileName}");
		}
		else
		{
			$this->info("Successfully created {$fileName}");
		}

		\File::put($fileName, $template);

	}

}
