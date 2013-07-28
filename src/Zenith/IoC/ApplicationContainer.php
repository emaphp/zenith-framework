<?php
namespace Zenith\IoC;

use Zenith\Application;
use Zenith\Event\IEventHandler;
use Injector\Container;
use Zenith\Error\ErrorHandler;
use Monolog\Logger;

class ApplicationContainer extends Container {	
	public function configure() {
		//get composer autoloader
		$loader = require 'vendor/autoload.php';
		
		//obtain configuration
		$config = Application::getInstance()->load_config('app');
		
		if (is_null($config)) {
			throw new \RuntimeException("No configuration file found!");
		}
		
		//setup application namespaces
		if (array_key_exists('namespaces', $config) && is_array($config['namespaces'])) {
			foreach ($config['namespaces'] as $ns) {
				$loader->add($ns, Application::getInstance()->path('services'));
				$loader->add($ns, Application::getInstance()->path('components'));
			}
		}
		
		//obtain additional dependencies
		$inject = array_key_exists('inject', $config) && is_array($config['inject']) ? $config['inject'] : array();
		
		//logger object
		if (!array_key_exists('logger', $inject) || !is_string($inject['logger']) || empty($inject['logger'])) {
			throw new \RuntimeException("No application logger found!");
		}
		
		//setup logger object
		$logger = $inject['logger'];
		
		$this['logger'] = $this->share(function ($c) use ($logger) {
			$logger_instance = new $logger;
			
			if (!($logger_instance instanceof Logger)) {
				throw new \RuntimeException("Class '$logger' is not a valid instance of Monolog\Logger!");
			}
			
			return $logger_instance;
		});
		
		//setup error handler object
		$this['error_handler'] = $this->share(function($c) {
			$errorHandler = new ErrorHandler($c['logger']);
			return $errorHandler;
		});

		//get error handler
		$error_handler = $this['error_handler'];
		
		//bind events to class
		set_error_handler(array(&$error_handler, 'error_handler'));
		set_exception_handler(array(&$error_handler, 'exception_handler'));
		register_shutdown_function(array(&$error_handler, 'shutdown_handler'));
		
		//additional dependencies
		$inject = array_diff_key($inject, array_flip(array('logger')));
		
		if (!empty($inject)) {
			do {
				$property = key($inject);
				$value = current($inject);
					
				if (!is_string($property) || empty($property)) {
					throw new \RuntimeException("Injected property is not valid!");
				}
					
				//generate object from class name
				if (is_string($value)) {
					$this[$property] = function ($c) use ($value) {
						return new $value;
					};
				}
				else {
					$this[$property] = $value;
				}
			} while (next($inject));
		}
	}
}