<?php

abstract class Form_ElementModel implements AgaviIModel
{
	const idPrefix = 'atras-el-';
	const elementClass = 'clearfix';

	/**
	 * @var AgaviContext current application context
	 */
	protected $context = null;

	/**
	 * @var array name of configurations
	 */
	protected $configDefinition = array(
		'id' => 'is_int',
		'name' => 'is_string',
		'parents' => 'is_array',
		'title' => 'is_string'
	);

	/**
	 * @var boolean whether element has been rendered or not
	 */
	protected $rendered = false;

	/**
	 * @var array element configurations
	 */
	protected $configuration = array();

	/**
	 * @var boolean sets whether current element may contain children or not
	 */
	protected $allowChildren = false;

	/**
	 * @var array children of element
	 */
	protected $children = array();

	/**
	 * @var Form_FormModel form model
	 */
	protected $form = null;

	/**
	 * constructs element
	 *
	 * @param mixed $configuration configuration of element
	 * @param Form_Elements_FieldsetModel $form container form
	 */
	public function __construct($configuration=null, Form_Elements_FieldsetModel $form=null)
	{
		if(!empty($configuration))
		{
			$this->setConfiguration($configuration);
		}
		if(!empty($form))
			$this->form = $form;
	}

	/**
	 * sets configutation by given array
	 *
	 * @param mixed $configuration
	 */
	public function setConfiguration($configuration)
	{
		if(is_array($configuration) or is_object($configuration))
			foreach($configuration as $name => $value)
			{
				$this->{$name} = $value;
			}
	}

	/**
	 * Retrieve the current application context.
	 *
	 * @return AgaviContext The current AgaviContext instance.
	 */
	public final function getContext()
	{
		return $this->context;
	}

	/**
	 * Initialize this model.
	 *
	 * @param AgaviContext $context The current application context.
	 * @param array $parameters passed parameters to construct
	 */
	public function initialize(AgaviContext $context, array $parameters = array())
	{
		$this->context = $context;
	}

	/**
	 * sets class variables
	 *
	 * @param string $name name of variable
	 * @param mixed $value value of variable
	 */
	public function __set($name, $value)
	{
		if($name=='value' and !is_null($value) and isset($this->configDefinition[$name]))
			$value = $this->setValue($value);
		if(isset($this->configDefinition[$name]) and !is_null($value) and is_callable($this->configDefinition[$name]) and !call_user_func($this->configDefinition[$name], $value))
			throw new Exception('invalid configuration value for '.$name.' in '.get_class($this));
		if(isset($this->configDefinition[$name]))
			$this->configuration[$name] = $value;
	}

	/**
	 * gets value of class variable if exists
	 *
	 * @param string $name name of variable
	 * @return mixed value of variable or null if has not been set
	 */
	public function __get($name)
	{
		if(isset($this->configuration[$name]))
			return $this->configuration[$name];
		return null;
	}

	/**
	 * checks that given variable has been set or not
	 *
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->configuration[$name]);
	}

	/**
	 * adds a new child to element
	 *
	 * @param Form_ElementModel $child new child to get added
	 */
	public function addChild(Form_ElementModel $child)
	{
		if(!$this->allowChildren)
		{
			throw new Exception(__CLASS__." doesn't support children");
		}
		$this->children[] = $child;
	}

	/**
	 * returns whether the element has been rendered or not
	 *
	 * @return bool
	 */
	public function isRendered()
	{
		return $this->rendered;
	}

	/**
	 * sets whether the element has been rendered or not
	 *
	 * @param bool $rendered
	 */
	public function setRendered($rendered)
	{
		$this->rendered = $rendered;
	}

	/**
	 * generates html presentation of element
	 * rendered must be set to true after execution
	 *
	 * @param string $client javascript client library - for validation purposes
	 * @return string
	 */
	abstract public function html($client=null);

	/**
	 * returns validation errors
	 *
	 * @param mixed $value value that must be validated
	 * @return array assocc-array of errors (empty array on success)
	 */
	abstract public function getValidationErrors($value);

	/**
	 * prepares given value before regular validation check, also a good place
	 * for having element specifc validation check
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function setValue($value)
	{
		// any preparation goes here, for example casting to int
		$errors = $this->getValidationErrors($value);
		// if specific validation existed would go here & if
		// anything goes wrong will put error message into errors
		if(!empty($errors))
			throw $this->getContext()->getModel('ValidationException', 'Form', array(($errors)));
		return $value;
	}
}
