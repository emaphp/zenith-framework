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
		$config = $app->load_config('server');
		
		if (is_array($config) && array_key_exists('wsdl', $config) && file_exists($config['wsdl'])) {
			unlink($config['wsdl']);
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
		$commandTester->execute(array('command' => $command->getName()));

		$this->assertRegExp('/No target path specified. Generated WSDL will not be stored/', $commandTester->getDisplay());
	}
	
	public function testRender() {
		//start application
		$app = \Zenith\Application::getInstance();
		$app->clear_config();
		$app->environment = 'development';
		
		$command = $this->application->find('generate-wsdl');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName()));
		
		$config = $app->load_config('server');
		
		if (is_array($config) && array_key_exists('wsdl', $config) && file_exists($config['wsdl'])) {
			$this->assertTrue(file_exists($config['wsdl']));
		}
		
		$this->assertRegExp('/WDSL generated successfully!!!/', $commandTester->getDisplay());
	}
}