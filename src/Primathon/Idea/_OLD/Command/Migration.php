<?php namespace Primathon\Idea\Command;

use \Illuminate\Filesystem\File;
use \Primathon\Idea\Command\BaseCommand;

class Migration extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'idea:migration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Write migration file';

	/**
	 * Generate migration file
	 *
	 * @param $idea
	 * @return void
	 */
	protected function runCommand($idea)
	{
		// Check for existing 'create_table' migration
		$files = \File::files(app_path() . '/database/migrations');

		$search = 'create_' . $idea->tableName . '_table';

		$overwrite = false;
		foreach ($files as $filePath)
		{
			if (strstr($filePath, $search) !== false)
			{
				$file = explode('/', $filePath);
				$file = end($file);
				$question = "A migration to create this table already exists! ({$file}) -- do you want to overwrite it?";
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

		// load migration template
		$template = \File::get($this->templatePath . 'migration');

		// 'create table' class name
		$className = 'Create' . \Str::studly($idea->tableName) . 'Table';
		$tableName = $idea->tableName;

		// You have the option to overwrite an existing file above
		if (!isset($fileName))
		{
			$fileName  = 'app/database/migrations/' . date('Y_m_d_His') . '_create_' . $idea->tableName . '_table.php';
		}

		// build fields string
		$fields = '';

		// Does this table include the auto timestamps?
		if ($idea->timestamps)
		{
			$fields .= '$table->timestamps();' . "\n\t\t\t";
		}

		// How about soft deleting functionality?
		if ($idea->softDeletes)
		{
			$fields .= '$table->softDeletes();' . "\n\t\t\t";
		}

		// Parse through fields list
		foreach ($idea->fields as $field)
		{
			$fields .= '$table->' . $field->type . "('" . $field->name . "');\n\t\t\t";
		}
		$fields = trim($fields);

		// fill template with string data
		$vars = array(
			'className', 'tableName', 'fileName', 'fields',
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
