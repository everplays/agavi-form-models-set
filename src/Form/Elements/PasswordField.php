<?php

class Form_Elements_PasswordField extends Form_Elements_TextField
{
	/**
	 * constructs element
	 */
	public function __construct($configuration=null, Form_Elements_Fieldset $form=null)
	{
		$this->configDefinition = array_merge(
			$this->configDefinition,
			array(
				'equal' => 'is_string',
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
		$errors = parent::getValidationErrors($value);
		
		if (isset($this->equal)) {
			$other = $this->form->getChildByName($this->equal);
			if (!$other) {
                $errors['equal'] = json_encode(array(
                                                 'msg' => "field %s dose not exist",
                                                 'arguments' => array($this->equal)
                                                 ));    				
			} elseif ($other->value != $value) {
                $errors['equal'] = json_encode(array(
                                                 'msg' => "must be equal with %s field",
                                                 'arguments' => array($this->equal)
                                                 ));                
            }
		}
        return $errors;
	}
	
}

