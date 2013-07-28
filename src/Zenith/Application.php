<?php
namespace Zenith;

class Application {
	/**
	 * Application configuration
	 * @var array
	 */
	public $config = array();
	
	/**
	 * Application environment
	 * @var string
	 */
	public $environment;
	
	/**
	 * Application paths
	 * @var array
	 */
	public $paths;
	
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
	 * Clears all configuration values associated to a configuration filename
	 * If no name is specified then all values are deleted
	 * @param string $name
	 * @return bool
	 */
	public function clear_config($name = null) {
		if (is_null($name)) {
			$this->config = array();
		}
		elseif (array_key_exists($name, $this->config)) {
			unset($this->config[$name]);
		}
		else {
			return false;
		}
		
		return true;
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
		$filename = $this->path('config', "/$name.php");
		
		if (file_exists($filename)) {	
			$app_config = require $filename;
		}
		else {
			$app_config = null;
		}
		
		if ($environment !== false) {
			$environment = is_null($environment) ? (is_null($this->environment) ? null : $this->environment) : $environment;
			
			if (!is_null($environment)) {
				//include environment file
				$env_filename = $this->path('config', "/$environment/$name.php");
					
				if (file_exists($env_filename)) {
					$config = require $env_filename;
						
					if (!is_null($app_config)) {
						$app_config = array_merge($app_config, $config);
					}
					else {
						$app_config = $config;
					}
				}
			}
		}
		
		if (!is_null($app_config)) {
			$this->config[$name] = $app_config;
		}
		
		return $app_config;
	}
	
	/**
	 * Builds a path with all components sent
	 * @param string $path
	 * @return string
	 */
	public function build_path($path) {
		$args = func_get_args();
		$n_path = count($args) > 1 ? implode(DIRECTORY_SEPARATOR, $args) : $path;
		$n_path = preg_replace('#' . DIRECTORY_SEPARATOR . '+#', DIRECTORY_SEPARATOR, $n_path);
		return $n_path;
	}
	
	/**
	 * Builds a path to a previously declared directory in bootstrap/paths.php file
	 * @param string $dir
	 * @param string $path
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public function path($dir, $path = null) {
		if (!array_key_exists($dir, $this->paths)) {
			throw new \InvalidArgumentException("No path found for '$path'!");
		}
		
		//build path
		$p = $this->build_path(getcwd(), $this->paths[$dir]);
		
		if (!is_null($path)) {
			$p = $this->build_path($p, $path);
		}
		
		return $p;
	}
}