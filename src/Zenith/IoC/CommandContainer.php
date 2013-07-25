<?php
namespace Zenith\IoC;

use Zenith\Application;
use Zenith\View\View;
use Injector\Container;

class CommandContainer extends Container {
	public function configure() {
		//load configuration
		$config = Application::getInstance()->load_config('app');
		$twig_config = array_key_exists('twig', $config) && is_array($config['twig']) ? $config['twig'] : array();
		
		$this['view'] = function ($c) use ($twig_config) {
			return new View(VIEWS_DIR, $twig_config);
		};
	}
}