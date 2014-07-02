<?php
namespace Zenith\View;

use Zenith\View\Engine\PHPEngine;
use Zenith\Application;

class View {
	/**
	 * Template engines
	 * @var array
	 */
	protected $engines;
	
	/**
	 * Supported extension (extension => engine)
	 * @var array
	 */
	protected $extensions = [
		'php' => 'default',
		'twig' => 'twig'
	];
	
	public function __construct($twig_config) {
		$views_dir = Application::getInstance()->path('views');
				
		//template engines
		$this->engines = [
			'default' => new PHPEngine($views_dir),
			'twig'    => new \Twig_Environment(new \Twig_Loader_Filesystem($views_dir), $twig_config)
		];
	}
	
	/**
	 * Returns all possible template files for a view
	 * @param string $view
	 * @return array
	 */
	protected function view_list($view) {
		$files = array();
		
		foreach ($this->extensions as $ext => $engine) {
			$files[$engine] = $view . '.' . $ext;
		}
		
		return $files;
	}
	
	/**
	 * Renders a view and returns its contents
	 * @param string $view
	 * @param string $params
	 * @throws \InvalidArgumentException
	 */
	public function render($view, $params = null) {
		if (!is_string($view) || empty($view)) {
			throw new \InvalidArgumentException("Parameter 'view' is not a valid string!");
		}
				
		//check if extension is especified
		$filename = basename($view);
		$regex = '/(.*)\.(' . implode('|', array_keys($this->extensions)) . ')$/';
		
		if (preg_match($regex, $filename, $matches)) {
			$extension = $matches[2];
			
			if (!array_key_exists($extension, $this->extensions)) {
				throw new \RuntimeException("No suitable engine found for extension '$engine'!");
			}
			
			$engine = $this->extensions[$extension];
			$path = Application::getInstance()->path('views', $view);
			
			if (!file_exists($path)) {
				throw new \InvalidArgumentException("View '$view' does not exists!");
			}
			
			return $this->engines[$engine]->render($view, $params);
		}
		
		//try appending all possible extensions to view
		foreach ($this->view_list($view) as $engine => $file) {
			$path = Application::getInstance()->path('views', $file);
			
			if (file_exists($path)) {
				return $this->engines[$engine]->render($file, $params);
			}
		}
		
		throw new \InvalidArgumentException("View '$view' does not exists!");
	}
}