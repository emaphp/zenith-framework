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
 * @Provider Zenith\IoC\Provider\FilesystemServiceProvider
 * @Provider Zenith\IoC\Provider\ViewServiceProvider
 * @Provider Zenith\IoC\Provider\LoggerServiceProvider
 */
abstract class BleachCommand extends Command {
	/**
	 * @Inject fs
	 */
	protected $fs;
	
	/**
	 * @Inject view
	 */
	protected $view;
	
	/**
	 * @Inject logger
	 */
	protected $logger;
}