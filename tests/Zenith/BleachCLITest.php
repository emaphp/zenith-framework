<?php
/**
 * Test CLI command construction through BleachCLI class
 * @group command
 * Author: Emmanuel Antico
 */
use Zenith\CLI\BleachCLI;
use Zenith\CLI\Command\BleachCommand;
use Injector\Injector;

class BleachCLITest extends \PHPUnit_Framework_TestCase {
	/**
	 * Tests commands initialization
	 */
	public function testCommandBuilder() {
		$cli = new BleachCLI();
		$this->assertObjectHasAttribute('cli_commands', $cli);
		$this->assertTrue(is_array($cli->cli_commands));
		$commands = $cli->cli_commands;
		
		foreach ($cli->cli_commands as $class) {
			$cmd = Injector::create($class);
			$this->assertTrue($cmd instanceof BleachCommand);
			$this->assertObjectHasAttribute('view', $cmd);
			$this->assertObjectHasAttribute('fs', $cmd);
			$this->assertObjectHasAttribute('logger', $cmd);
			
			$reflectionClass = new \ReflectionClass($class);
			//check injected properties
			$viewProperty = $reflectionClass->getProperty('view');
			$viewProperty->setAccessible(true);
			$view = $viewProperty->getValue($cmd);
			$this->assertInstanceOf('Zenith\View\View', $view);

			$fsProperty = $reflectionClass->getProperty('fs');
			$fsProperty->setAccessible(true);
			$fs = $fsProperty->getValue($cmd);
			$this->assertInstanceOf('Symfony\Component\Filesystem\Filesystem', $fs);
			
			$loggerProperty = $reflectionClass->getProperty('logger');
			$loggerProperty->setAccessible(true);
			$logger = $loggerProperty->getValue($cmd);
			$this->assertInstanceOf('Monolog\Logger', $logger);
		}
		
	}
}