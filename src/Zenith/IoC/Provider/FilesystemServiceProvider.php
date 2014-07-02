<?php
namespace Zenith\IoC\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemServiceProvider implements ServiceProviderInterface {
	public function register(Container $container) {
		$container['fs'] = function($c) {
			return new Filesystem;
		};
	}
}
?>