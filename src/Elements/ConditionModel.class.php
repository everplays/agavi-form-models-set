<?php

if(!class_exists('Form_ElementModel'))
	require __DIR__'/../ElementModel.class.php';

class Form_Elements_ConditionModel extends Form_ElementModel
{
	/**
	 * @var array name of configurations
	 */
	protected $configDefinition = array(
		'condition' => false,
		'opration' => false,
		'value' => false
	);

	/**
	 * checks given value can pass the conditions or not
	 *
	 * @param mixed $value
	 * @throws Form_ValidationExceptionModel if condition check fails
	 * @return mixed
	 */
	public function setValue($value)
	{
		$failure = true;
		switch($this->opration)
		{
			case '==':
				if($value==$this->condition)
					$failure = false;
				break;
			case '>':
				if($value>$this->condition)
					$failure = false;
				break;
			case '>=':
				if($value>=$this->condition)
					$faulure = false;
				break;
			case '<':
				if($value<$this->condition)
					$failure = false;
				break;
			case '<=':
				if($value<=$this->condition)
					$failure = false;
				break;
			case '*':
				if(strpos($value, $this->condition)!==false)
					$failure = false;
				break;
			case '^':
				if(strpos($value, $this->condition)===0)
					$failure = false;
				break;
			case '$':
				if(strrpos($value, $this->condition)==strlen($value)-strlen($this->condition))
					$failure = false;
				break;
			default:
				break;
		}
		if($failure)
			throw $this->getContext()->getModel('ValidationException', 'Form', array(
				array(
					'' => 'condition failed'
				)
			));
		return $value;
	}

	/**
	 * checks value of element against regular checks
	 *
	 * @param mixed $value value to be checked
	 * @return array errors have been found
	 */
	public function getValidationErrors($value)
	{
		return array();
	}

	/**
	 * generates html for element
	 *
	 * @param string $client client validation
	 * @return string
	 */
	public function html($client=null)
	{
		return '';
	}
}
