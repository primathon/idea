<?php namespace Primathon\Idea\Command;

use \Illuminate\Filesystem\File;
use \Illuminate\Support\Pluralizer;
use \Primathon\Idea\Command\BaseCommand;

class Views extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:views';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Write view files (index / create / edit / show)';

	/**
	 * Generate migration file
	 *
	 * @param $model
	 * @return void
	 */
	protected function runCommand($idea)
	{

		$this->generateCreateView($idea);

	}

	private function generateCreateView($idea) 
	{

		// TODO: allow overwrite view path		
//		$this->info("views: " . $idea->viewsPath);

		if (empty($idea->viewsPath))
		{
			$idea->viewsPath = '/views/' . $idea->tableName;
		}
		$idea->viewsPath  = str_replace('.', '/', $idea->viewsPath);

		if (empty($idea->routesPath))
		{
			$idea->routesPath = $idea->tableName;
		}
		$idea->routesPath  = str_replace('/', '.', $idea->routesPath);

		// Check for existing create view
		$files = \File::files(app_path() . $idea->viewsPath);
		$search = 'create.blade.php';

		$overwrite = false;
		foreach ($files as $filePath)
		{
			$file = explode('/', $filePath);
			$file = end($file);

			if ($file == $search)
			{
				$question = "It appears that this view already exists! ({$idea->viewsPath}/{$file}) -- do you want to overwrite it?";
				$this->error($question);

				// Overwrite?
				if ($this->confirm('[yes|no] >> '))
				{
					$fileName = str_replace(app_path(), '', $filePath);
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

		// load create view template
		$template = \File::get($this->templatePath . 'view.create');

		$tableName = $idea->tableName;

		$fields = '';
		foreach ($idea->fields as $field)
		{
			switch ($field->type) {

				// Hidden auto-increment field
				case 'increments':
					$fieldTemplateName = 'field.hidden';
					break;

				// Numbers and whatnot
				case 'integer':
				case 'bigInteger':
				case 'smallInteger':
				case 'float':
				case 'decimal':
					$fieldTemplateName = 'field.text-sm';
					break;

				// Standard text entry
				case 'string':
					$fieldTemplateName = 'field.text';
					break;

				// Large textarea entry
				case 'text':
					$fieldTemplateName = 'field.textarea';
					break;

				// Date/time entries
				case 'date':
				case 'dateTime':
				case 'time':
				case 'timestamp':
					$fieldTemplateName = 'field.date';
					break;

				// Checkbox
				case 'boolean':
					$fieldTemplateName = 'field.checkbox';
					break;

				// Select Dropdown
				case 'enum':
					$fieldTemplateName = 'field.select';
					break;

				// IGNORE BINARY
				case 'binary':
					$fieldTemplateName = ''; // SKIP
					break;

				// Fallback
				default:
					$fieldTemplateName = 'field.text';
					break;
			}

			// Load the field template, or duck out
			$fieldTemplatePath = $this->templatePath . $fieldTemplateName;
			if (!\File::exists($fieldTemplatePath))
			{
				continue;
			}
			$fieldTemplate = \File::get($fieldTemplatePath);

			// Define your replacement matrix
			$matrix = array(
				'fieldName'   => $field->name,
				'fieldLabel'  => $field->label,
				'fieldType'   => $field->type,
				'langKey'     => $idea->tableName . '.' . $field->name,
				'placeholder' => $field->placeholder,
			);

			foreach ($matrix as $key => $value)
			{
				$fieldTemplate = str_replace('{{' . $key . '}}', $value, $fieldTemplate);
			}

			$fields .= $fieldTemplate;
		}
		$fields = "\n" . trim($fields);

		$title = "Creating new {$idea->modelName}";

		// Assign route to Store method
		$routePath = $idea->routesPath . '.store';

		// Dynamic search/replace in template file
		$vars = array(
			'tableName', 'fileName', 'routePath', 'fields', 'title',
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

		\File::put(app_path() . '/' . $fileName, $template);

	}


}
