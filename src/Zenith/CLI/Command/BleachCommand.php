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
	protected $fs;
	
	/**
	 * @inject.service view
	 */
	protected $view;
	
	/**
	 * @inject.service logger
	 */
	protected $logger;
}