<?php

class Form_Elements_PasswordField extends Form_Elements_TextField
{
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
		$return .= "<input type=\"password\" name=\"{$this->name}\" ".
			"id=\"{$id}\" value=\"".htmlentities($this->value)."\" ".
			(isset($this->min)?"maxlength=\"{$this->max}\" ":'').
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

