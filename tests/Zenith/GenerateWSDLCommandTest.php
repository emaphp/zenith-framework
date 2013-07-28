<?php
/**
 * Tests the GenerateWSDLCommand class
 * Author: Emmanuel Antico;
 */
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenith\CLI\Command\GenerateWSDLCommand;

class GenerateWSDLCommandTest extends \PHPUnit_Framework_TestCase {
	protected $application;
	protected $environment = null;
	
	public function setUp() {
		//store current application environment
		if (is_null($this->environment)) {
			$app = \Zenith\Application::getInstance();
			$app->clear_config();
			$this->environment = $app->environment;
		}
		
		//generate cli application
		$this->application = new Application();
		$cmd = new GenerateWSDLCommand();
		$cmd->__setup();
		$this->application->add($cmd);
	}
	
	public function tearDown() {
		$app = \Zenith\Application::getInstance();
		$app->clear_config();
		$app->environment = 'development';
		$config = $app->load_config('server');
		
		if (is_array($config) && array_key_exists('wsdl', $config) && file_exists($app->path('wsdl', $config['wsdl']))) {
			unlink($app->path('wsdl', $config['wsdl']));
		}
		
		//restore application environment
		$app->environment = $this->environment;
	}
	
	public function testNoPath() {
		//start application with custom environment
		$app = \Zenith\Application::getInstance();
		$app->clear_config();
		$app->environment = 'cli_test';
		
		$command = $this->application->find('generate-wsdl');
		$commandTester = new CommandTester($command);
		$success = $commandTester->execute(array('command' => $command->getName()));

		$this->assertRegExp('/No target path specified in \'server\' configuration file. Generated WSDL will not be stored/', $commandTester->getDisplay());
		$this->assertEquals(0, $success);
	}
	
	public function testRender() {
		//start application
		$app = \Zenith\Application::getInstance();
		$app->clear_config();
		$app->environment = 'development';
		
		$command = $this->application->find('generate-wsdl');
		$commandTester = new CommandTester($command);
		$success = $commandTester->execute(array('command' => $command->getName(), '--force'));
		
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