<?php

class Form_Elements_RadioGroup extends Form_Element
{
    /**
     * constructs element
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
                'items' => 'is_array',
                'defaultValue' => 'is_int'
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
        if (isset($this->defaultValue) and !empty($this->readonly) and $value != $this->defaultValue) {
            $errors['readonly'] = 'this element is readonly';
        }
        return $errors;
    }

    /**
     * generates html presentation of element
     * rendered must be set to true after execution
     *
     * @param AgaviRenderer $renderer the templating engine that will be used for rendering element by calling render method of it
     *
     * @return string
     */
    public function html(AgaviRenderer $renderer=null)
    {
        $i = 0;
        foreach ($this->items as $item) {
            $item['checked'] = $item['value'] == $this->value;
            $item['id'] = $i++;
        }
        return parent::html($renderer);
    }

    /**
     * prepares value before checking regular validation check
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function setValue($value)
    {
        $value = (int) $value;
        $value = parent::setValue($value);
        return $value;
    }
}
