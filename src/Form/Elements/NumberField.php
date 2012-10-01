<?php

class Form_Elements_NumberField extends Form_Element
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
				'min' => 'is_numeric',
				'max' => 'is_numeric',
				'value' => 'is_numeric',
				'required' => 'is_bool',
				'readonly' => 'is_bool',
				'disabled' => 'is_bool',
				'allowDecimal' => 'is_bool',
				'defaultValue' => 'is_numeric'
			)
		);
		parent::__construct($configuration, $form);
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
		if(isset($this->min) and $this->min>0 and $value<$this->min)
		{
			$errors['min'] = json_encode(array(
				'msg' => "minimum value is %s",
				'arguments' => array($this->min)
			));
		}
		if(isset($this->max) and $this->max>0 and $value>$this->max)
		{
			$errors['max'] = json_encode(array(
				'msg' => "maximum value is $s",
				'arguments' => array($this->max)
			));
		}
		if(isset($this->regex) and !preg_match($this->regex, $value))
		{
			$errors['pattern'] = 'value does not match the pattern';
		}
		if(isset($this->defaultValue) and !empty($this->readonly) and $value!=$this->defaultValue)
		{
			$errors['readonly'] = 'this element is readonly';
		}
		return $errors;
	}
}
