<?php
namespace Zenith\CLI;

use Symfony\Component\Console\Application;
use Injector\Injector;

class BleachCLI extends Application {
	//default commands
	public $cli_commands = [
		'Zenith\CLI\Command\GenerateWSDLCommand',
		'Zenith\CLI\Command\CreateServiceCommand'
	];
	
	public function __construct() {
		parent::__construct('Bleach Command Line Interface', 'v2.0');
	}
	
	/**
	 * Builds all CLI commands
	 * @return array
	 */
	public function build_cli_commands() {
		foreach ($this->cli_commands as $command) {
			$cmd = Injector::create($command);
			$this->add($cmd);
		}
	}
}