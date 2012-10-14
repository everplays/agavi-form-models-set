<?php

class Form_Elements_Fieldset extends Form_Element
{
    /**
     * @var boolean ability to have children
     */
    protected $allowChildren = true;

    /**
     * @var array children of form
     */
    protected $children = array();

    /**
     * @var array name to Child index - for getting children by name
     */
    protected $name2index = array();

    /**
     * @var array id to Child index - for getting children by id
     */
    protected $id2index = array();

    /**
     * adds Child to container
     *
     * @param Form_Element $Child child to be added
     *
     * @return void
     */
    public function addChild(Form_Element $Child)
    {
        $cp = (array) $Child->parents; // child parents
        $fp = (array) $this->parents; // fieldset parents
        foreach ($fp as $parent => $condition) {
            $cp[$parent] = $condition;
        }
        $Child->parents = $cp;
        $index = count($this->children);
        $this->children[] = $Child;
        if (isset($Child->id)) {
            $this->id2index[$Child->id] = $index;
        }
        if (isset($Child->name)) {
            $this->name2index[$Child->name] = $index;
        }
    }

    /**
     * returns Child by given id
     *
     * @param int $id id of Child
     *
     * @return Form_Element Child or null
     */
    public function getChildById($id)
    {
        if (isset($this->id2index[$id])) {
            return $this->children[$this->id2index[$id]];
        }
        return null;
    }

    /**
     * returns Child by given name
     *
     * @param string $name name of Child
     *
     * @return Form_Element Child or null
     */
    public function getChildByName($name)
    {
        if (isset($this->name2index[$name])) {
            return $this->children[$this->name2index[$name]];
        }
        return null;
    }

    /**
     * returns children of container
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * removes an Child by id
     *
     * @param int $id id of Child
     *
     * @return Form_Element removed Child or null
     */
    public function removeChildById($id)
    {
        if (isset($this->id2index[$id])) {
            $tmp = $this->children[$this->id2index[$id]];
            if (isset($tmp->name)) {
                unset($this->name2index[$tmp->name]);
            }
            unset($this->children[$this->id2index[$id]], $this->id2index[$id]);
            return $tmp;
        }
        return null;
    }

    /**
     * removes an Child by name
     *
     * @param string $name name of Child
     *
     * @return Form_Element removed Child or null
     */
    public function removeChildByName($name)
    {
        if (isset($this->name2index[$name])) {
            $tmp = $this->children[$this->name2index[$name]];
            if (isset($tmp->id)) {
                unset($this->id2index[$tmp->id]);
            }
            unset($this->children[$this->name2index[$name]], $this->name2index[$name]);
            return $tmp;
        }
        return null;
    }

    /**
     * removes an Child
     *
     * @param Form_Element $Child Child to get removed
     *
     * @return Form_Element removed Child or null
     */
    public function removeChild($Child)
    {
        $index = array_search($Child, $this->children);
        if ($index !== false) {
            $tmp = $this->children[$index];
            if (isset($tmp->id)) {
                unset($this->id2index[$tmp->id]);
            }
            if (isset($tmp->name)) {
                unset($this->name2index[$tmp->name]);
            }
            unset($this->children[$index]);
            return $tmp;
        }
        return null;
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
        return array();
    }

    /**
     * parses children & adds them into given fieldset
     *
     * @param object                 $config    Configuration
     * @param Form_Elements_Fieldset $container fieldset
     *
     * @return void
     */
    public static function parseChildren($config, Form_Elements_Fieldset $container)
    {
        if (isset($config->items) and is_array($config->items)) {
            foreach ($config->items as $item) {
                if (isset($item->xtype)) {
                    switch ($item->xtype) {
                    case 'textfield':
                        if (isset($item->inputType) and $item->inputType == 'password') {
                            $model = 'Form_Elements_PasswordField';
                        } else {
                            $model = 'Form_Elements_TextField';
                        }
                        break;
                    case 'numberfield':
                        $model = 'Form_Elements_NumberField';
                        break;
                    case 'combo':
                        $model = 'Form_Elements_ResourceField';
                        break;
                    case 'datefield':
                        $model = 'Form_Elements_DateField';
                        break;
                    case 'radiogroup':
                        $model = 'Form_Elements_RadioGroup';
                        break;
                    case 'checkbox':
                        $model = 'Form_Elements_Checkbox';
                        break;
                    case 'fieldset':
                        $model = Form_Elements_Fieldset::fromJson($item, $container);
                        break;
                    case 'textarea':
                        $model = 'Form_Elements_TextArea';
                        break;
                    default:
                        $model = null;
                    }
                    if (is_string($model)) {
                        $el = $model($item, $container);
                        $container->addChild($el);
                    } elseif (!is_null($model)) {
                        $container->addChild($model);
                    }
                }
            }
        }
    }

    /**
     * parses config object from extjs
     *
     * @param object    $config lazy configuration of extjs
     * @param Form_Form $form   Form
     *
     * @return Form_Elements_Fieldset
     */
    public static function fromJson($config, Form_Elements_Fieldset $form = null)
    {
        $fieldset = new Form_Elements_Fieldset($config, $form);
        $children = array();
        $columns = count($config->items);
        $rows = count($config->items[0]->items);
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $columns; $j++) {
                if (isset($config->items[$j]->items[$i])) {
                    $children[] = $config->items[$j]->items[$i];
                }
            }
        }
        $tmp = new stdClass();
        $tmp->items = $children;
        self::parseChildren($tmp, $fieldset);
        $form->addChild($fieldset);
        if (!is_null($form)) {
            foreach ($fieldset->children as $child) {
                $form->addChild($child);
            }
        }
        return $fieldset;
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
        return parent::html($renderer);
    }
}
