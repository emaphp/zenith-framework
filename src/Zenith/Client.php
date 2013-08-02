<?php
namespace Zenith;

use Zenith\SOAP\Response;

class Client extends \SoapClient {
	/**
	 * Service class
	 * @var string
	 */
	protected $class;
	
	/**
	 * Service method
	 * @var string
	 */
	protected $method;
	
	/**
	 * Configuration options
	 * @var array
	 */
	protected $options = array();
	
	/**
	 * Request parameter
	 * @var string
	 */
	protected $parameter;
	
	/**
	 * Returned response
	 * @var array
	 */
	protected $rawResponse;
	
	/**
	 * Sets the values for the service section
	 * @param string $class
	 * @param string $method
	 */
	public function setService($class, $method) {
		$this->class = $class;
		$this->method = $method;
	}
	
	/**
	 * Sets/Adds an option to the configuration section
	 * @param string $name
	 * @param string $value
	 */
	public function setOption($name, $value) {
		$this->options[$name] = $value;
	}
	
	/**
	 * Sets the parameter section
	 * @param unknown $parameter
	 */
	public function setParameter($parameter) {
		$this->parameter = $parameter;
	}

	/**
	 * Obtains the response obtained from server
	 * @return array
	 */
	public function getRawResponse() {
		return $this->rawResponse;
	}

	/**
	 * Obtains the response returned from the service
	 * @throws \RuntimeException
	 * @return \Zenith\SOAP\Response
	 */
	public function getResponse() {
		if (!isset($this->rawResponse)) {
			throw new \RuntimeException("No request has been made!");
		}
		
		if (is_soap_fault($this->rawResponse)) {
			throw new \RuntimeException("Server returned a SOAP Fault!");
		}
		
		//build response and return
		$response = new Response();
		$response->setService($this->rawResponse['service']->class, $this->rawResponse['service']->method);
		$response->setStatus($this->rawResponse['status']->code, $this->rawResponse['status']->message);
		$response->setResult($this->rawResponse['result'], false);
		
		return $response;
	}
	
	/**
	 * Obtains the fault code from the response
	 * @throws \RuntimeException
	 */
	public function getFaultCode() {
		if (!isset($this->rawResponse)) {
			throw new \RuntimeException("No request has been made!");
		}
		
		return $this->rawResponse->faultcode;
	}
	
	/**
	 * Obtains the fault string from the response
	 * @throws \RuntimeException
	 */
	public function getFaultString() {
		if (!isset($this->rawResponse)) {
			throw new \RuntimeException("No request has been made!");
		}
		
		return $this->rawResponse->faultstring;
	}
	
	/**
	 * Invokes the 'execute' operation
	 * @return boolean If the request was successful
	 */
	public function invoke() {
		//build service section
		$service = new \stdClass();
		$service->class = $this->class;
		$service->method = $this->method;
		
		//buid options section
		$configuration = new \stdClass();
		
		if (!empty($this->options)) {
			if (count($this->options) == 1) {
				$configuration->option = new \stdClass();
				$configuration->option->name = key($this->options);
				$configuration->option->value = current($this->options);
			}
			else {
				$configuration->option = array();
				
				foreach ($this->options as $k => $v) {
					$option = new \stdClass();
					$option->name = $k;
					$option->value = $v;
					$configuration->option[] = $option;
				}
			}
		}
		
		//build parameter section
		$parameter = new \stdClass();
		$parameter->any = $this->parameter;
		
		//invoke server
		$this->rawResponse = $this->__soapCall('execute', array('service' => $service,
																'configuration' => $configuration,
																'parameter' => $parameter));
		
		//validate response
		return !is_soap_fault($this->rawResponse);
	}
}