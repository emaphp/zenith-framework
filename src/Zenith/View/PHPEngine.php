<?php
namespace Zenith\View;

class PHPEngine {
	/**
	 * Views directory
	 * @var string
	 */
	protected $dir;
	
	public function __construct($views_dir) {
		$this->dir = $views_dir;
	}
	
	/**
	 * Builds a view from a generic template
	 * @param string $view
	 * @param array $args
	 * @return string
	 */
	public function render($view, $args = null) {
		//remove separators
		$explode = explode(DIRECTORY_SEPARATOR, $view);
		$view = $this->dir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $explode);
		
		if (!file_exists($view)) {
			throw new \InvalidArgumentException("View '$view' does not exists");
		}
		
		//default arguments
		$args = is_array($args) ? $args : array();
		
		//start buffer
		ob_start();
		//buidl content
		extract($args);
		include $filename;
		$content = ob_get_clean();
		return $content;
	}
}