<?php

class Form_Elements_Checkbox extends Form_Element
{
    /**
     * constructs element
     */
    public function __construct($configuration=null, Form_Elements_Fieldset $form=null)
    {
        $this->configDefinition = array_merge(
            $this->configDefinition,
            array(
                'value' => 'is_bool',
                'required' => 'is_bool',
                'readonly' => 'is_bool',
                'disabled' => 'is_bool',
                'defaultValue' => 'is_bool',
                'description' => 'is_string'
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
        if($this->required===true and !$value)
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
     * registers validators for element
     *
     * @param AgaviValidationManager $vm instance of AgaviValidationManager to register validators on it
     * @param array $depends depends parameter of validations that get registered
     * @param array $parameters
     */
    public function registerValidators(AgaviValidationManager $vm, array $depends, array $parameters=array())
    {
        $this->value = isset($parameters[$this->name]);
        $vm->createValidator(
            'AgaviIssetValidator',
            array($this->name),
            array('' => 'field is required'),
            array(
                'translation_domain' => AgaviConfig::get('Form.TranslationDomain'),
                'required' => (bool) $this->required,
                'depends' => $depends
            )
        );
    }
}
