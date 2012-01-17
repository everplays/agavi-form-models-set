<?php

if(!class_exists('Form_ElementModel'))
	require __DIR__'/../ElementModel.class.php';

class Form_Elements_RadioGroupModel extends Form_ElementModel
{
	/**
	 * constructs element
	 */
	public function __construct($configuration=null, Form_Elements_FieldsetModel $form=null)
	{
		$this->configDefinition = array_merge(
			$this->configDefinition,
			array(
				'regex' => 'is_string',
				'value' => 'is_int',
				'required' => 'is_bool',
				'readonly' => 'is_bool',
				'disabled' => 'is_bool',
				'items' => 'is_array',
				'defaultValue' => 'is_int'
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
		$return .= "<label>{$this->title}:</label>";
		$return .= "<div class=\"input\"><ul class=\"inputs-list\">";
		$i = 0;
		foreach($this->items as $item)
		{
			$option = "<li><label>".
				"<input type=\"radio\" name=\"{$this->name}\" ".
				"value=\"{$item->value}\" id=\"{$id}_".$i++."\" ".
				($this->value==$item->value?'checked="checked" ':'').
				($this->readonly===true?'readonly="readonly" ':'').
				($this->disabled===true?'disabled="disabled" ':'').
				"/><span>{$item->label}</span></label></li>";
			if(!is_null($client) and is_callable(array($this, $client)))
			{
				$return .= $this->{$client}($option);
			}
		}
		$return .= "</ul></div></div>";
		return $return;
	}

	/**
	 * prepares value before checking regular validation check
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function setValue($value)
	{
		$value = (int) $value;
		$value = parent::setValue($value);
		return $value;
	}
}
