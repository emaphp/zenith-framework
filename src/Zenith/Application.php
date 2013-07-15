<?php
namespace Zenith;

class Application {
	/**
	 * Application configuration
	 * @var array
	 */
	public $config = array();
	
	/**
	 * Class instance
	 * @var Zenith\Application
	 */
	protected static $instance = null;
	
	private function __construct() {
		
	}
	
	/**
	 * Obtain application instance
	 * @return Zenith\Application
	 */
	public static function &getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new Application;
		}
		
		return self::$instance;
	}

	/**
	 * Loads a configuration array and stores its values
	 * @param string $name
	 * @param string $environment
	 * @return array|NULL
	 */
	public function load_config($name, $environment = null) {
		//check if is already loaded
		if (array_key_exists($name, $this->config)) {
			return $this->config[$name];
		}
		
		//include file from main directory
		$filename = CONFIG_DIR . "/$name.php";
		
		if (file_exists($filename)) {
			include $filename;
			$app_config = $config;
		}
		else {
			$app_config = null;
		}
		
		if ($environment !== false) {
			$environment = is_null($environment) ? $this->environment : $environment;
			
			//include environment file
			$env_filename = CONFIG_DIR . "/$environment/$name.php";
			
			if (file_exists($env_filename)) {
				include $env_filename;
					
				if (!is_null($app_config)) {
					$app_config = array_merge($app_config, $config);
				}
				else {
					$app_config = $config;
				}
			}
		}
		
		if (!is_null($app_config)) {
			$this->config[$name] = $app_config;
		}
		
		return $app_config;
	}
}