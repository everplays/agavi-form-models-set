<?php

class Form_ValidationExceptionModel extends Exception
{
	/**
	 * @var array holes errors that field has
	 */
	protected $validationErrors = array();

	/**
	 * constcutor
	 */
	public function __construct(array $errors)
	{
		$this->validationErrors = $errors;
		parent::__construct('element validation failed');
	}

	/**
	 * returns errors that happened during validation
	 */
	public function getValidationErrors()
	{
		return $this->validationErrors;
	}
}
