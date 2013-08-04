<?php
namespace Zenith\Dispatcher\SOAP;

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
		//avoid throwing custom faults
		Application::getInstance()->error_handler->safe_mode = false;
		
		//build request
		$request = new Request($service, $configuration, $parameter);
		
		//set service section
		$request->setService($service->class, $service->method);
		
		//set configuration
		if (isset($configuration->option) && is_array($configuration->option)) {
			foreach ($configuration->option as $option) {
				$request->setOption($option->name, $option->value);
			}
		}
		elseif (isset($configuration->option)) {
			$option = $configuration->option;
			$request->setOption($option->name, $option->value);
		}
		
		//obtain parameter
		if (is_array($parameter->any) && array_key_exists('text', $parameter->any)) {
			$request->setParameter($parameter->any['text']);
		}
		else {
			$request->setParameter($parameter->any);
		}
		
		$request->setRawParameter($parameter);
		
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

			//build response
			$resp = array('service' => $response->getService(),
						  'status' => $response->getStatus(),
						  'result' => array('any' => $response->getResult()));
			return $resp;
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
			Application::getInstance()->error_handler->logException($e);
			set_exception_handler(null);
			$sf = new \SoapFault("Server", $e->getMessage());
			throw $sf;
		}
	}
}