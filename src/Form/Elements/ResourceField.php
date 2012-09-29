<?php

class Form_Elements_ResourceField extends Form_Element
{
	/**
	 * constructs element
	 */
	public function __construct($configuration=null, Form_Elements_Fieldset $form=null)
	{
		$this->configDefinition = array_merge(
			$this->configDefinition,
			array(
				'regex' => 'is_string',
				'value' => 'is_int',
				'required' => 'is_bool',
				'readonly' => 'is_bool',
				'disabled' => 'is_bool',
				'defaultValue' => 'is_int',
				'depends' => 'is_array',
				'source' => __CLASS__.'::is_source',
				'params' => 'is_object'
			)
		);
		parent::__construct($configuration, $form);
	}

	/**
	 * checks type of source
	 *
	 * @param mixed $source
	 * @return bool $source could be string or array
	 */
	public static function is_source($source)
	{
		return is_string($source) or is_array($source);
	}

	/**
	 * returns validation errors
	 *
	 * @param mixed $value value that must be validated
	 * @return array assocc-array of errors (empty array on success)
	 */
	public function getValidationErrors($value)
	{
		$errors = array();
		if($this->required===true and !isset($value))
		{
			$errors['required'] = 'this element is required';
		}
		if(isset($this->defaultValue) and !empty($this->readonly) and $value!=$this->defaultValue)
		{
			$errors['readonly'] = 'this element is readonly';
		}
		return $errors;
	}

	/**
	 * prepares value before regular check
	 *
	 * @param mixed $value
	 * return int
	 */
	public function setValue($value)
	{
		$value = (int) $value;
		$value = parent::setValue($value);
		return $value;
	}
}
