<?php

if(!class_exists('Form_ElementModel'))
	require __DIR__'/../ElementModel.class.php';

class Form_Elements_CheckboxModel extends Form_ElementModel
{
	/**
	 * constructs element
	 */
	public function __construct($configuration=null, Form_Elements_FieldsetModel $form=null)
	{
		$this->configDefinition = array_merge(
			$this->configDefinition,
			array(
				'value' => 'is_bool',
				'required' => 'is_bool',
				'readonly' => 'is_bool',
				'disabled' => 'is_bool',
				'defaultValue' => 'is_bool',
				'description' => 'is_string'
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
		if($this->required===true and !$value)
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
	 * generates special html markup / js for needed validation
	 *
	 * @param string $html html syntax of element
	 * @return string final element
	 */
	public function jQueryValidationEngine($html)
	{
		$validation = array();
		if($this->required===true)
		{
			$validation[] = 'required';
		}
		if(!empty($validation))
			$html = str_replace("<input", "<input class=\"validate[".implode(',', $validation)."]\"", $html);
		return $html;
	}

	/**
	 * generates html for element
	 *
	 * @param string $client javascript client library - for validation purpose
	 * @return string generated html for element
	 */
	public function html($client=null)
	{
		$this->setRendered(true);
		$id = self::idPrefix.$this->id;
		$return  = '<div class="'.self::elementClass."\" id=\"{$id}_container\">";
		$return .= "<label for=\"{$id}\">";
		$return .= !empty($this->title)?$this->title.':':'';
		$return .= "</label>";
		$return .= "<div class=\"input\">";
		$return .= "<input type=\"checkbox\" name=\"{$this->name}\" ".
			"id=\"{$id}\" ".($this->value?"checked=\"checked\" ":'').
			(isset($this->min)?"maxlength=\"{$this->max}\" ":'').
			($this->readonly===true?'readonly="readonly" ':'').
			($this->disabled===true?'disabled="disabled" ':'').
			"/>";
		if(isset($this->description) and !empty($this->description))
			$return .= "<span class=\"help-block\">{$this->description}</span>";
		$return .= "</div></div>";
		if(!is_null($client) and is_callable(array($this, $client)))
		{
			$return = $this->{$client}($return);
		}
		return $return;
	}

	/**
	 * registers validators for element
	 *
	 * @param AgaviValidationManager $vm instance of AgaviValidationManager to register validators on it
	 * @param array $depends depends parameter of validations that get registered
	 * @param array $parameters
	 */
	public function registerValidators(AgaviValidationManager $vm, array $depends, array $parameters=array())
	{
		$this->value = isset($parameters[$this->name]);
		$vm->createValidator(
			'AgaviIssetValidator',
			array($this->name),
			array('' => 'field is required'),
			array(
				'translation_domain' => AgaviConfig::get('Form.TranslationDomain'),
				'required' => (bool) $this->required,
				'depends' => $depends
			)
		);
	}
}
