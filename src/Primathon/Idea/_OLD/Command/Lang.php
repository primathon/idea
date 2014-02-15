<?php namespace Primathon\Idea\Command;

use \Illuminate\Filesystem\File;
use \Primathon\Idea\Command\BaseCommand;

class Lang extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:lang';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Write language file (default: en)';

	/**
	 * Generate migration file
	 *
	 * @param $idea
	 * @return void
	 */
	protected function runCommand($idea)
	{
		// Check for existing 'create_table' migration
		$files = \File::files(app_path() . '/lang/en');

		$search = $idea->tableName;

		$overwrite = false;
		foreach ($files as $filePath)
		{
			if (strstr($filePath, $search) !== false)
			{
				$file = explode('/', $filePath);
				$file = end($file);
				$question = "A language file for this model already exists! ({$file}) -- do you want to overwrite it?";
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

		// load migration template
		$template = \File::get($this->templatePath . 'lang');

		// Pretty-format by aligning all the array definitions
		$len = 0;
		foreach ($idea->fields as $field)
		{
			if ($field->name != $idea->primaryKey)
			{
				$len = max(strlen($field->name), $len);
			}
		}
		$len += 2;

		// Generate array content for language file
		$fields = '';
		foreach ($idea->fields as $field)
		{
			if ($field->name != $idea->primaryKey)
			{
				$name = str_pad("'".$field->name."'", $len, ' ');
				$value = (!empty($field->label)) ? $field->label : $field->name ;
				$fields .= $name . " => '" . addslashes($value) . "',\n\t";
			}
		}
		$fields = trim($fields);

		$tableName = $idea->tableName;
		$fileName  = 'lang/en/' . $tableName . '.php';

		// Dynamic search/replace in template file
		$vars = array(
			'tableName', 'fields', 'fileName',
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
