<?php
namespace Zenith\WSDL;

use Zenith\Application;
use Zenith\WSDL\WSDLService;

class DefaultWSDLService extends WSDLService {
	/**
	 * Returns a rendered WSDL file
	 * (non-PHPdoc)
	 * @see \Zenith\WSDL\WSDLService::render()
	 */
	public function render() {
		//get wsdl configuration
		$app = Application::getInstance();
		$config = $app->load_config('wsdl');
		
		//obtain template
		if (!array_key_exists('template', $config) || !is_string($config['template']) || empty($config['template'])) {
			throw new \RuntimeException("No WSDL template found");
		}
		
		//build template
		$template = $config['template'];
		
		if (array_key_exists('template_params', $config) && is_array($config['template_params'])) {
			$template_params = $config['template_params'];
		}
		else {
			//obtain service uri from main configuration
			$config = $app->load_config('app');
			
			if (!array_key_exists('uri', $config) || !is_string($config['uri']) || empty($config['uri'])) {
				throw new \RuntimeException("No service URI specified");
			}
			
			//build default parameter list
			$template_params = array('uri' => $config['uri']);
		}

		return $this->view->render($template, $template_params);
	}
} 