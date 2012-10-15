<?php
/**
  * Class implementing DateField element
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
  * Class implementing DateField element
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

class Form_Elements_DateField extends Form_Element
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
     *
     * @return array assocc-array of errors (empty array on success)
     */
    public function getValidationErrors($value)
    {
        $errors = array();
        if ($this->required === true and empty($value)) {
            $errors['required'] = 'this element is required';
        }
        if (isset($this->defaultValue) and !empty($this->readonly) and $value != $this->defaultValue) {
            $errors['readonly'] = 'this element is readonly';
        }
        return $errors;
    }

    /**
     * prepare date before checking validation
     *
     * @param mixed $value the value to be set to
     *
     * @return int
     */
    public function setValue($value)
    {
        if (is_numeric($value)) {
            $value = (int) $value;
        }
        if (is_string($value)) {
            $value = strtotime($value);
        }
        $value = date('Y/m/d', $value);
        $value = parent::setValue($value);
        return $value;
    }
}
