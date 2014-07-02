<?php
namespace Zenith\CLI\Command;

use Symfony\Component\Console\Command\Command;

/**
 * BleachCLI command base class
 * 
 * @author emaphp
 * 
 * Dependencies:
 * 
 * @inject.provider Zenith\IoC\Provider\FilesystemServiceProvider
 * @inject.provider Zenith\IoC\Provider\ViewServiceProvider
 * @inject.provider Zenith\IoC\Provider\LoggerServiceProvider
 */
abstract class BleachCommand extends Command {
	/**
	 * @inject.service fs
	 */
	private $fs;
	
	/**
	 * @inject.service view
	 */
	private $view;
	
	/**
	 * @inject.service logger
	 */
	private $logger;
}