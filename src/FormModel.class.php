<?php

if(!class_exists('Form_Elements_FieldsetModel'))
	require __DIR__.'/Elements/FieldsetModel.class.php';

class Form_FormModel extends Form_Elements_FieldsetModel
{
	/**
	 * constructs element
	 */
	public function __construct($configuration=null, Form_FormModel $form=null)
	{
		$this->configDefinition = array_merge(
			$this->configDefinition,
			array(
				'submit' => 'is_string',
				'description' => 'is_string',
				'action' => 'is_string',
				'method' => 'is_string'
			)
		);
		parent::__construct($configuration, $form);
	}

	/**
	 * generates html of form
	 *
	 * @return string generated html for element
	 * @param string $client javascript client library - for validation purpose
	 */
	public function html($client=null)
	{
		$this->setRendered(true);
		// children level 2 or deeper will be at the end of list so
		// when their parent get rendered they will be rendered too
		foreach($this->children as $child)
		{
			$child->setRendered(false);
		}
		$result = "<form id=\"".self::idPrefix."-{$this->id}\" enctype=\"multipart/form-data\" ";
		if(isset($this->action))
			$result .= "action=\"".$this->action."\" ";
		$method = 'post';
		if(isset($this->method))
			$method = strtolower($this->method);
		$result .= "method=\"{$method}\" ";
		$result .= ">";
		foreach($this->children as $child)
		{
			if(!$child->isRendered())
			{
				$result .= $child->html($client);
			}
		}
		if(isset($this->submit))
			$result .= "<input type=\"submit\" value=\"{$this->submit}\" class=\"btn primary\" />";
		$result .="</form>";
		if(isset($this->description) || isset($this->title))
		{
			$result  = "<div class=\"row\"><div class=\"span4\">".
				(isset($this->title)?"<h2>{$this->title}</h2>":'').
				(isset($this->description)?"<p>{$this->description}</p>":'').
				"</div><div class=\"span12\">".
				$result."</div></div>";
		}
		if(!is_null($client) and is_callable(array($this, $client)))
		{
			$result = $this->{$client}($result);
		}
		return $result;
	}

	/**
	 * generates special html markup / js for needed validation
	 *
	 * @param string $html html syntax of element
	 * @return string final element
	 */
	public function jQueryValidationEngine($html)
	{
		$prefix = self::idPrefix;
		$html .= <<<HERE
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#{$prefix}-{$this->id}").validationEngine();

HERE;
		foreach($this->children as $child)
		{
			if(isset($child->parents))
			{
				foreach($child->parents as $parent => $condition)
				{
					$parent = '#'.self::idPrefix.$parent;
					$id = self::idPrefix.$child->id;
					$html .= "$('#{$id}_container').ConditionManager(".json_encode($parent).", ".json_encode($condition).");\n";
				}
			}
		}
		$html .= '});</script>';
		return $html;
	}

	/**
	 * parses config object
	 *
	 * should be used to make Form_FormModel object from a config
	 * (configuration is inspired by extjs lazy config)
	 *
	 * @param object $config lazy configuration
	 * @param Form_FormModel $form
	 * @return Form_FormModel
	 */
	public static function fromJson($config, Form_Elements_FieldsetModel $form=null)
	{
		$contextProfile = AgaviConfig::get('core.default_context');
		if(is_null($contextProfile))
		{
			$contextProfile = md5(microtime());
			AgaviConfig::set('core.default_context', $contextProfile);
		}
		$context = AgaviContext::getInstance();
		$_form = $context->getModel('Form', 'Form', array($config, $form));
		self::parseChildren($config, $_form);
		return $_form;
	}
}
