<?php
namespace Zenith;

use Zenith\Application;
use Zenith\WSDL\WSDLService;

class MainController {
	/**
	 * Handles upcoming request
	 * @throws \RuntimeException
	 * @throws \RuntimeConfiguration
	 */
	public function service() {
		//obtain configuration
		$app = Application::getInstance();
		
		//check if request tries to obtain WSDL
		if (array_key_exists('wsdl', $_GET)) {
			//load WSDL configuration
			$wsdl_config = $app->load_config('wsdl');
			
			if (is_null($wsdl_config) || !is_array($wsdl_config)) {
				throw new \RuntimeException("No WSDL configuration found");
			}
			
			if (!array_key_exists('service', $wsdl_config) || !is_string($wsdl_config['service']) || empty($wsdl_config['service'])) {
				throw new \RuntimeException("No WSDL service found");
			}
			
			//generate wsdl service
			$service = new $wsdl_config['service']();
			
			if (!($service instanceof WSDLService)) {
				throw new \RuntimeException("WSDL service not valid");
			}
			
			//generate container
			$container_class = $service->container;
			$container = new $container_class;
			$container->configure();
			
			//inject dependencies
			$container->injectAll($service);
			
			//run service
			$wsdl = $service->render();
			
			//print wsdl
			echo $wsdl;
			return;
		}
		
		//load main configuration file
		$config = $app->load_config('app');
		
		//obtain dispatcher class from config
		if (!array_key_exists('dispatcher', $config) || !is_string($config['dispatcher']) || empty($config['dispatcher'])) {
			throw new \RuntimeException("No dispatcher class defined");
		}
		
		//call soap dispatcher
		$dispatcher = new $config['dispatcher'];
		
		//load server config
		$server_config = $app->load_config('server');
		
		if (is_null($server_config)) {
			throw new \RuntimeConfiguration("No server configuration found");
		}
		
		//get server config vars
		$wsdl = array_key_exists('wsdl', $server_config) && is_string($server_config['wsdl']) && !empty($server_config['wsdl']) ? $server_config['wsdl'] : null;
		$options = array_key_exists('options', $server_config) && is_array($server_config['options']) ? $server_config['options'] : array();
		
		//initialize soap server
		$server = new \SoapServer($wsdl, $options);
		$server->setObject($dispatcher);
		$server->handle();
	}
}