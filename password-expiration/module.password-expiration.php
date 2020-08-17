<?php
/**
 * @copyright   Copyright (C) 2020 ops-itop
 * @license     NIT
 */

SetupWebPage::AddModule(
	__FILE__,
	'password-expiration/1.0.0',
	array(
		// Identification
		//
		'label' => 'Password Expiration',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'authent-local/2.7.0',
		),
		'mandatory' => false,
		'visible' => true, // To prevent auto-install but shall not be listed in the install wizard

		// Components
		//
		'datamodel' => array(
			'model.password-expiration.php',
		),
		'data.struct' => array(
		),
		'data.sample' => array(
		),
		
		// Documentation
		//
		'doc.manual_setup' => '',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(
		),
	)
);
