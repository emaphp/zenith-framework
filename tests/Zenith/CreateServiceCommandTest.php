<?php
/**
 * Tests the CreateServiceCommand class
 * Author: Emmanuel Antico
 */
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenith\CLI\Command\CreateServiceCommand;

class CreateServiceCommandTest extends \PHPUnit_Framework_TestCase {
	protected $application;
	protected $test_class = 'TestService';
	protected $test_route = 'ServiceTest/TestClass';
	
	public function setUp() {
		//setup application
		$this->application = new Application();
		$command = new CreateServiceCommand();
		$command->__setup();
		$this->application->add($command);
	}
	
	public function tearDown() {
		if (file_exists(SERVICES_DIR . $this->test_route . '.php')) {
			unlink(SERVICES_DIR . $this->test_route . '.php');
			rmdir(SERVICES_DIR . dirname($this->test_route));
		}
		
		if (file_exists(SERVICES_DIR . $this->test_class . '.php')) {
			unlink(SERVICES_DIR . $this->test_class . '.php');
		}
	}
	
	public function testEmptyParams() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => ''));
		$this->assertRegExp('/You must define a class name/', $commandTester->getDisplay());
	}
	
	public function testRegexValidation() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => '123'));
		$this->assertRegExp('/\'123\' is not a valid namespace\/class name/', $commandTester->getDisplay());
	}
	
	public function testRegexValidation2() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => 'Ns/123'));
		$this->assertRegExp('/\'123\' is not a valid namespace\/class name/', $commandTester->getDisplay());
	}
	
	public function testRegexValidation3() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => '123/Class'));
		$this->assertRegExp('/\'123\' is not a valid namespace\/class name/', $commandTester->getDisplay());
	}
	
	public function testRegexValidation4() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => 'Ns/Class', 'methods' => array('123')));
		$this->assertRegExp('/\'123\' is not a valid method name/', $commandTester->getDisplay());
	}
	
	public function testRegexValidation5() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => 'Ns/Class', 'methods' => array('_hello', '321')));
		$this->assertRegExp('/\'321\' is not a valid method name/', $commandTester->getDisplay());
	}
	
	public function testExecute() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => $this->test_class));
		$this->assertRegExp('/New service created/', $commandTester->getDisplay());
		$this->assertTrue(file_exists(SERVICES_DIR . $this->test_class . '.php'));
	}
	
	public function testExecute2() {
		$command = $this->application->find('create-service');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName(), 'class' => $this->test_route));
		$this->assertRegExp('/New service created/', $commandTester->getDisplay());
		$this->assertTrue(file_exists(SERVICES_DIR . $this->test_route . '.php'));
	}
}