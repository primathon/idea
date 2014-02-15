<?php namespace Primathon\Idea\Writer;

use Illuminate\Filesystem as File;
use Illuminate\Console\Command as Command;

class Writer {

	/**
	 * Array of application paths to store files on disk
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * Illuminate Filesystem instance
	 *
	 * @var Illuminate\Filesystem
	 */
	protected $file;

	/**
	 * Illuminate Console Command instance
	 *
	 * @var Illuminate\Console\Command
	 */
	protected $command;

	/**
	 * Writer constructor
	 *
	 * sets $this->file as Filesystem instance
	 * sets $this->command as Command instance
	 * sets $this->paths as array of output location paths
	 */
	public function __construct($command)
	{
		$this->file    = new \File;
		$this->command = $command;
	}


	/**
	 * Write Model file
	 *
	 * @param  $path [optional] string
	 * @param  $fileName        string
	 * @param  $content         string
	 * @return void
	 */
//	public function writeModel($fileName, $content)
//	{
//		$path = $this->paths['models'];
//		$this->writeFile($path . $fileName, $content);
//	}


	/**
	 * Write file to disk
	 *
	 * @param  $path    string
	 * @param  $content string
	 * @return void
	 */
	public function writeFile($filePath, $content)
	{
		// Get short version of the intended write path
		$appPath = str_replace(base_path(), '', $filePath);

		$no = true;

		// Okay, Migrations work differently than all other files.
		if (strstr($filePath, '/database/migrations/'))
		{
			// Split the intended filePath into component parts; strip the date and leave only the 'create_{$tablename}_table' string
			$path = pathinfo($filePath);
			$migration = $path['basename'];
			$search = substr($migration, strpos($migration, 'create_'));

			// Look through Migrations directory for an existing 'create_table' file
			$overwrite = false;
			$files = \File::files(app_path() . '/database/migrations');
			foreach ($files as $file)
			{
				// If you find a match, prompt the user to overwrite it
				if (strstr($file, $search))
				{
					$appPath = str_replace(base_path(), '', $file);
					$this->command->error("CAUTION: {$appPath} already exists! Do you want to overwrite it?");
					
					// If they want to overwrite, reassign the filename and update the migration template
					if ($this->command->confirm('[y|n]'))
					{
						// Update the template content
						$search = str_replace(base_path(), '', $filePath);
						$replace = str_replace(base_path(), '', $file);
						$content = str_replace($search, $replace, $content);

						// Update the filename we're writing to
						$filePath = $file;
						$appPath = str_replace(base_path(), '', $filePath);
					}
					// No overwrite; abort
					else
					{
						$this->command->info('Aborting...');
						return false;
					}

				}
			}

		}
		// Normal files don't need to do any particularly special processing; just search for dupes and prompt for overwrite
		else
		{
			// Does this already exist? Ask them if they want to overwrite it.
			if (\File::exists($filePath))
			{
				$this->command->error("CAUTION: {$appPath} already exists! Do you want to overwrite it?");
				if (!$this->command->confirm('[y|n]'))
				{
					$this->command->info('Aborting...');
					return false;
				}
			}
		}

		// Write file to disk
		if (\File::put($filePath, $content))
		{
			$this->command->info("Successfully wrote {$appPath}");
		}
		else
		{
			$this->command->error('There was a problem with the writeFile function!');
		}
	}

}
