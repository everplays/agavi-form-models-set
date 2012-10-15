<?php
/**
  * Class implementing PasswordField element
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
  * Class implementing PasswordField element
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


class Form_Elements_PasswordField extends Form_Elements_TextField
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
                'equal' => 'is_string',
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
        $errors = parent::getValidationErrors($value);
        
        if (isset($this->equal)) {
            $other = $this->form->getChildByName($this->equal);
            if (!$other) {
                $errors['equal'] = json_encode(
                    array(
                    'msg' => "field %s dose not exist",
                    'arguments' => array($this->equal)
                    )
                );                    
            } elseif ($other->value != $value) {
                $errors['equal'] = json_encode(
                    array(
                    'msg' => "must be equal with %s field",
                    'arguments' => array($this->equal)
                    )
                );                
            }
        }
        return $errors;
    }
    
}

