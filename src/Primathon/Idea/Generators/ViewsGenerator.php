<?php
namespace Primathon\Idea\Generators;

use Primathon\Idea\Writer\Writer;
use \Illuminate\Support\Pluralizer as Pluralizer;

// TODO: add error display somewhere
// TODO: add cancel buttons where appropriate

class ViewsGenerator extends BaseGenerator {

	//-------------------------------------------------------------------------------------------------
	// INDEX VIEW
	private function generateIndexView()
	{
		$template = $this->getTemplate('view.index');

		$model  = \Str::lower(Pluralizer::singular($this->model->modelName));
		$models = \Str::lower(Pluralizer::plural($this->model->modelName));
		$Models = Pluralizer::plural($this->model->modelName);
		$Model  = $this->model->modelName;

		$title = "Listing all {$Models}";

		$routesPath = $this->model->routesPath;
		$primaryKey = $this->model->primaryKey;

		// Get your paths figured out
		$fileName = 'index.blade.php';
		$filePath = app_path() . $this->outputPaths['views'] . $this->model->viewsPath . '/' . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['views'] . $fileName));

		// fill template with string data
		$vars = array(
			'title', 'routesPath', 'model', 'models', 'Models', 'Model', 'primaryKey',
		);
		foreach ($vars as $var)
		{
			$template = str_replace('{{' . $var . '}}', $$var, $template);
		}

		// Create Writer class and output file
		$writer = new Writer($this->command);
		$writer->writeFile($filePath, $template);

	}

	//-------------------------------------------------------------------------------------------------
	// EDIT VIEW
	private function generateEditView()
	{
		$template = $this->getTemplate('view.edit');
		$model  = \Str::lower(Pluralizer::singular($this->model->modelName));
		$models = \Str::lower(Pluralizer::plural($this->model->modelName));
		$Models = Pluralizer::plural($this->model->modelName);
		$Model  = $this->model->modelName;

		$fields = '';
		foreach ($this->model->fields as $field)
		{
			$fieldTemplateName = $this->getFieldTemplateName($field->type);
			$fieldTemplate = $this->getTemplate($fieldTemplateName);

			if ($fieldTemplate)
			{
				// Define your replacement matrix
				$matrix = array(
					'fieldName'   => $field->name,
					'fieldLabel'  => $field->label,
					'fieldType'   => $field->type,
					'langKey'     => $this->model->tableName . '.' . $field->name,
					'placeholder' => $field->placeholder,
				);

				foreach ($matrix as $key => $value)
				{
					$fieldTemplate = str_replace('{{' . $key . '}}', $value, $fieldTemplate);
				}

				$fields .= $fieldTemplate;
			}
		}
		$fields = trim($fields);

		$title = "Editing existing {$this->model->modelName}";

		$routesPath = $this->model->routesPath;
		$primaryKey = $this->model->primaryKey;

		// Get your paths figured out
		$fileName = 'edit.blade.php';
		$filePath = app_path() . $this->outputPaths['views'] . $this->model->viewsPath . '/' . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['views'] . $fileName));

		// fill template with string data
		$vars = array(
			'title', 'fields', 'routesPath', 'appPath', 'model', 'primaryKey', 'Model', 'models',
		);
		foreach ($vars as $var)
		{
			$template = str_replace('{{' . $var . '}}', $$var, $template);
		}

		// Create Writer class and output file
		$writer = new Writer($this->command);
		$writer->writeFile($filePath, $template);

	}


	//-------------------------------------------------------------------------------------------------
	// CREATE VIEW
	private function generateCreateView()
	{
		$template = $this->getTemplate('view.create');
		$model  = \Str::lower(Pluralizer::singular($this->model->modelName));
		$models = \Str::lower(Pluralizer::plural($this->model->modelName));
		$Models = Pluralizer::plural($this->model->modelName);
		$Model  = $this->model->modelName;

		$fields = '';
		foreach ($this->model->fields as $field)
		{
			$fieldTemplateName = $this->getFieldTemplateName($field->type);
			$fieldTemplate = $this->getTemplate($fieldTemplateName);

			// Define your replacement matrix
			$matrix = array(
				'fieldName'   => $field->name,
				'fieldLabel'  => $field->label,
				'fieldType'   => $field->type,
				'langKey'     => $this->model->tableName . '.' . $field->name,
				'placeholder' => $field->placeholder,
			);

			foreach ($matrix as $key => $value)
			{
				$fieldTemplate = str_replace('{{' . $key . '}}', $value, $fieldTemplate);
			}

			$fields .= $fieldTemplate;
		}
		$fields = trim($fields);

		$title = "Creating new {$this->model->modelName}";

		$routesPath = $this->model->routesPath;

		// Get your paths figured out
		$fileName = 'create.blade.php';
		$filePath = app_path() . $this->outputPaths['views'] . $this->model->viewsPath . '/' . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['views'] . $fileName));

		// fill template with string data
		$vars = array(
			'title', 'fields', 'routesPath', 'appPath', 'Model', 'models',
		);
		foreach ($vars as $var)
		{
			$template = str_replace('{{' . $var . '}}', $$var, $template);
		}

		// Create Writer class and output file
		$writer = new Writer($this->command);
		$writer->writeFile($filePath, $template);

	}

	//-------------------------------------------------------------------------------------------------
	// SHOW VIEW
	private function generateShowView()
	{
		$template = $this->getTemplate('view.show');

		$model  = \Str::lower(Pluralizer::singular($this->model->modelName));
		$models = \Str::lower(Pluralizer::plural($this->model->modelName));
		$Models = Pluralizer::plural($this->model->modelName);
		$Model  = $this->model->modelName;

		$fields = '';
		foreach ($this->model->fields as $field)
		{
			if ($field->name != $this->model->primaryKey)
			{
				$fieldTemplateName = $this->getFieldTemplateName($field->type);
				$fieldTemplate = <<<TEMPLATE_HTML

<li class="list-group-item">
	<h4 class="list-group-item-heading">{{ Lang::get('{{langKey}}') }}</h4>
	<p class="list-group-item-text">{{ \${{model}}->$field->name }}</p>
</li>

TEMPLATE_HTML;

				// Define your replacement matrix
				$matrix = array(
					'model'       => $model,
					'fieldName'   => $field->name,
					'fieldLabel'  => $field->label,
					'fieldType'   => $field->type,
					'langKey'     => $this->model->tableName . '.' . $field->name,
					'placeholder' => $field->placeholder,
				);

				foreach ($matrix as $key => $value)
				{
					$fieldTemplate = str_replace('{{' . $key . '}}', $value, $fieldTemplate);
				}

				$fields .= $fieldTemplate;
			}
		}

		$fields = trim($fields);

		$title = "Viewing {$this->model->modelName}";

		$routesPath = $this->model->routesPath;
		$primaryKey = $this->model->primaryKey;

		// Get your paths figured out
		$fileName = 'show.blade.php';
		$filePath = app_path() . $this->outputPaths['views'] . $this->model->viewsPath . '/' . $fileName;
		$appPath = str_replace(base_path(), '', (app_path() . $this->outputPaths['views'] . $fileName));

		// fill template with string data
		$vars = array(
			'title', 'fields', 'routesPath', 'appPath', 'model', 'primaryKey', 'Model', 'models',
		);
		foreach ($vars as $var)
		{
			$template = str_replace('{{' . $var . '}}', $$var, $template);
		}

		// Create Writer class and output file
		$writer = new Writer($this->command);
		$writer->writeFile($filePath, $template);

	}



	//-------------------------------------------------------------------------------------------------

	// generate create view output from template file
	public function generate()
	{
		// Load Idea file into Parser and generate a parsed View
		$this->parser->parse($this->idea);
		$this->model = $this->parser->model;

		$this->generateIndexView();
		$this->generateCreateView();
		$this->generateEditView();
		$this->generateShowView();

	}

}



