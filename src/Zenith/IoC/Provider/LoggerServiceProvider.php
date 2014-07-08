<?php
namespace Zenith\IoC\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Zenith\Application;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerServiceProvider implements ServiceProviderInterface {
	public function register(Container $container) {
		$container['logger'] = function ($c) {
			//load logger config
			$app = Application::getInstance();
			$config = $app->load_config('logger');
				
			//initialize logger instance
			$logger = new Logger($app->getEnvironment());
				
			if (is_array($config) && array_key_exists('path', $config) && !empty($config['path'])) {
				$log_path = $config['path'];
				
				if (is_callable($log_path)) {
					$log_path = call_user_func($log_path, $app);
				}
			}
			else {
				//build default
				$log_path = $app->path('logs', $app->getEnvironment() . '_' . date('Y-m-d') . '.log');
			}
			
			if (is_array($config) && array_key_exists('threshold', $config)) {
				$log_threshold = intval($config['threshold']);
			}
			else {
				$log_threshold = Logger::DEBUG;
			}
			
			//set handler
			$logger->pushHandler(new StreamHandler($log_path, $log_threshold));
			return $logger;
		};
	}	
}
?>