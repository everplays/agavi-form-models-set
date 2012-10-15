<?php
/**
  * Class implementing Checkbox element
  * 
  * PHP version 5.3
  *
  * @category  Xamin
  * @package   XaminApplianceMarket
  * @author    Alireza Ghafouri <fzerorubigd@gmail.com>
  * @author    Behrooz Shabani <everplays@gmail.com>
  * @author    Koosha Khajeh Moogahi <koosha.khajeh@gmail.com>
  * @copyright 2012 (c) ParsPooyesh Co
  * @license   GNU GPLv3+ <http://www.gnu.org/licenses/gpl-3.0.html>
  * @link      http://www.xamin.ir
  *
  */

/**
  * Class implementing Checkbox element
  *
  * @category  Xamin
  * @package   XaminApplianceMarket
  * @author    Alireza Ghafouri <fzerorubigd@gmail.com>
  * @author    Behrooz Shabani <everplays@gmail.com>
  * @author    Koosha Khajeh Moogahi <koosha.khajeh@gmail.com>
  * @copyright 2012 (c) ParsPooyesh Co
  * @license   GNU GPLv3+ <http://www.gnu.org/licenses/gpl-3.0.html>
  * @link      http://www.xamin.ir
  *
  */

class Form_Elements_Checkbox extends Form_Element
{
    /**
     * constructs element
     *
     * @param array                  $configuration optional configuration array
     * @param Form_Elements_Fieldset $form          From (default to NULL)
     *
     * @return an object
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
     *
     * @return array assocc-array of errors (empty array on success)
     */
    public function getValidationErrors($value)
    {
        $errors = array();
        if ($this->required === true and !$value) {
            $errors['required'] = 'this element is required';
        }
        if (isset($this->defaultValue) and !empty($this->readonly) and $value != $this->defaultValue) {
            $errors['readonly'] = 'this element is readonly';
        }
        return $errors;
    }

    /**
     * registers validators for element
     *
     * @param AgaviValidationManager $vm         instance of AgaviValidationManager to register validators on it
     * @param array                  $depends    depends parameter of validations that get registered
     * @param array                  $parameters Params  
     *
     * @return void
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
