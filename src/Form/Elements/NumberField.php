<?php
/**
  * Class implementing NumberField element
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
  * Class implementing NumberField element
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


class Form_Elements_NumberField extends Form_Element
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
                'regex' => 'is_string',
                'min' => 'is_numeric',
                'max' => 'is_numeric',
                'value' => 'is_numeric',
                'required' => 'is_bool',
                'readonly' => 'is_bool',
                'disabled' => 'is_bool',
                'allowDecimal' => 'is_bool',
                'defaultValue' => 'is_numeric'
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
        if ($this->required === true and !isset($value)) {
            $errors['required'] = 'this element is required';
        }
        if (isset($this->min) and $this->min > 0 and $value < $this->min) {
            $errors['min'] = json_encode(
                array(
                'msg' => "minimum value is %s",
                'arguments' => array($this->min)
                )
            );
        }
        if (isset($this->max) and $this->max > 0 and $value > $this->max) {
            $errors['max'] = json_encode(
                array(
                'msg' => "maximum value is $s",
                'arguments' => array($this->max)
                 )
            );
        }
        if (isset($this->regex) and !preg_match($this->regex, $value)) {
            $errors['pattern'] = 'value does not match the pattern';
        }
        if (isset($this->defaultValue) and !empty($this->readonly) and $value != $this->defaultValue) {
            $errors['readonly'] = 'this element is readonly';
        }
        return $errors;
    }
}
