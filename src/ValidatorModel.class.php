<?php

class Form_ValidatorModel extends AgaviValidator
{
	public static function registerValidators(Form_FormModel $form, AgaviValidationManager $vm, array $parameters, array $files=array())
	{
		$conditions = array();
		foreach($form->getChildren() as $child)
		{
			if($child instanceof Form_Elements_FieldsetModel)
				continue;
			$parents = (array) $child->parents;
			$depends = array();
			foreach($parents as $id => $condition)
			{
				$depend = bin2hex("{$id}_{$condition['opration']}_{$condition['condition']}");
				$parent = $form->getChildById($id);
				if(is_null($parent))
				{
					throw new Exception("can't find parent with id={$id} for {$child->id}");
				}
				if(!isset($conditions[$depend]))
				{
					$vm->createValidator(
						'Form_ValidatorModel',
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
			if(is_callable(array($child, 'registerValidators')))
			{
				$child->registerValidators($vm, $depends, $parameters, $files);
			}
			else
			{
				$vm->createValidator(
					'Form_ValidatorModel',
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

	function validate()
	{
		$element = $this->getParameter('element');
		if(!$element instanceof Form_ElementModel)
		{
			$model = $this->getParameter('model');
			$module = $this->getParameter('module');
			$config = $this->getParameter('configuration');
			$element = $this->getContext()->getModel($model, $module, array($config));
		}

		try
		{
			$element->value = $this->getData($this->getArgument());
			$this->export($element->value);
			return true;
		}
		catch(Form_ValidationExceptionModel $e)
		{
			$this->errorMessages = $e->getValidationErrors();
			foreach($this->errorMessages as $error => $message)
			{
				$this->throwError($error);
			}
			return false;
		}
	}
}
