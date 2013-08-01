<?php
namespace Zenith\SOAP;

use Zenith\Application;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;
use Zenith\Service;
use Zenith\Exception\ServiceException;

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
		//build request
		$request = new Request($service, $configuration, $parameter);
		
		//build response
		$response = new Response();
		
		try {
			//get class and method tags
			$class = $service->class;
			$method = $service->method;
			
			//security check: avoid calling magic methods
			if (preg_match('@^__@', $method)) {
				throw new \RuntimeException("Operation '{$method}' cannot be invoked!");
			}
		
			//fix class namespace reference
			if (strstr($class, '/')) {
				$class = str_replace('/', '\\', $class);
			}
		
			//create service instance
			$serviceObj = new $class;
		
			if (!($serviceObj instanceof Service)) {
				throw new \RuntimeException("Class '$class' is not a valid service!");
			}
			
			if (!method_exists($serviceObj, $method)) {
				throw new \RuntimeException("Operation '{$method}' is not available in service '$class'!");
			}
		
			//setup service
			$serviceObj->__setup();
				
			//build response
			$response->setService($class, $method);
			$response->setStatus(0, 'Ok');
			
			//invoke service
			$result = $serviceObj->$method($request, $response);
			
			if (!is_null($result)) {
				$response->setResult($result);
			}
		
			return $response->build();
		}
		catch (ServiceException $se) {
			//log exception
			Application::getInstance()->error_handler->logException($se);
			//obtain status code and message from exception
			$response->setStatus($se->getStatusCode(), $se->getStatusMessage());
			//build generated response
			return $response->build();
		}
		catch (\SoapFault $sf) {
			//log exception
			Application::getInstance()->error_handler->logException($sf);
			set_exception_handler(null);
			throw $sf;
		}
		catch (\Exception $e) {
			//log exception
			Application::getInstance()->error_handler->logException($se);
			set_exception_handler(null);
			$sf = new \SoapFault("Server", $e->getMessage());
			throw $sf;
		}
	}
}