<?php
namespace Zenith\CLI;

use Symfony\Component\Console\Application;

class BleachCLI extends Application {
	//default commands
	public $cli_commands = array('Zenith\CLI\Command\GenerateWSDLCommand',
								 'Zenith\CLI\Command\ServiceCommand');
		
	/**
	 * Builds all CLI commands
	 * @return array
	 */
	public function build_cli_commands() {
		foreach ($this->cli_commands as $command) {
			$cmd = new $command;
			$container = $this->build_container($cmd->container);
			$container->injectAll($cmd);
			$this->add($cmd);
		}
	}
	
	/**
	 * Obtains a command container
	 * @param string $class
	 * @return Zenith\IoC\Container
	 */
	protected function build_container($class) {
		static $containers = array();
		
		if (!array_key_exists($class, $containers)) {
			$containers[$class] = new $class;
		}
		
		return $containers[$class];
	}
}