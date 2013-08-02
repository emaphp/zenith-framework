<?php
namespace Zenith\SOAP;

class Request {
	/**
	 * Conversion constants
	 */
	const AS_RAW = 0;
	const AS_XML = 1;
	const AS_SIMPLEXML = 2;
	const AS_DOM = 3;
	
	/**
	 * Service section
	 * @var \stdClass
	 */
	protected $service;
	
	/**
	 * Configuration section
	 * @var unknown
	 */
	protected $configuration = array();
	
	/**
	 * Request parameter
	 * @var string
	 */
	protected $parameter;
	
	public function __construct($service, $configuration, $parameter) {
		$this->service = $service;
		$this->parameter = $parameter;
		
		if (isset($configuration->option) && is_array($configuration->option)) {
			foreach ($configuration->option as $option) {
				$this->option($option->name, $option->value);
			}
		}
		elseif (isset($configuration->option)) {
			$option = $configuration->option;
			$this->option($option->name, $option->value);
		}
	}
	
	public function getService() {
		return $this->service;
	}
	
	public function getConfiguration() {
		return $this->configuration;
	}
	
	/**
	 * Obtains parameter in the requested form
	 * @param int $as
	 * @return SimpleXMLElement|\DOMDocument|string
	 */
	public function getParameter($as = self::AS_RAW) {
		//check if parameter is a simple string
		if (is_array($this->parameter->any) && array_key_exists('text', $this->parameter->any)) {
			return $this->parameter->any['text'];
		}
		elseif ($as == self::AS_XML) {
			return $this->parameter->any;
		}
		elseif ($as == self::AS_SIMPLEXML) {
			//convert to SimpleXMLElement
			$success = simplexml_load_string($this->parameter->any);
				
			if ($success === false) {
				$error = libxml_get_last_error();
				throw new \RuntimeException("XML Syntax error: " . $error->message);
			}
				
			return $success;
		}
		elseif ($as == self::AS_DOM) {
			//convert to DOMDocument
			$dom = new \DOMDocument();
				
			if (!$dom->loadXML($this->parameter->any)) {
				$error = libxml_get_last_error();
				throw new \RuntimeException("XML Syntax error: " . $error->message);
			}
				
			return $dom;
		}
		
		return $this->parameter;
	}
	
	/**
	 * Sets a request option
	 * @param string $name
	 * @param string $value
	 * @return NULL|\Zenith\SOAP\mixed
	 */
	public function option($name, $value = null) {
		if (is_null($value)) {
			if (!array_key_exists($name, $this->configuration)) {
				return null;
			}
			
			return $this->configuration[$name];
		}
		
		$this->configuration[$name] = $value;
	}
}