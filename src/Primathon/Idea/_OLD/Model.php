<?php namespace Primathon\Idea;

use \Primathon\Idea\Exception\ParseError;

class Model {

	/**
     * The name of the model, camelcase singular
     * @var string
     */
    public $modelName = '';

    /**
     * The table name of the model, lowercase plural
     * @var string
     */
    public $tableName = '';

    /**
	 * Indicates if the model should be timestamped
	 * (adds 'created_at' and 'updated_at' columns)
     * @var boolean
     */
    public $timestamps = true;

    /**
	 * Indicates if the model should soft delete
	 * (adds 'deleted_at' column)
     * @var boolean
     */
    public $softDeletes = false;

    /**
     * The primary key column name that is an integer auto increment. Default 'id'
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * The path that will be used in the Controller when redirecing to various routes
     * @var string
     */
	public $routesPath = '';
	
    /**
	 * The path that will be used in the Controller when loading various views
	 * Also, the path where view files will be stored upon generation
     * @var string
     */
	public $viewsPath  = '';

    /**
	 * Indicates if the IDs are auto-incrementing
     * @var boolean
     */
	public $incrementing = true;

	/**
	 * Collection of model Field objects
	 * @var array of objects
	 */
	public $fields = array();

}

// end Primathon/Idea/Model.php
