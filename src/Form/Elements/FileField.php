<?php
/**
  * Class implementing FileField element
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
  * Class implementing FileField element
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

class Form_Elements_FileField extends Form_Element
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
                'value' => __CLASS__.'::isFile',
                'required' => 'is_bool',
                'readonly' => 'is_bool',
                'disabled' => 'is_bool',
                'types' => 'is_array'
            )
        );
        parent::__construct($configuration, $form);
    }

    /**
     * checks that given value is intance of AgaviUploadedFile
     *
     * @param mixed $value Input
     *
     * @return bool true if the supplied value is an instance of AgaviUploadedFile. false otherwise.
     */
    public static function isFile($value)
    {
        return $value instanceof AgaviUploadedFile;
    }

    /**
     * returns validation errors
     *
     * @param mixed $value value to be checked
     *
     * @return array assoc-array of errors (empty = success)
     */
    public function getValidationErrors($value)
    {
        $errors = array();
        // ignore it if it's not an AgaviUploaded file, maybe it's not required
        if ($value instanceof AgaviUploadedFile) {
            if (!empty($this->types) and !in_array($value->getMimeType(), $this->types)) {
                $errors['mime_type'] = true; // we're not using it for messages (only
                // in this element) so if any error exists just set key of them to
                // avoid returning empty $errors
            }
        }
        return $errors;
    }

    /**
     * register special validators of this element on validation manager
     *
     * @param AgaviValidationManager $vm         instance of AgaviValidationManager to register validators on it
     * @param array                  $depends    depends parameter of validations that get registered
     * @param array                  $parameters Paramas
     * @param array                  $files      array of AgaviUploadedFile
     *
     * @return void
     */
    public function registerValidators(AgaviValidationManager $vm, array $depends, array $parameters=array(), array $files=array())
    {
        if (isset($files[$this->name])) {
            if ($files[$this->name] instanceof AgaviUploadedFile and !$files[$this->name]->hasError()) {
                $errors = $this->getValidationErrors($files[$this->name]);
                if (empty($errors)) {
                    $this->value = $files[$this->name];
                }
            }
        }
        $vm->createValidator(
            'AgaviFileValidator',
            array($this->name),
            array(
                '' => 'field is required',
                'mime_type' => 'format of uploaded file is not acceptable'
            ),
            array( // parameters
                'name' => $this->name,
                'mime_type' => empty($this->types)?'/.*/':'#^'.implode('$|^', $this->types).'$#',
                'translation_domain' => AgaviConfig::get('Form.TranslationDomain'),
                'required' => (bool) $this->required
            )
        );
    }
}
