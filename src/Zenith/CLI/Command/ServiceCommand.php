<?php
namespace Zenith\CLI\Command;

use Zenith\CLI\Command\BleachCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServiceCommand extends BleachCommand {
	protected $validation_regex = '/^[A-z|_]{1}[\w]*$/';
	
	protected function configure() {
		$this->setName('service')
		->setDescription("Generates a service")
		->addArgument('class', InputArgument::REQUIRED)
		->addArgument('methods', InputArgument::IS_ARRAY);
	}
	
	/**
	 * Creates the directory to store a newly created service
	 * @param string $path
	 * @param number $mode
	 * @return boolean
	 */
	protected function make_directory($path, $mode = 0777) {
		$dirs = explode(DIRECTORY_SEPARATOR , $path);
		$count = count($dirs);
		$path = '.';
		
		for ($i = 0; $i < $count; ++$i) {
			$path .= DIRECTORY_SEPARATOR . $dirs[$i];
			
			if (!is_dir($path) && !mkdir($path, $mode)) {
				return false;
			}
		}
		
		return true;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$class = $input->getArgument('class');
		
		//remove slashes
		if (preg_match('@^/@', $class)) {
			$class = substr($class, 1);
		}
		
		if (preg_match('@/$@', $class)) {
			$class = substr($class, 0, strlen($class) - 1);
		}
		
		$route = explode(DIRECTORY_SEPARATOR, $class);
		
		//validate namespace/class name
		foreach ($route as $route_part) {
			if (!preg_match($this->validation_regex, $route_part)) {
				$output->writeln("<error>'$route_part' is not a valid namespace/class name</error>");
				return;
			}
		}
		
		$methods = $input->getArgument('methods');
		
		//validate methods
		foreach ($methods as $method) {
			if (!preg_match($this->validation_regex, $method)) {
				$output->writeln("<error>'$method' is not a valid method name</error>");
				return;
			}
		}
		
		$classname = array_pop($route);
		
		//check if namespace has benn defined
		if (!empty($route)) {
			//generate namespace
			$namespace = implode('\\', $route);
			
			$path = SERVICES_DIR . implode(DIRECTORY_SEPARATOR, $route);
			
			if (!$this->make_directory($path)) {
				$output->writeln("<error>Failed to create directory $path</error>");
				return;
			}
		}
		else {
			$namespace = null;
			$path = SERVICES_DIR;
		}
		
		//build script
		$script = $this->view->render('command/service', array('namespace' => $namespace, 'classname' => $classname, 'methods' => $methods));
		$filename = $path . DIRECTORY_SEPARATOR . "$classname.php";
		
		if (!file_put_contents($filename, $script)) {
			$output->writeln("<error>Failed to write file $filename</error>");
			return;
		}
		
		$output->writeln("<info>New service created in $filename</info>");
	}
}