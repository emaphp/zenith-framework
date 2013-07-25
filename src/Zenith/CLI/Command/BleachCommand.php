<?php
namespace Zenith\CLI\Command;

use Symfony\Component\Console\Command\Command;

abstract class BleachCommand extends Command {
	use \Injector\Injectable;
	public $container = 'Zenith\IoC\CommandContainer';
}