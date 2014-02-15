<?php
namespace Primathon\Idea\Generators;

use Primathon\Idea\Writer\Writer;
use \Illuminate\Support\Pluralizer as Pluralizer;

class LangGenerator extends BaseGenerator {

	// generate lang output from template file
	public function generate()
	{
		// Load Idea file into Parser and generate a parsed Lang
		$this->parser->parse($this->idea);
		$this->model = $this->parser->model;

		// Get the template we'll be working with
		$template = $this->getTemplate('lang');

		// Get your paths figured out
		$fileName = $this->model->tableName . '.php';
		$filePath = app_path() . $this->outputPaths['lang'] . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['lang'] . $fileName));
	
		// Pretty-format by aligning all the array definitions
		$len = 0;
		foreach ($this->model->fields as $field)
		{
			if ($field->name != $this->model->primaryKey)
			{
				$len = max(strlen($field->name), $len);
			}
		}
		$len += 2;

		// Generate array content for language file
		$fields = '';
		foreach ($this->model->fields as $field)
		{
			if ($field->name != $this->model->primaryKey)
			{
				$name = str_pad("'".$field->name."'", $len, ' ');
				$value = (!empty($field->label)) ? $field->label : $field->name ;
				$fields .= $name . " => '" . addslashes($value) . "',\n\t";
			}
		}
		$fields = trim($fields);

		// Dynamic search/replace in template file
		$vars = array(
			'fields', 'appPath',
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


