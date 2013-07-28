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
	
	protected $service;
	protected $configuration = array();
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
	
	public function getParameter($as = self::AS_RAW) {
		if (isset($this->parameter->any)) {
			if ($as == self::AS_XML) {
				return $this->parameter->any;
			}
			elseif ($as == self::AS_SIMPLEXML) {
				return simplexml_load_string($this->parameter->any);
			}
			elseif ($as == self::AS_DOM) {
				$dom = new \DOMDocument();
				$dom->loadXML($this->parameter->any);
				return $dom;
			}
		}
		
		return $this->parameter;
	}
	
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