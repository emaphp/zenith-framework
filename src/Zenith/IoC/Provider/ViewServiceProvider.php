<?php
namespace Zenith\IoC\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Zenith\Application;

class ViewServiceProvider implements ServiceProviderInterface {
	public function register(Container $container) {
		$container['view'] = function ($c) {
			//load configuration
			$config = Application::getInstance()->load_config('app');
			$twig_config = array_key_exists('twig', $config) && is_array($config['twig']) ? $config['twig'] : [];
			return new View($twig_config);
		};
	}
}
?>