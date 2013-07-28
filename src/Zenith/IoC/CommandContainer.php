<?php
namespace Zenith\IoC;

use Zenith\Application;
use Zenith\View\View;
use Injector\Container;
use Symfony\Component\Filesystem\Filesystem;

class CommandContainer extends Container {
	public function configure() {
		//load configuration
		$config = Application::getInstance()->load_config('app');
		$twig_config = array_key_exists('twig', $config) && is_array($config['twig']) ? $config['twig'] : array();
		
		//create 'view' service
		$this['view'] = function ($c) use ($twig_config) {
			return new View($twig_config);
		};
		
		//create filesystem service
		$this['fs'] = function ($c) {
			return new Filesystem();
		};
	}
}