<?php

if(!class_exists('Form_ElementModel'))
	require __DIR__'/../ElementModel.class.php';

class Form_Elements_FieldsetModel extends Form_ElementModel
{
	/**
	 * @var boolean ability to have children
	 */
	protected $allowChildren = true;

	/**
	 * @var array children of form
	 */
	protected $children = array();

	/**
	 * @var array name to Child index - for getting children by name
	 */
	protected $name2index = array();

	/**
	 * @var array id to Child index - for getting children by id
	 */
	protected $id2index = array();

	/**
	 * adds Child to container
	 *
	 * @param Form_ElementModel $Child child to be added
	 */
	public function addChild(Form_ElementModel $Child)
	{
		$cp = (array) $Child->parents; // child parents
		$fp = (array) $this->parents; // fieldset parents
		foreach($fp as $parent => $condition)
			$cp[$parent] = $condition;
		$Child->parents = $cp;
		$index = count($this->children);
		$this->children[] = $Child;
		if(isset($Child->id))
			$this->id2index[$Child->id] = $index;
		if(isset($Child->name))
			$this->name2index[$Child->name] = $index;
	}

	/**
	 * returns Child by given id
	 *
	 * @param int $id id of Child
	 * @return Form_ElementModel Child or null
	 */
	public function getChildById($id)
	{
		if(isset($this->id2index[$id]))
			return $this->children[$this->id2index[$id]];
		return null;
	}

	/**
	 * returns Child by given name
	 *
	 * @param string $name name of Child
	 * @return Form_ElementModel Child or null
	 */
	public function getChildByName($name)
	{
		if(isset($this->name2index[$name]))
			return $this->children[$this->name2index[$name]];
		return null;
	}

	/**
	 * returns children of container
	 *
	 * @return array
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * removes an Child by id
	 *
	 * @param int $id id of Child
	 * @return Form_ElementModel removed Child or null
	 */
	public function removeChildById($id)
	{
		if(isset($this->id2index[$id]))
		{
			$tmp = $this->children[$this->id2index[$id]];
			if(isset($tmp->name))
				unset($this->name2index[$tmp->name]);
			unset($this->children[$this->id2index[$id]], $this->id2index[$id]);
			return $tmp;
		}
		return null;
	}

	/**
	 * removes an Child by name
	 *
	 * @param string $name name of Child
	 * @return Form_ElementModel removed Child or null
	 */
	public function removeChildByName($name)
	{
		if(isset($this->name2index[$name]))
		{
			$tmp = $this->children[$this->name2index[$name]];
			if(isset($tmp->id))
				unset($this->id2index[$tmp->id]);
			unset($this->children[$this->name2index[$name]], $this->name2index[$name]);
			return $tmp;
		}
		return null;
	}

	/**
	 * removes an Child
	 *
	 * @param Form_ElementModel $Child Child to get removed
	 * @return Form_ElementModel removed Child or null
	 */
	public function removeChild($Child)
	{
		$index = array_search($Child, $this->children);
		if($index!==false)
		{
			$tmp = $this->children[$index];
			if(isset($tmp->id))
				unset($this->id2index[$tmp->id]);
			if(isset($tmp->name))
				unset($this->name2index[$tmp->name]);
			unset($this->children[$index]);
			return $tmp;
		}
		return null;
	}

	/**
	 * returns validation errors
	 *
	 * @param mixed $value value that must be validated
	 * @return array assocc-array of errors (empty array on success)
	 */
	public function getValidationErrors($value)
	{
		return array();
	}

	/**
	 * generates html presentation of element
	 *
	 * @return string generated html for element
	 * @param string $client javascript client library - for validation purpose
	 */
	public function html($client=null)
	{
		$this->setRendered(true);
		$id = self::idPrefix.$this->id;
		$return  = '<div class="'.self::elementClass.'">';
		$result = "<fieldset ".
			"id=\"{$id}_container\" ".
			">";
		if(isset($this->title))
		{
			$result .= "<legend>{$this->title}</legend>";
		}
		// children level 2 or deeper will be at the end of list so
		// when their parent get rendered they will be rendered too
		foreach($this->children as $child)
		{
			$child->setRendered(false);
		}
		foreach($this->children as $child)
		{
			if(!$child->isRendered())
			{
				$result .= $child->html($client);
			}
		}
		$result .= '</fieldset>';
		return $result;
	}

	/**
	 * parses children & adds them into given fieldset
	 *
	 * @param object $config
	 * @param Form_Elements_FieldsetModel $container fieldset
	 */
	public static function parseChildren($config, Form_Elements_FieldsetModel $container)
	{
		$contextProfile = AgaviConfig::get('core.default_context');
		if(is_null($contextProfile))
		{
			$contextProfile = md5(microtime());
			AgaviConfig::set('core.default_context', $contextProfile);
		}
		$context = AgaviContext::getInstance();
		if(isset($config->items) and is_array($config->items))
		{
			foreach($config->items as $item)
			{
				if(isset($item->xtype))
				{
					switch($item->xtype)
					{
						case 'textfield':
							if(isset($item->inputType) and $item->inputType=='password')
								$model = array('Elements.PasswordField', 'Form');
							else
								$model = array('Elements.TextField', 'Form');
							break;
						case 'numberfield':
							$model = array('Elements.NumberField', 'Form');
							break;
						case 'combo':
							$model = array('Elements.ResourceField', 'Form');
							break;
						case 'datefield':
							$model = array('Elements.DateField', 'Form');
							break;
						case 'radiogroup':
							$model = array('Elements.RadioGroup', 'Form');
							break;
						case 'checkbox':
							$model = array('Elements.Checkbox', 'Form');
							break;
						case 'fieldset':
							$model = Form_Elements_FieldsetModel::fromJson($item, $container);
							break;
						case 'textarea':
							$model = array('Elements.TextArea', 'Form');
							break;
						default:
							$model = null;
					}
					if(is_array($model))
					{
						$el = $context->getModel($model[0], $model[1], array($item, $container));
						$container->addChild($el);
					}
					elseif(!is_null($model))
					{
						$container->addChild($model);
					}
				}
			}
		}
	}

	/**
	 * parses config object from extjs
	 *
	 * @param object $config lazy configuration of extjs
	 * @param Form_FormModel $form
	 * @return Form_Elements_FieldsetModel
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
		$fieldset = $context->getModel('Elements.Fieldset', 'Form', array($config, $form));
		$children = array();
		$columns = count($config->items);
		$rows = count($config->items[0]->items);
		for($i=0; $i<$rows; $i++)
		{
			for($j=0; $j<$columns; $j++)
			{
				if(isset($config->items[$j]->items[$i]))
				{
					$children[] = $config->items[$j]->items[$i];
				}
			}
		}
		$tmp = new stdClass();
		$tmp->items = $children;
		self::parseChildren($tmp, $fieldset);
		$form->addChild($fieldset);
		if(!is_null($form))
			foreach($fieldset->children as $child)
			{
				$form->addChild($child);
			}
		return $fieldset;
	}
}
