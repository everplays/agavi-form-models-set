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
		$params = $this->params;
		if(is_null($params))
			$params = new stdClass();
		$params->field = $this->id;
		$params = json_encode($params);
		if(!empty($validation))
			$html = str_replace("<input", "<input class=\"validate[".implode(',', $validation)."]\"", $html);
		$depends = json_encode($this->depends);
		$source = json_encode($this->source);
		$html .= '<script type="text/javascript">
jQuery(document).ready(function(){
	$("#'.self::idPrefix.$this->id."\").resource({
		minLength: 0,
		depends: {$depends},
		source: {$source},
		params: {$params}
	}).parent('.input-append').find('.add-on').click(function(){
		$(\"#".self::idPrefix.$this->id."_view\").autocomplete('search');
	}).mouseenter(function(){
		$(this).addClass('active');
	}).mouseleave(function(){
		$(this).removeClass('active');
	});
});
</script>";
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
		$return .= "<label for=\"{$id}\">{$this->title}:</label>";
		$return .= "<div class=\"input\">";
		$return .= "<div class=\"input-append\">";
		if(!empty($this->value))
		{
			$view = $this->value;
			$xView = true;
            foreach($this->source as $item)
            {
                $item = (object) $item;
                if($item->id==$this->value)
                {
                    $view = $item->label;
                    $xView = true;
                    break;
                }
            }
			$return .= "<input type=\"text\" name=\"{$this->name}_view\" ".
				"id=\"{$id}_view\" value=\"{$view}\" ".
				($this->readonly===true?'readonly="readonly" ':'').
				($this->disabled===true?'disabled="disabled" ':'').
				"/>";
			$return .= "<input type=\"hidden\" name=\"{$this->name}\" ".
				"id=\"{$id}\" value=\"{$this->value}\" />";
		}
		else
			$return .= "<input type=\"text\" name=\"{$this->name}\" ".
				"id=\"{$id}\" value=\"{$this->value}\" ".
				($this->readonly===true?'readonly="readonly" ':'').
				($this->disabled===true?'disabled="disabled" ':'').
				"/>";
		$return .= "<label class=\"add-on\">↓</label>".
			"</div></div></div>";
		if(!is_null($client) and is_callable(array($this, $client)))
		{
			$return = $this->{$client}($return);
		}
		return $return;
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
