<?php
namespace Zenith\IoC;

use Zenith\Application;
use Zenith\Event\IEventHandler;

class ApplicationContainer extends Container {
	public function __construct($environment, &$loader) {
		//application environment
		$this['environment'] = $environment;
		
		//application autoloader
		$this['loader'] = $loader;
	}
	
	public function configure() {
		//obtain configuration
		$config = Application::getInstance()->load_config('app', $this['environment']);
		
		if (is_null($config)) {
			throw new \RuntimeException("No configuration file found");
		}
		
		//add directories for autoloading
		if (array_key_exists('autoload', $config) && is_array($config['autoload'])) {
			foreach ($config['autoload'] as $ns => $dir) {
				$this['loader']->add($ns, $dir);
			}
		}
		
		//obtain additional dependencies
		$inject = array_key_exists('inject', $config) && is_array($config['inject']) ? $config['inject'] : array();
		
		//logger object
		if (!array_key_exists('logger', $inject) || !is_string($inject['logger']) || empty($inject['logger'])) {
			throw new \RuntimeException("No application logger found");
		}
		
		$app_logger = $inject['logger'];
		
		$this['logger'] = $this->share(function ($c) use ($app_logger) {
			return new $app_logger;
		});
		
		//event handler object
		if (!array_key_exists('event', $inject) || !is_string($inject['event']) || empty($inject['event'])) {
			throw new \RuntimeException("No event handler found");
		}
		
		$app_event = $inject['event'];
		
		$this['event'] = $this->share(function ($c) use ($app_event) {
			return new $app_event($c['logger']);		
		});
		
		//obtain event handler
		$event_handler = $this['event'];
		
		if (!($event_handler instanceof IEventHandler)) {
			throw new \RuntimeException("Property 'event' is not a valid IEventHandler instance");
		}
		
		//bind events to class
		set_error_handler(array($event_handler, 'error_handler'));
		set_exception_handler(array($event_handler, 'exception_handler'));
		register_shutdown_function(array($event_handler, 'shutdown_handler'));
		
		//additional dependencies
		$inject = array_diff_key($inject, array_flip(array('logger', 'event')));
		
		do {
			$property = key($inject);
			$value = current($inject);
			
			if (!is_string($property) || empty($property)) {
				throw new \RuntimeException("Injected property not valid");
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