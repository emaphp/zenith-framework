<?php
return [
		/**
		 * Dispatcher class
		 * This class implements the method declared in the WSDL
		 */
		'dispatcher' => 'Zenith\SOAP\Dispatcher',
		
		/**
		 * Application namespaces
		 * Use this key to indicate which namespaces are associated with classes stored in the 'services' and 'components' directories.
		 * Example: 'namespaces' => ['Acme\\', 'Company\\']
		 */
		'namespaces' => ['Acme\\'],
		
		/**
		 * Twig configuration
		 * Configuration vars for Twig
		 */
		'twig' => ['cache' => false]
];