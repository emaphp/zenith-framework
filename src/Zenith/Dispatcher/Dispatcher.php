<?php
namespace Zenith\Dispatcher;

use Zenith\Application;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;
use Zenith\SOAPService;
use Zenith\Exception\SOAPServiceException;
use Injector\Injector;

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
		Application::getInstance()->getErrorHandler()->safeMode(false);
		
		//build request
		$request = new Request();
		
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
		if (isset($parameter->any)) {
			if (is_array($parameter->any) && array_key_exists('text', $parameter->any)) {
				//text only parameter
				$request->setParameter($parameter->any['text']);
			}
			else {
				//XML parameter
				$request->setParameter($parameter->any);
			}
		}
		else {
			$request->setParameter(null);
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
			$serviceObj = Injector::create($class);
		
			if (!($serviceObj instanceof SOAPService)) {
				throw new \RuntimeException("Class '$class' is not a valid service!");
			}
			
			if (!method_exists($serviceObj, $method)) {
				throw new \RuntimeException("Operation '{$method}' is not available in service '$class'!");
			}
		
			//build (default) response
			$response->setService($class, $method);
			$response->setStatus(0, 'Ok');
			
			//invoke service
			$result = $serviceObj->$method($request, $response);
			
			if (!is_null($result)) {
				$response->setResult($result);
			}

			//build response
			$resp = [
				'service' => $response->getService(),
				'status'  => $response->getStatus(),
				'result'  => ['any' => $response->getResult()]
			];
			return $resp;
		}
		catch (SOAPServiceException $se) {
			//log exception
			Application::getInstance()->getErrorHandler()->logException($se);
			
			//obtain status code and message from exception
			$response->setStatus($se->getStatusCode(), $se->getStatusMessage());
			
			//build generated response
			$resp = [
				'service' => $response->getService(),
				'status' => $response->getStatus(),
				'result' => ['any' => $response->getResult()]
			];
			return $resp;
		}
		catch (\SoapFault $sf) {
			//log exception
			Application::getInstance()->getErrorHandler()->logException($sf);
			set_exception_handler(null);
			throw $sf;
		}
		catch (\Exception $e) {
			//log exception
			Application::getInstance()->getErrorHandler()->logException($e);
			set_exception_handler(null);
			$sf = new \SoapFault("Server", $e->getMessage());
			throw $sf;
		}
	}
}