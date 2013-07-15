<?php
namespace Zenith\SOAP;

class Request {
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
	
	public function getParameter() {
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