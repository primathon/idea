<?php namespace Primathon\Idea;

use \Primathon\Idea\Model;
use \Primathon\Idea\Field;
use \Primathon\Idea\Exception\ParseError;

class Parser {

	public $model;
	public $currentLine = 0;

	protected $modelParser;
	protected $fieldParser;

	/**
	 * Parser constructor
	 *
	 * defines $this->model as Idea\Model instance
	 */
	public function __construct(
//		\Primathon\Idea\Parser\FieldParser $fieldParser,
//		\Primathon\Idea\Parser\ModelParser $modelParser
	)
	{
//		$this->fieldParser = $fieldParser;
//		$this->modelParser = $modelParser;
//		$this->modelParser = new Parser\ModelParser;
//		$this->fieldParser = new Parser\FieldParser;
		$this->model       = new Model;
	}

	public function parse($file)
	{
		// Track the current line for showing errors
		$this->currentLine = 0;

		// Replace Windows line endings with Linux newlines
		// Explode file into different lines
		$file = str_replace("\r\n", "\n", $file);
		$lines = explode("\n", $file);

		foreach ($lines as $line)
		{
			// Increment current line count
			$this->currentLine++;

			// Ignore blank lines
			if (!trim($line)) continue;

			// Ignore comments
			if (substr($line, 0, 2) == '//') continue;

			$this->parseLine($line);
		}

		// Give me my instance back
		return $this->model;
	}

	public function parseLine($line)
	{
		// If line starts with no spaces, it's a model definition
		if (!preg_match('/^\s/', $line))
		{
			try
			{
				$this->parseModel($line);
			}
			catch (ParseError $e)
			{
				throw new ParseError("[Line $this->currentLine] " . $e->getMessage());
			}
		}
		// Line starts with spaces; it's a field definition
		else
		{
			// Ignore indented comments
			$line = trim($line);
			if (substr($line, 0, 2) != '//')
			{
				try
				{
					$this->parseField($line);
				}
				catch (ParseError $e)
				{
					throw new ParseError("[Line $this->currentLine] " . $e->getMessage());
				}
			}
		}
	}

	public function parseModel($line)
	{
		// Gather model metadata
		$modelData = explode(';', trim($line));
		foreach ($modelData as $i => $val)
		{
			$modelData[$i] =  trim($val);
			if (!$modelData[$i]) unset ($modelData[$i]);
		}

		// First segment is ALWAYS in the format "ModelName table_name"
		$metaData = explode(' ', $modelData[0]);
		if (count($metaData) < 2)
		{
			throw new ParseError("[Line $this->currentLine] Model does not include a name and table");
		}
		$this->model->modelName = $metaData[0];
		$this->model->tableName = $metaData[1];
		unset($modelData[0]);

		// Loop through the rest of the segments:
		// >> timestamps | softDeletes | route "routes.path" | views "views/path"
		foreach ($modelData as $i => $segment)
		{
			// Handle model metadata for timestamps and softDeletes
			if ($segment == 'timestamps')
			{
				$this->model->timestamps = true;
			}
			else if ($segment == 'softDeletes')
			{
				$this->model->softDeletes = true;
			}
			else 
			{
				// Segments follow the pattern { key "value" }
				$data = explode(' ', $segment);
				$key = $data[0];
				$val = trim($data[1], '"\'');

				switch ($key)
				{
					// Path to use for routes file and Controller redirection
					case 'route':
						$val = str_replace('/', '.', $val);
						$this->model->routesPath = $val;
						break;

					// Path to use for view loading in Controller
					case 'views':
						$val = str_replace('/', '.', $val);
						$this->model->viewsPath = $val;
						break;

					// Fallback condition
					default:
						break;
				}
			}
		}
	}

	public function parseField($line)
	{
		// Create new field object
		$field = new \Primathon\Idea\Field;

		// Tidy up the line, split segments up by semicolons
		$line = trim($line);
		$segments = $this->getLineSegments($line);

		// Determine field name; should be the absolute first thing defined
		// >> what_exactly text; rules "required|alpha"; label "What exactly are you measuring?"
		// >> project_id integer hidden; ??
		$fieldMeta = explode(" ", $segments[0]);
		$field->name = $fieldMeta[0];

		// timestamps/softDeletes detected; shouldn't be in fields list, but don't break if they are
		if (in_array($field->name, array('timestamps', 'softDeletes')))
		{
			if ($field->name == 'timestamps')
			{
				$this->model->timestamps = true;
			}
			else if ($field->name == 'softDeletes')
			{
				$this->model->softDeletes = true;
			}
		}

		// Normal field
		else
		{
			// Check for validity of the type field
			$allowed_types = array(
				'increments', 'integer', 'bigInteger', 'smallInteger', 'float', 'decimal',
				'string', 'text', 'date', 'dateTime', 'time', 'timestamp',
				'boolean', 'binary', 'enum',
				);
			if (!in_array($fieldMeta[1], $allowed_types))
			{
				throw new ParseError("Invalid field type: {$fieldMeta[1]}");
			}

			// Assign field type
			$field->type = $fieldMeta[1];

			// TODO: fieldMeta may also hold: 
			// name type size hidden nullable unsigned
			/*
			 field types
				string('email', 100)
				enum('sizes', array('sm', 'med', 'lg'))
				double('column', 15, 8)
				decimal('amount', 6, 2)

			And the following field modifiers are supported:
				default "value"
				nullable
				unsigned
				primary
				fulltext
				unique
				index
	
			type enum "admin", "moderator", "user"
			type enum admin, moderator, user
			 */

			// Loop through semicolon-delimited field segments
			foreach ($segments as $segment)
			{
				// Break segment into component pieces
				$fieldData = explode(" ", $segment);
				$key = $fieldData[0];
				$val = trim(str_replace($key, '', $segment));
				$val = trim($val, '"');

				// As you move through the segments, what are you looking at?
				switch ($key) {

					// set field "default" property
					case 'default':
						$field->default = $val;
						break;

					// set form placeholder value
					case 'placeholder':
						$field->placeholder = $val;
						break;

					// set model validation rules
					case 'rules':
						$field->rules = $val;
						break;

					// set form label value
					case 'label':
						$field->label = $val;
						break;

					// fallback condition
					default:
						break;
				}
			}

			// Assign assembled field object to data model
			$this->model->fields[$field->name] = $field;
		}
	}

	/**
	 * Returns an array of commands found in a line that are separated
	 * by a semicolon. Though the first segment is always about the field,
	 * the subsequent segments are field modifiers that specify things like
	 * default value, nullable, and other field properties.
	 *
	 * @param string $line The complete field line
	 *
	 * @return array An array of all segments as strings
	 */
	public static function getLineSegments($line)
	{
		// We have to take care of semicolons appearing inside quotes
		// and stuffs like that

		// Add imaginary semicolons at the beginning and the end of the
		// string for reference purpose
		$line = ";{$line};";

		// Some C style parsing to the rescue
		$insideQuotes = false;
		$semicolonPositions = array();

		$length = strlen($line);
		for ($i = 0; $i < $length; $i++)
		{
			// If we have either of the quote character, set insideQuotes
			// to that quote character. We need to keep track of which
			// character started to quote so that the same character
			// dequotes it as well
			if (in_array($line[$i], array('"', "'")))
			{
				// If a quote is open and it is equal to the current
				// quote character, then dequote
				if ($insideQuotes and ($insideQuotes == $line[$i]))
				{
					$insideQuotes = false;
				}
				// Else if it's a quote and we are not inside a quote yet
				else
				{
					$insideQuotes = $line[$i];
				}

				// In both cases, go to next character
				continue;
			}

			// If we have a semicolon, push its position only if we are not
			// inside a quote
			if (!$insideQuotes and ($line[$i] == ';'))
			{
				$semicolonPositions[] = $i;
			}
		}

		// Initialize a list of segments
		$segments = array();

		// Now that we have the position of all semicolons that are actual
		// separators, we'll explode the string manually at those points
		$semicolonPositionsCount = count($semicolonPositions);
		for ($i = 1; $i < $semicolonPositionsCount; $i++)
		{
			// The start of the substring excluding the semicolon
			$s = $semicolonPositions[$i - 1] + 1;

			// the length of the substring ignoring the semicolon
			$l = $semicolonPositions[$i] - $s;

			// Get the substring and trim it, and add it to the list
			// of segments
			$subs = trim( substr($line, $s, $l) );

			// To filter out blank substrings
			if ($subs) $segments[] = $subs;
		}

		return $segments;

	} // end function getLineSegments($line)


}

// end file
