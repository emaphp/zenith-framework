<?php
namespace Zenith\View;

class GenericView {
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
		if (!preg_match('^/', $view)) {
			$view = $this->dir . $view;
		}
		
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