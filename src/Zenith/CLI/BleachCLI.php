<?php
namespace Zenith\CLI;

use Symfony\Component\Console\Application;

class BleachCLI extends Application {
	//default commands
	public $cli_commands = array('Zenith\CLI\Command\GenerateWSDLCommand',
								 'Zenith\CLI\Command\CreateServiceCommand');
	
	public function __construct() {
		parent::__construct('Bleach Command Line Interface', 'v1.0');
	}
	
	/**
	 * Builds all CLI commands
	 * @return array
	 */
	public function build_cli_commands() {
		foreach ($this->cli_commands as $command) {
			$cmd = new $command;
			$cmd->__setup();
			$this->add($cmd);
		}
	}
}