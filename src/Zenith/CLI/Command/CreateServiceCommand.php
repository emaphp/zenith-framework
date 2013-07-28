<?php
namespace Zenith\CLI\Command;

use Zenith\CLI\Command\BleachCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zenith\Application;
use Symfony\Component\Filesystem\Exception\IOException;

class CreateServiceCommand extends BleachCommand {
	public static $validation_regex = '/^[A-z|_]{1}[\w]*$/';
	
	protected function configure() {
		$this->setName('create-service')
		->setDescription("Generates a new service")
		->addArgument('class', InputArgument::REQUIRED)
		->addArgument('methods', InputArgument::IS_ARRAY)
		->addOption('force', null, InputOption::VALUE_NONE, "When set, the generated service will override the older file");
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$class = $input->getArgument('class');
		
		if (empty($class)) {
			$output->writeln("<error>You must define a class name!</error>");
			return 1;
		}
		
		//remove slashes
		if (preg_match('@^/@', $class)) {
			$class = substr($class, 1);
		}
		
		if (preg_match('@/$@', $class)) {
			$class = substr($class, 0, strlen($class) - 1);
		}
		
		//parse route
		$route = explode(DIRECTORY_SEPARATOR, $class);
		
		//validate namespace/class name
		foreach ($route as $route_part) {
			if (!preg_match(self::$validation_regex, $route_part)) {
				$output->writeln("<error>'$route_part' is not a valid namespace/class name!</error>");
				return 2;
			}
		}
		
		$methods = $input->getArgument('methods');
		
		//validate methods
		foreach ($methods as $method) {
			if (!preg_match(self::$validation_regex, $method)) {
				$output->writeln("<error>'$method' is not a valid method name!</error>");
				return 3;
			}
		}
		
		$classname = array_pop($route);
		
		//check if namespace has been defined
		if (!empty($route)) {
			//generate namespace
			$namespace = implode('\\', $route);
			$path = Application::getInstance()->path('services', implode(DIRECTORY_SEPARATOR, $route));
			
			if (!$this->fs->exists($path)) {
				try {
					$this->fs->mkdir($path);
				}
				catch (IOException $e) {
					$output->writeln("<error>Failed to create directory '$path'!</error>");
					return 4;
				}
			}
		}
		else {
			$namespace = null;
			$path = Application::getInstance()->path('services');
		}
		
		//build script
		$script = $this->view->render('command/service', array('namespace' => $namespace, 'classname' => $classname, 'methods' => $methods));
		$filename = Application::getInstance()->build_path($path, "$classname.php");
		
		//check if file already exists
		if ($this->fs->exists($filename)) {
			if (!$input->getOption('force')) {
				$output->writeln("<info>File '$filename' already exists</info>");
				$output->writeln("<comment>Use --force to overwrite it</comment>");
				return 5;
			}
		}
		
		//write file
		try {
			$this->fs->dumpFile($filename, $script);
		}
		catch (IOException $e) {
			$output->writeln("<error>Failed to create file '$filename'!</error>");
			return 6;
		}
				
		$output->writeln("<info>New service created in '$filename'!!!</info>");
		return 0;
	}
}