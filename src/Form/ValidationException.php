<?php

/**
  * Validation Exception class
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
  * Validation Exception class
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
class Form_ValidationException extends Exception
{
    /**
     * @var array holes errors that field has
     */
    protected $validationErrors = array();

    /**
     * constcutor
     *
     * @param array $errors validation errors
     */
    public function __construct(array $errors)
    {
        $this->validationErrors = $errors;
        parent::__construct('element validation failed');
    }

    /**
     * returns errors that happened during validation
     *
     * @return Error that happened during validation
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}
