<?php
/**
 * @copyright   Copyright (C) 2020 ops-itop
 * @license     NIT
 */

SetupWebPage::AddModule(
	__FILE__,
	'opsitop-main/1.0.0',
	array(
		// Identification
		//
		'label' => 'Learn iTop Module',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-config-mgmt/2.7.0',
			'teemip-ip-mgmt/2.6.1'
		),
		'mandatory' => false,
		'visible' => true, // To prevent auto-install but shall not be listed in the install wizard

		// Components
		//
		'datamodel' => array(
			'model.opsitop-main.php',
			'main.opsitop-main.php',
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
