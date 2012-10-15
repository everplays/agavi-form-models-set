<?php
/**
  * Class implementing Form
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
  * Class implementing Form
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
class Form_Form extends Form_Elements_Fieldset
{
    /**
     * constructs element
     *
     * @param array     $configuration The configuration array (default to NULL)
     * @param Form_Form $form          The input form (default to NULL)
     */
    public function __construct($configuration = null, Form_Form $form = null)
    {
        $this->configDefinition = array_merge(
            $this->configDefinition,
            array(
                'submit' => 'is_string',
                'description' => 'is_string',
                'action' => 'is_string',
                'method' => 'is_string',
                'renderer' => __CLASS__.'::isAgaviRenderer'
            )
        );
        parent::__construct($configuration, $form);
    }

    /**
      * Determines whether the supplied argument is an instance of AgaviRenderer class
      *
      * @param mixed $v the input argument
      *
      * @return bool true if the input is an object of AgaviRenderer. false otherwise.
      */
    public static function isAgaviRenderer($v)
    {
        return $v instanceof AgaviRenderer;
    }

    /**
     * generates html of form
     *
     * @param AgaviRenderer $renderer the templating engine that will be used for rendering element by calling render method of it
     *
     * @return string
     */
    public function html(AgaviRenderer $renderer=null)
    {
        $this->setRendered(true);
        foreach ($this->children as $child) {
            $child->setRendered(false);
        }
        return parent::html($this->renderer);
    }

    /**
     * parses config object
     *
     * should be used to make Form_Form object from a config
     * (configuration is inspired by extjs lazy config)
     *
     * @param object    $config lazy configuration
     * @param Form_Form $form   Form
     *
     * @return Form_Form
     */
    public static function fromJson($config, Form_Elements_Fieldset $form=null)
    {
        $_form = new self($config, $form);
        self::parseChildren($config, $_form);
        return $_form;
    }
}
