<?php

if(!class_exists('Form_Elements_TextFieldModel'))
	require __DIR__'/TextFieldModel.class.php';

class Form_Elements_TextAreaModel extends Form_Elements_TextFieldModel
{
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
		if(isset($this->min) and $this->min>0)
		{
			$validation[] = "minSize[{$this->min}]";
		}
		if(isset($this->max) and $this->max>0)
		{
			$validation[] = "maxSize[{$this->max}]";
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
			$html = str_replace("<textarea", "<textarea class=\"validate[".implode(',', $validation)."]\"", $html);
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
		$return .= "<label for=\"".self::idPrefix."{$this->id}\">{$this->title}:</label>";
		$return .= "<div class=\"input\">";
		$return .= "<textarea name=\"{$this->name}\" ".
			"id=\"{$id}\" rows=\"5\" cols=\"80\"".
			($this->readonly===true?'readonly="readonly" ':'').
			($this->disabled===true?'disabled="disabled" ':'').
			'>'.htmlspecialchars($this->value).'</textarea></div></div>';
		if(!is_null($client) and is_callable(array($this, $client)))
		{
			$return = $this->{$client}($return);
		}
		return $return;
	}
}

