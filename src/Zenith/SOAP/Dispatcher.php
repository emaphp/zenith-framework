<?php
namespace Zenith\SOAP;

use Zenith\Application;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;
use Zenith\Service;

class Dispatcher {
	/**
	 * Main execution method
	 * @param \stdClass $service
	 * @param \stdClass $configuration
	 * @param \stdClass $parameter
	 * @throws \RuntimeException
	 * @return array
	 */
	public function execute($service, $configuration, $parameter) {
		$request = new Request($service, $configuration, $parameter);
		
		//get class and method tags
		$service_class = $service->class;
		$service_method = $service->method;
		
		//security check: avoid calling core classes
		if (class_exists($service_class)) {
			throw new \RuntimeException("Access to class '$service_class' is forbidden");
		}
	
		//security check: avoid calling magic methods
		if (preg_match('@^__@', $method)) {
			throw new \RuntimeException("Operation '{$service_method}' cannot be invoked");
		}
	
		//create service instance
		$serviceObj = new $service_class;
	
		if (!($serviceObj instanceof Service)) {
			throw new \RuntimeException("Class '$service_class' is not a valid service");
		}
		
		if (!method_exists($serviceObj, $method)) {
			throw new \RuntimeException("Operation '{$method}' is not available on this service");
		}
	
		//create container
		$container_class = $serviceObj->container;
		$container = new $container_class;
		$container->configure();
		
		//inject dependencies
		$container->injectAll($serviceObj);
			
		//build response
		$response = new Response();
		$response->setService($service_class, $service_method);
		$response->setStatus(0, 'Ok');
		
		//invoke service
		$result = $serviceObj->$service_method($request, $response);
		
		if (!is_null($result)) {
			$response->setResult($result);
		}
	
		return $response->build();
	}
}