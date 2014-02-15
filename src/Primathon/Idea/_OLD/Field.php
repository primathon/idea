<?php namespace Primathon\Idea;

use \Primathon\Idea\Exception\ParseError;

class Field {

	/**
	 * Field name
	 * @var string
	 */
	var $name = '';

	/**
	 * Field type
	 * @var string
	 */
	var $type = '';

	/**
	 * Field size (optional)
	 * @var integer
	 */
	var $size = null;

	/**
	 * Can field hold null values (optional)
	 * @var boolean
	 */
	var $nullable = null;

	/**
	 * Signed or unsigned (optional, integer only)
	 * @var boolean
	 */
	var $unsigned = null;

	/**
	 * Field default value (optional)
	 * @var mixed
	 */
	var $default  = false;

	/**
	 * Field label for Lang file output (optional)
	 * @var string
	 */
	var $label    = false;

	/**
	 * Field validation rules, pipe-separated (optional)
	 * @var string
	 */
	var $rules    = false;

	/**
	 * Field placeholder for Form elements (optional)
	 * @var string
	 */
	var $placeholder = false;

}

// end Primathon/Idea/Field.php
