<?php
namespace Zenith\View;

use Zenith\View\GenericView;

class ViewManager {
	/**
	 * Supported extension (extension => engine)
	 * @var array
	 */
	public $extensions = array('php' => 'default', 'twig' => 'twig');
	
	public function __construct($twig_config) {
		//template engines
		$this->engines = array('default' => new GenericView(VIEWS_DIR),
							   'twig' => new \Twig_Environment(new \Twig_Loader_Filesystem(VIEWS_DIR), $twig_config));
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
			throw new \InvalidArgumentException("Parameter 'view' is not a valid string");
		}
				
		//check if extension is especified
		$filename = basename($view);
		$regex = '(.*)\.(' . implode('|', array_keys($this->extensions)) . ')$';
		
		if (preg_match($regex, $filename, $matches)) {
			$engine = $matches[2];
			
			if (!file_exists(VIEWS_DIR . $filename)) {
				throw new \InvalidArgumentException("View '$view' does not exists");
			}
			
			return $this->engines[$engine]->render($view, $params);
		}
		
		//try appending all possible extensions to view
		foreach ($this->view_list($view) as $engine => $file) {
			if (file_exists(VIEWS_DIR . $file)) {
				return $this->engines[$engine]->render($view, $params);
			}
		}
		
		throw new \InvalidArgumentException("View '$view' does not exists");
	}
}