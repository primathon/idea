<?php
namespace Primathon\Idea\Generators;

use Primathon\Idea\Writer\Writer;
use \Illuminate\Support\Pluralizer as Pluralizer;

class ControllerGenerator extends BaseGenerator {

	// generate controller output from template file
	public function generate()
	{
		// Load Idea file into Parser and generate a parsed Controller
		$this->parser->parse($this->idea);
		$this->model = $this->parser->model;

		// Get the template we'll be working with
		$template = $this->getTemplate('controller');

		$className = Pluralizer::plural($this->model->modelName) . 'Controller';

		$model  = \Str::lower(Pluralizer::singular($this->model->modelName));
		$models = \Str::lower(Pluralizer::plural($this->model->modelName));
		$Models = Pluralizer::plural($this->model->modelName);
		$Model  = $this->model->modelName;

		if (empty($this->model->routesPath)) { $this->model->routesPath = $models; }
		if (empty($this->model->viewsPath))  { $this->model->viewsPath  = $models; }

		$routesPath = $this->model->routesPath;
		$viewsPath  = $this->model->viewsPath;

		$primaryKey = $this->model->primaryKey;

		// Get your paths figured out
		$fileName = $className . '.php';
		$filePath = app_path() . $this->outputPaths['controllers'] . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['controllers'] . $fileName));

		// fill template with string data
		$vars = array(
			'className', 'Model', 'model', 'Models', 'models', 'routesPath', 'viewsPath', 'primaryKey', 'appPath',
		);
		foreach ($vars as $var)
		{
			$template = str_replace('{{' . $var . '}}', $$var, $template);
		}

		// Create Writer class and output file
		$writer = new Writer($this->command);
		$writer->writeFile($filePath, $template);

	}

}

