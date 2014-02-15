<?php
namespace Primathon\Idea\Generators;

use Primathon\Idea\Writer\Writer;
use \Illuminate\Support\Pluralizer as Pluralizer;

class MigrationGenerator extends BaseGenerator {

	// generate migration output from template file
	public function generate()
	{
		// Load Idea file into Parser and generate a parsed Migration
		$this->parser->parse($this->idea);
		$this->model = $this->parser->model;

		// Get the template we'll be working with
		$template = $this->getTemplate('migration');

		// Get your paths figured out
		$fileName  = date('Y_m_d_His') . '_create_' . $this->model->tableName . '_table.php';
		$filePath = app_path() . $this->outputPaths['migrations'] . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['migrations'] . $fileName));

		// 'create table' class name
		$className = 'Create' . \Str::studly($this->model->tableName) . 'Table';
		$tableName = $this->model->tableName;

		// build fields string
		$fields = '';
		foreach ($this->model->fields as $field)
		{
			$fields .= '$table->' . $field->type . "('" . $field->name . "');\n\t\t\t";
		}
		$fields = trim($fields);

		// fill template with string data
		$vars = array(
			'className', 'tableName', 'fields', 'appPath',
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


