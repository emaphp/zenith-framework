<?php
/**
 * Tests the GenerateWSDLCommand class
 * @group command
 * Author: Emmanuel Antico;
 */
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenith\CLI\Command\GenerateWSDLCommand;
use Injector\Injector;

class GenerateWSDLCommandTest extends \PHPUnit_Framework_TestCase {
	protected $application;
	protected $environment = null;
	
	public function setUp() {
		//store current application environment
		if (is_null($this->environment)) {
			$app = \Zenith\Application::getInstance();
			$app->clear_config();
			$this->environment = $app->getEnvironment();
		}
		
		//generate cli application
		$this->application = new Application();
		$cmd = Injector::create('Zenith\CLI\Command\GenerateWSDLCommand');
		$this->application->add($cmd);
	}
	
	public function tearDown() {
		$app = \Zenith\Application::getInstance();
		$app->clear_config();
		$container = new \Pimple\Container;
		$container['environment'] = 'development';
		Injector::inject($app, $container);
		$config = $app->load_config('server');
		
		if (is_array($config) && array_key_exists('wsdl', $config) && file_exists($app->path('wsdl', $config['wsdl']))) {
			unlink($app->path('wsdl', $config['wsdl']));
		}
		
		//restore application environment
		$container = new \Pimple\Container;
		$container['environment'] = $this->environment;
		Injector::inject($app, $container);
	}
	
	public function testNoPath() {
		//start application with custom environment
		$app = \Zenith\Application::getInstance();
		$app->clear_config();
		$container = new \Pimple\Container;
		$container['environment'] = 'cli_test';
		Injector::inject($app, $container);
		
		$command = $this->application->find('wsdl-create');
		$commandTester = new CommandTester($command);
		$success = $commandTester->execute(['command' => $command->getName()]);

		$this->assertRegExp('/No target path specified in \'server\' configuration file. Generated WSDL will not be stored/', $commandTester->getDisplay());
		$this->assertEquals(0, $success);
	}
	
	public function testRender() {
		//start application
		$app = \Zenith\Application::getInstance();
		$app->clear_config();
		$container = new \Pimple\Container;
		$container['environment'] = 'development';
		Injector::inject($app, $container);
		
		$command = $this->application->find('wsdl-create');
		$commandTester = new CommandTester($command);
		$success = $commandTester->execute(['command' => $command->getName(), '--force']);
		
		$config = $app->load_config('server');
		$this->assertTrue(is_array($config));
		$this->assertArrayHasKey('wsdl', $config);
		$this->assertEquals('application.wsdl', $config['wsdl']);
		
		if (is_array($config) && array_key_exists('wsdl', $config) && file_exists($app->path('wsdl', $config['wsdl']))) {
			$this->assertTrue(file_exists($app->path('wsdl', $config['wsdl'])));
		}
		
		$this->assertRegExp('/WDSL generated successfully!!!/', $commandTester->getDisplay());
		$this->assertEquals(0, $success);
	}
}