<?php
/**
  * Class implementing ResourceField element
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
  * Class implementing ResourceField element
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


class Form_Elements_ResourceField extends Form_Element
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
                'value' => 'is_int',
                'required' => 'is_bool',
                'readonly' => 'is_bool',
                'disabled' => 'is_bool',
                'defaultValue' => 'is_int',
                'depends' => 'is_array',
                'source' => __CLASS__.'::isSource',
                'params' => 'is_object'
            )
        );
        parent::__construct($configuration, $form);
    }

    /**
     * checks type of source
     *
     * @param mixed $source Source
     *
     * @return bool $source could be string or array
     */
    public static function isSource($source)
    {
        return is_string($source) or is_array($source);
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
        if (isset($this->defaultValue) and !empty($this->readonly) and $value != $this->defaultValue) {
            $errors['readonly'] = 'this element is readonly';
        }
        return $errors;
    }

    /**
     * prepares value before regular check
     *
     * @param mixed $value the value to be set to
     *
     * @return int
     */
    public function setValue($value)
    {
        $value = (int) $value;
        $value = parent::setValue($value);
        return $value;
    }
}
