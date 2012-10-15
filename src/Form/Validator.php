<?php

/**
  * Class implementing the Agavi validator
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
  * Class implementing the Agavi validator
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
class Form_Validator extends AgaviValidator
{
    /**
      * Registers validators
      *
      * @param Form_Form              $form       The form
      * @param AgaviValidationManager $vm         the validation manager
      * @param array                  $parameters the input parameters
      * @param array                  $files      a list of files (optional)
      *
      * @return void
      */
    public static function registerValidators(Form_Form $form, AgaviValidationManager $vm, array $parameters, array $files = array())
    {
        $conditions = array();
        foreach ($form->getChildren() as $child) {
            if ($child instanceof Form_Elements_Fieldset) {
                continue;
            }
            $parents = (array) $child->parents;
            $depends = array();
            foreach ($parents as $id => $condition) {
                $depend = bin2hex("{$id}_{$condition['operation']}_{$condition['condition']}");
                $parent = $form->getChildById($id);
                if (is_null($parent)) {
                    throw new Exception("can't find parent with id={$id} for {$child->id}");
                }
                if (!isset($conditions[$depend])) {
                    $vm->createValidator(
                        'Form_Validator',
                        array($parent->name),
                        array(), // no error will be happen as we
                        // run this validator with severity="silent"
                        array( // parameters
                            'model' => 'Elements.Condition',
                            'name' => $depend,
                            'module' => 'Form',
                            'configuration' => $condition,
                            'provides' => $depend,
                            'severity' => 'info'
                        )
                    );
                    $conditions[$depend] = true;
                }
                $depends[] = $depend;
            }
            if (is_callable(array($child, 'registerValidators'))) {
                $child->registerValidators($vm, $depends, $parameters, $files);
            } else {
                $vm->createValidator(
                    'Form_Validator',
                    array($child->name),
                    array('' => 'field is required'), // error messages will be handled don't worry about it
                    array( //parameters
                        'element' => $child,
                        'name' => $child->name,
                        'export' => $child->name,
                        'depends' => $depends,
                        'translation_domain' => AgaviConfig::get('Form.TranslationDomain'),
                        'required' => (bool) $child->required
                    )
                );
            }
        }
    }
    
    /**
      * Does the validation
      *
      * @return bool true if the element has a valid value. False otherwise.
      */
    function validate()
    {
        $element = $this->getParameter('element');
        if (!$element instanceof Form_Element) {
            $model = $this->getParameter('model');
            $module = $this->getParameter('module');
            $config = $this->getParameter('configuration');
            $element = new $model($config);
        }

        try {
            $element->value = $this->getData($this->getArgument());
            $this->export($element->value);
            return true;
        }
        catch (Form_ValidationException $e) {
            $this->errorMessages = $e->getValidationErrors();
            foreach ($this->errorMessages as $error => $message) {
                $this->throwError($error);
            }
            return false;
        }
    }
}
