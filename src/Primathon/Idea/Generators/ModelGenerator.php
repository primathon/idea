<?php
namespace Primathon\Idea\Generators;

use Primathon\Idea\Writer\Writer;

class ModelGenerator extends BaseGenerator {

	// generate model output from template file
	public function generate()
	{
		// Load Idea file into Parser and generate a parsed Model
		$this->parser->parse($this->idea);
		$this->model = $this->parser->model;

		// Get the template we'll be working with
		$template = $this->getTemplate('model');

		// Define replacement variables
		$modelName   = $this->model->modelName;
		$tableName   = $this->model->tableName;
		$timestamps  = ($this->model->timestamps)  ? 'true' : 'false' ;
		$softDeletes = ($this->model->softDeletes) ? 'true' : 'false' ;
		$primaryKey  = 'id'; // TODO: extend this to handle other values from Model

		// Get your paths figured out
		$fileName = $this->model->modelName . '.php';
		$filePath = app_path() . $this->outputPaths['models'] . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['models'] . $fileName));

		$guarded  = '';
		$fillable = '';

		// Determine padding length
		$len = 0;
		foreach ($this->model->fields as $field)
		{
			if ($field->rules)
			{
				$len = max(strlen($field->name), $len);
			}
		}
		$len += 2;

		// TODO: validate against rules:
		// http://laravel.com/docs/validation#available-validation-rules
		$rules = "\n\t\t";
		foreach ($this->model->fields as $field)
		{
			if ($field->rules)
			{
				$name = str_pad("'".$field->name."'", $len, ' ');
				$value = (!empty($field->rules)) ? $field->rules : '' ;
				$rules .= $name . " => '" . $value . "',\n\t\t";
			}
		}

		// Dynamic search/replace in template file
		$vars = array(
			'modelName', 'tableName', 'timestamps', 'softDeletes', 'primaryKey', 'guarded', 'fillable', 'rules', 'appPath',
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
