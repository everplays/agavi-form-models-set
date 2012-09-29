<?php

class Form_Elements_TextField extends Form_Element
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
				'min' => 'is_int',
				'max' => 'is_int',
				'value' => 'is_string',
				'required' => 'is_bool',
				'readonly' => 'is_bool',
				'disabled' => 'is_bool',
				'defaultValue' => 'is_string'
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
		if(isset($this->min) and $this->min>0 and mb_strlen($value)<$this->min)
		{
			$errors['min'] = json_encode(array(
				'msg' => "at least %s characters is required",
				'arguments' => array($this->min)
			));
		}
		if(isset($this->max) and $this->max>0 and mb_strlen($value)>$this->max)
		{
			$errors['max'] = json_encode(array(
				'msg' => "maximum characters length is %s",
				'arguments' => array($this->max)
			));
		}
		if(!empty($this->value))
		{
			if(isset($this->regex))
			{
				if(substr($this->regex, 0, 1)!==substr($this->regex, -1, 1))
				{
					if(strpos($this->regex, '/')===false)
						$this->regex = '/'.$this->regex.'/';
					elseif(strpos($this->regex, '~')===false)
						$this->regex = '~'.$this->regex.'~';
				}
			}
			if(isset($this->regex) and !preg_match($this->regex, $value))
			{
				$errors['pattern'] = 'value does not match the pattern';
			}
		}
		if(isset($this->defaultValue) and !empty($this->readonly) and $value!=$this->defaultValue)
		{
			$errors['readonly'] = 'this element is readonly';
		}
		return $errors;
	}
}
