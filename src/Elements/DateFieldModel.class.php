<?php

if(!class_exists('Form_ElementModel'))
	require __DIR__'/../ElementModel.class.php';

class Form_Elements_DateFieldModel extends Form_ElementModel
{
	/**
	 * @var array maps languages with calendar type
	 */
	public static $calendar = array(
		'fa' => 'persian'
	);

	/**
	 * @var array languages that are rtl
	 */
	private $bidi = array(
		'fa'
	);

	/**
	 * constructs element
	 */
	public function __construct($configuration=null, Form_Elements_FieldsetModel $form=null)
	{
		$this->configDefinition = array_merge(
			$this->configDefinition,
			array(
				'value' => 'is_string',
				'required' => 'is_bool',
				'readonly' => 'is_bool',
				'disabled' => 'is_bool',
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
		if($this->required===true and empty($value))
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
		$validation = array('custom[date]');
		if($this->required===true)
		{
			$validation[] = 'required';
		}
		if(!empty($validation))
			$html = str_replace("<input", "<input class=\"validate[".implode(',', $validation)."]\"", $html);
		$locale = $this->getContext()->getTranslationManager()->getCurrentLocaleIdentifier();
		$locale = explode('_', $locale);
		$locale = array_shift($locale);
		$calendar = 'undefined';
		if(isset(self::$calendar[$locale]))
			$calendar = "jQuery.calendars.instance('".self::$calendar[$locale]."', '{$locale}')";
		$alignment = '';
		if(in_array($locale, $this->bidi))
			$alignment = "alignment: 'bottomRight',";
		$id = self::idPrefix."{$this->id}";
		$html .= <<<HERE
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function(){
	// view
	var v = jQuery("#{$id}");
	// main
	var m = jQuery('<input />').attr({
		id: v.attr('id'),
		name: v.attr('name'),
		value: v.val(),
		type: 'hidden'
	});
	v.attr('id', v.attr('id')+'_view')
		.attr('name', v.attr('name')+'_view')
		.attr('readonly', 'readonly')
		.addClass('datepicker');
	// calendar
	var c = {$calendar};
	if(c && /^[0-9]+$/.test(m.val()))
	{
		v.val(c.fromJSDate(new Date(Number(m.val())*1000)).formatDate('Y-m-d'));
	}
	m.insertAfter(v);
	v.calendarsPicker({
		calendar: {$calendar},
		dateFormat: 'YYYY-mm-dd',
		{$alignment}
		renderer: jQuery.calendars.picker.themeRollerRenderer,
		showOnFocus: false,
		showTrigger: jQuery('<label class=\"add-on\" id=\"'+m.attr('id')+'_trigger\">↓</label>'),
		onSelect: function(d)
		{
			if(d && d[0])
				m.val(d[0].toJSDate().getTime()/1000);
			else
				m.val('');
		}
	});
	jQuery('#'+m.attr('id')+'_trigger').mouseenter(function(){
		jQuery(this).addClass('active');
	}).mouseleave(function(){
		jQuery(this).removeClass('active');
	});
});
//]]>
</script>
HERE;
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
		$return .= "<input type=\"text\" name=\"{$this->name}\" ".
			"id=\"{$id}\" value=\"{$this->value}\" ".
			($this->readonly===true?'readonly="readonly" ':'').
			($this->disabled===true?'disabled="disabled" ':'').
			"/>".
			"</div></div></div>";
		if(!is_null($client) and is_callable(array($this, $client)))
		{
			$return = $this->{$client}($return);
		}
		return $return;
	}

	/**
	 * prepare date before checking validation
	 *
	 * @param mixed $value
	 * @return int
	 */
	public function setValue($value)
	{
		if(is_numeric($value))
			$value = (int) $value;
		if(is_string($value))
			$value = strtotime($value);
		$value = date('Y/m/d', $value);
		$value = parent::setValue($value);
		return $value;
	}
}
