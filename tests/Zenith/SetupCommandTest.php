<?php
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenith\CLI\Command\SetupCommand;

class SetupCommandTest extends \PHPUnit_Framework_TestCase {
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
		$cmd = new SetupCommand();
		$cmd->__setup();
		$this->application->add($cmd);
	}
	
	public function tearDown() {
		//restore application environment
		$app = \Zenith\Application::getInstance();
		$app->environment = $this->environment;
	}
	
	public function testExecute() {
		$command = $this->application->find('setup');
		$commandTester = new CommandTester($command);
		$success = $commandTester->execute(array('command' => $command->getName()));
		$this->assertEquals(0, $success);
	}
}