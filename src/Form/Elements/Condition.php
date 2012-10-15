<?php
/**
  * Class implementing Condition element
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
  * Class implementing Condition element
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

class Form_Elements_Condition extends Form_Element
{
    /**
     * @var array name of configurations
     */
    protected $configDefinition = array(
        'condition' => false,
        'operation' => false,
        'value' => false
    );

    /**
     * checks given value can pass the conditions or not
     *
     * @param mixed $value The value to be set to
     *
     * @throws Form_ValidationException if condition check fails
     *
     * @return mixed
     */
    public function setValue($value)
    {
        $failure = true;
        switch ($this->operation) {
        case '==':
            if ($value == $this->condition) {
                $failure = false;
            }
            break;
        case '>':
            if ($value > $this->condition) {
                $failure = false;
            }
            break;
        case '>=':
            if ($value >= $this->condition) {
                $faulure = false;
            }
            break;
        case '<':
            if ($value < $this->condition) {
                $failure = false;
            }
            break;
        case '<=':
            if ($value <= $this->condition) {
                $failure = false;
            }
            break;
        case '*':
            if (strpos($value, $this->condition) !== false) {
                $failure = false;
            }
            break;
        case '^':
            if (strpos($value, $this->condition) === 0) {
                 $failure = false;
            }
            break;
        case '$':
            if (strrpos($value, $this->condition) == strlen($value) - strlen($this->condition)) {
                 $failure = false;
            }
            break;
        default:
            break;
        }
        if ($failure) {
            throw new Form_ValidationException(array('' => 'condition failed'));
        }
        return $value;
    }

    /**
     * checks value of element against regular checks
     *
     * @param mixed $value value to be checked
     *
     * @return array errors have been found
     */
    public function getValidationErrors($value)
    {
        return array();
    }
}
