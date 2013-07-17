<?php
$config = array(		
		/**
		 * Dispatcher class
		 * This class implements the method declared in the WSDL
		 */
		'dispatcher' => 'Bleach\SOAP\Dispatcher',
		
		/**
		 * Autoloader configuration
		 * Additional directories to add to the application autoloader (namespace => directory)
		 */
		'autoload' => array('' => SERVICES_DIR),
		
		/**
		 * Twig configuration
		 * Configuration vars for Twig
		 */
		'twig' => array('cache' => false)
);