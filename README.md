Agavi-Form-Models-SET
====================

This is a set of models for Agavi framework which helps you create forms and validate user input based on them.

Installation
------------

make a module called Form in your project then put contents of src into models directory of your Form module:

	$ cd /path/to/your/agavi/project
	$ agavi module-create
	...
	Module name: Form
	...
	$ cp -r /path/to/agavi-form-models-set/src/* app/modules/Form/models/

also you need to load css/js files in client directory into your page.

### I have a Form module already, what should i do?

you can put models into your existing Form module, but if there's any name conflict or you just don't want to do it make WhatEverYouWant module & replace all `Form_` with `WhatEverYouWant_` in models:

	$ find /path/to/agavi-form-models-set/src -name "*php" -exec sed -i 's/Form_/WhatEverYouWant_/g' {} \;


How to use it?
--------------

### Making a form

	<?php

	// reduce method calls
	$context = $this->getContext();
	$tm = $context->getTranslationManager();

	$form = $context->getModel('Form', 'Form', array(
		array(
			'id' => 1,
			'title' => $tm->_('my new form', 'default.Form'),
			'description' => $tm->_('a description/help which will be shown to user', 'default.Form'),
			'action' => $this->getContext()->getRouting()->gen(null),
			'method' => 'post'
		),
		null
	));

	$fieldset = $context->getModel('Elements.Fieldset', 'Form', array(
		array(
			'id' => 2,
			'title' => $tm->_('user info', 'default.Form')
		),
		$form
	));

	$firstname = $context->getModel('Elements.TextField', 'Form', array(
		array(
			'id' => 3,
			'title' => $tm->_('first name', 'default.Form'),
			'name' => 'firstname',
			'required' => true,
			'min' => 2,
			'max' => 16
		),
		$fieldset
	));

	$lastname = $context->getModel('Elements.TextField', 'Form', array(
		array(
			'id' => 4,
			'title' => $tm->_('last name', 'default.Form'),
			'name' => 'lastname',
			'required' => true,
			'min' => 2,
			'max' => 32
		),
		$fieldset
	));

	$hasEmail = $context->getModel('Elements.Checkbox', 'Form', array(
		array(
			'id' => 5,
			'title' => $tm->_('subscribe?', 'default.Form'),
			'name' => 'hasEmail'
		),
		$fieldset
	));

	$email = $context->getModel('Elements.TextField', 'Form', array(
		array(
			'id' => 6,
			'title' => $tm->('email', 'default.Form'),
			'name' => 'email',
			'required' => true,
			'parents' => array(
				// will be validated if hasEmail has been checked
				5 => array(
					'opration' => '==',
					'condition' => true
				)
			),
			'regex' => '/^[a-z0-9_\.]+@[a-z0-9\.]+\.[a-z]{2,3}$/i',
		),
		$fieldset
	));

	$fieldset->addChild($firstname);
	$fieldset->addChild($lastname);
	$fieldset->addChild($hasEmail);
	$fieldset->addChild($email);
	$form->addChild($fieldset);

	// $form is ready
	?>

also you can make a form using lazy config

	<?php
	// think we have fetched $config from mongodb/couchdb/redis/...
	$config = array(
		'id' => 1,
		'title' => 'our form title',
		'description' => 'description of our form',
		'items' => array(
			array(
				'xtype' => 'textfield',
				'name' => 'fieldname'
			)
		)
	);
	$form = Form_FormModel::fromJson($config);
	$form->action = $this->getContext()->getRouting('my.routing');
	?>

### You said i can validate user input by a form, how's that?

	<?php
	// do such thing in register[Method]Validators method of your action
	// of course you need to autoload/load Form_ValidatorModel
	Form_ValidatorModel::registerValidators(
		$yourForm,
		$this->getContainer()->getValidationManager(),
		$this->getContext()->getRequest()->getRequestData()->getParameters(),
		$this->getContext()->getRequest()->getRequestData()->getFiles()
	);
	?>

### how to render a form?
just pass your `Form_FormModel` instance to your template/view & render it like `$form->html()`.

### What is form's markup?
generated markup is based on twitter-bootstrap.

### What about other output types?
only html is supported, you have to implement other representations if you need.

### is there any support for client validation?
yes, currently jQueryValidationEngine is supported, when you're rendering the form pass client validation as first parameter like: `$form->html('jQueryValidationEngine')`.

### I want to use another client validation, what should i do?
`jQueryValidationEngine` is a method on elements, output of `html` method will be passed to it so we alter html markup for client validation. you can do the same. just make your validation method on elements & let html method know what's it.

### any support for conditional elements?
Yes, take a look at above example, as you can see email will be validated if `subscribe?` checkbox has been checked by user.

### I've a complex element how i can validate it?
define `registerValidators` method on your element, you will get following parameters: `AgaviValidationManager $vm, array $depends, array $unvalidatedParameters, array $unvalidatedFiles` so you can register your validators into AgaviValidationManager.

### what is resource element?
think it's a replacement for html's select element based on autocomplete of jquery-ui so you can expect anything you expect from jquery-ui autocomplete but there's more: think you want to load cities based on states, for example only show cities of New York when user has selected New York as State.
