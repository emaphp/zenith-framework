<?php
/**
 * Tests loading configuration values through the Zenith\Application class
 * Author: Emmanuel Antico
 */
use Zenith\Application;

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
		$this->assertArrayHasKey('logger', $config);
		$this->assertEquals('Zenith\Log\ProductionLogger', $config['logger']);
		$app->clear_config('app');
	}
	
	/**
	 * Loads a configuration file with a previously defined environment
	 */
	public function testLoadApplicationConfig() {
		$app = Application::getInstance();
		$app->environment = 'development';
		$config = $app->load_config('app');
		
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('dispatcher', $config);
		$this->assertEquals('Zenith\SOAP\Dispatcher', $config['dispatcher']);
		$this->assertArrayHasKey('logger', $config);
		$this->assertEquals('Zenith\Log\DevelopmentLogger', $config['logger']);
		$app->clear_config('app');
	}
}