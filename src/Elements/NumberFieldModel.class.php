<?php

if(!class_exists('Form_ElementModel'))
	require __DIR__'/../ElementModel.class.php';

class Form_Elements_NumberFieldModel extends Form_ElementModel
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

	/**
	 * generates special html markup / js for needed validation
	 *
	 * @param string $html html syntax of element
	 * @return string final element
	 */
	public function jQueryValidationEngine($html)
	{
		$validation = array('custom[number]');
		if($this->required===true)
		{
			$validation[] = 'required';
		}
		if(isset($this->min) and $this->min>0)
		{
			$validation[] = "min[{$this->min}]";
		}
		if(isset($this->max) and $this->max>0)
		{
			$validation[] = "max[{$this->max}]";
		}
		if(isset($this->regex))
		{
			$validation[] = "custom[el{$this->id}]";
			$regex = json_encode($this->regex);
			$html .= <<<HERE
<script type="text/javascript">
//<![CDATA[
(function($){
	if($ && $.validationEngineLanguage)
	{
		$.validationEngineLanguage.allRules.el{$this->id} = {};
		$.validationEngineLanguage.allRules.el{$this->id}.regex = new RegExp({$regex});
	}
})(jQuery);
//]]>
</script>
HERE;
		}
		if(!empty($validation))
			$html = str_replace("<input", "<input class=\"validate[".implode(',', $validation)."]\"", $html);
		return $html;
	}

	/**
	 * generates html for element
	 *
	 * @return string generated html for element
	 * @param string $client javascript client library - for validation purpose
	 */
	public function html($client=null)
	{
		$this->setRendered(true);
		$id = self::idPrefix.$this->id;
		$return  = '<div class="'.self::elementClass."\" id=\"{$id}_container\">";
		$return .= "<label for=\"".self::idPrefix."{$this->id}\">{$this->title}:</label>";
		$return .= "<div class=\"input\">";
		$return .= "<input type=\"text\" name=\"{$this->name}\" ".
			"id=\"{$id}\" value=\"{$this->value}\" ".
			($this->readonly===true?'readonly="readonly" ':'').
			($this->disabled===true?'disabled="disabled" ':'').
			"/></div></div>";
		if(!is_null($client) and is_callable(array($this, $client)))
		{
			$return = $this->{$client}($return);
		}
		return $return;
	}
}
