<?php

class Form_Form extends Form_Elements_Fieldset
{
    /**
     * constructs element
     */
    public function __construct($configuration=null, Form_Form $form=null)
    {
        $this->configDefinition = array_merge(
            $this->configDefinition,
            array(
                'submit' => 'is_string',
                'description' => 'is_string',
                'action' => 'is_string',
                'method' => 'is_string',
                'renderer' => __CLASS__.'::is_agavi_renderer'
            )
        );
        parent::__construct($configuration, $form);
    }

    public static function is_agavi_renderer($v)
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
