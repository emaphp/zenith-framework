<?php
/**
 * Tests loading configuration values through the Zenith\Application class
 * @group application
 * Author: Emmanuel Antico
 */
use Zenith\Application;
use Injector\Injector;
use Monolog\Logger;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Loads a configuration file without environment
	 */
	public function testLoadConfig() {
		$app = Application::getInstance();
		$config = $app->load_config('wsdl');
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('template', $config);
		$this->assertEquals('application-wsdl', $config['template']);
		$this->assertArrayHasKey('args', $config);
		$this->assertTrue(is_array($config['args']));
		$this->assertArrayHasKey('uri', $config['args']);
		$this->assertEquals('http://my-domain.com/service.php', $config['args']['uri']);
		$app->clear_config('wsdl');
	}
	
	/**
	 * Loads a configuration file with a custom environment
	 */
	public function testLoadEnvironmentConfig() {
		$app = Application::getInstance();
		
		$config = $app->load_config('app', 'production');
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('dispatcher', $config);
		$this->assertEquals('Zenith\SOAP\Dispatcher', $config['dispatcher']);
		$app->clear_config('app');
		
		$config = $app->load_config('logger', 'production');
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('threshold', $config);
		$this->assertEquals(Logger::WARNING, $config['threshold']);
		$app->clear_config('logger');
	}
	
	/**
	 * Loads a configuration file with a previously defined environment
	 */
	public function testLoadApplicationConfig() {
		$app = Application::getInstance();
		$container = new Pimple\Container;
		$container['environment'] = 'development';
		Injector::inject($app, $container);
		
		$config = $app->load_config('app');
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('dispatcher', $config);
		$this->assertEquals('Zenith\SOAP\Dispatcher', $config['dispatcher']);
		$app->clear_config('app');
		
		$config = $app->load_config('logger');
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('threshold', $config);
		$this->assertEquals(Logger::DEBUG, $config['threshold']);
		$app->clear_config('logger');
	}
}