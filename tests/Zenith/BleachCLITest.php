<?php
/**
 * Test CLI command construction through BleachCLI class
 * Author: Emmanuel Antico
 */
use Zenith\CLI\BleachCLI;
use Zenith\CLI\Command\BleachCommand;

class BleachCLITest extends \PHPUnit_Framework_TestCase {
	public function testCommandBuilder() {
		$cli = new BleachCLI();
		$this->assertObjectHasAttribute('cli_commands', $cli);
		$this->assertTrue(is_array($cli->cli_commands));
		$commands = $cli->cli_commands;
		
		foreach ($cli->cli_commands as $class) {
			$cmd = new $class;
			$this->assertTrue($cmd instanceof BleachCommand);
			$this->assertObjectHasAttribute('container', $cmd);
			$this->assertTrue(is_string($cmd->container));
			
			$container = new $cmd->container;
			$container->configure();
			$container->inject($cmd);
			$this->assertObjectHasAttribute('view', $cmd);
			$this->assertEquals('Zenith\View\View', get_class($cmd->view));
		}
	}
}