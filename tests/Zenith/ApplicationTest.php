<?php
/**
 * Tests loading configuration values through the Zenith\Application class
 * Author: Emmanuel Antico
 */
use Zenith\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
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
	
	public function testLoadEnvironmentConfig() {
		$app = Application::getInstance();
		$config = $app->load_config('app', 'production');
		
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('dispatcher', $config);
		$this->assertEquals('Bleach\SOAP\Dispatcher', $config['dispatcher']);
		$this->assertArrayHasKey('inject', $config);
		$this->assertTrue(is_array($config['inject']));
		$this->assertArrayHasKey('logger', $config['inject']);
		$this->assertEquals('Zenith\Log\ProductionLogger', $config['inject']['logger']);
		$app->clear_config('app');
	}
	
	public function testLoadApplicationConfig() {
		$app = Application::getInstance();
		$app->environment = 'development';
		$config = $app->load_config('app');
		
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('dispatcher', $config);
		$this->assertEquals('Bleach\SOAP\Dispatcher', $config['dispatcher']);
		$this->assertArrayHasKey('inject', $config);
		$this->assertTrue(is_array($config['inject']));
		$this->assertArrayHasKey('logger', $config['inject']);
		$this->assertEquals('Zenith\Log\DevelopmentLogger', $config['inject']['logger']);
		$app->clear_config('app');
	}
}