<?php
namespace Zenith\CLI;

use Symfony\Component\Console\Application;

class BleachCLI extends Application {
	public $cli_commands = array('Zenith\CLI\Command\GenerateWSDLCommand');
	
	public function __construct() {
		parent::__construct();
		
		//build commands
		$commands = $this->build_cli_commands();
		
		foreach ($commands as $command) {
			$this->add($command);
		}
	}
	
	protected function build_cli_commands() {
		$commands = array();
		
		foreach ($this->cli_commands as $command) {
			$cmd = new $command;
			$container = $this->build_container($cmd->container);
			$container->injectAll($cmd);
			$commands[] = $cmd;
		}
		
		return $commands;
	}
	
	protected function build_container($class) {
		static $containers = array();
		
		if (!array_key_exists($class, $containers)) {
			$containers[$class] = new $class;
		}
		
		return $containers[$class];
	}
}