<?php
namespace Zenith\CLI\Command;

use Zenith\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class GenerateWSDLCommand extends BleachCommand {
	protected function configure() {
		//add command properties
		$this->setName('wsdl-create')
		->setDescription("Generates and stores the application WSDL")
		->addArgument('path', InputArgument::OPTIONAL)
		->addOption('output', null, InputOption::VALUE_NONE, 'If set, the generated WSDL will be printed to console')
		->addOption('force', null, InputOption::VALUE_NONE, "When set, the generated WSDL will override the older file");
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$path = $input->getArgument('path');
		
		if (!$path) {
			//load server configuration
			$config = Application::getInstance()->load_config('server');

			if (is_null($config)) {
				$output->writeln('<error>Server configuration not found!</error>');
				return;
			}
			
			//get path from server configuration file
			if (!array_key_exists('wsdl', $config) || !is_string($config['wsdl']) || empty($config['wsdl'])) {
				$output->writeln('<comment>No target path specified in \'server\' configuration file. Generated WSDL will not be stored.</comment>');
				$path = null;
			}
			else {
				$path = Application::getInstance()->path('wsdl', $config['wsdl']);
			}
		}
		
		//load wsdl configuration
		$wsdl_config = Application::getInstance()->load_config('wsdl');

		if (is_null($wsdl_config)) {
			$output->writeln('<error>WSDL configuration not found!</error>');
			return 1;
		}
		
		if (!array_key_exists('template', $wsdl_config) || !is_string($wsdl_config['template']) || empty($wsdl_config['template'])) {
			$output->writeln('<error>No WSDL template found in \'wsdl\' configuration file!</error>');
			return 2;
		}
		
		//obtain template
		$template = $wsdl_config['template'];
		
		if (!array_key_exists('args', $wsdl_config) || !is_array($wsdl_config['args'])) {
			$output->writeln('<error>No WSDL template parameters have been defined!</error>');
			return 3;
		}
		
		//obtain template parameters
		$template_params = $wsdl_config['args'];
		
		//render WSDL
		$wsdl = $this->view->render($template, $template_params);
		$output->writeln("<info>WDSL generated successfully!!!</info>");
		
		//check if path was specified
		if (isset($path)) {
			//build absolute path
			if (!$this->fs->isAbsolutePath($path)) {
				$path = Application::getInstance()->path('wsdl', $path);
			}
		
			//create directory
			$dir = dirname($path);
			
			if (!$this->fs->exists($dir)) {
				$output->writeln("<info>Creating directory '$path'...</info>");
				
				try {
					$this->fs->mkdir($path);
				}
				catch (IOException $e) {
					$output->writeln("<error>Failed to create directory '$dir'!: " . $e->getMessage() . '</error>');
					return 4;
				}
			}
			
			//check if file already exists
			if ($this->fs->exists($path)) {
				if (!$input->getOption('force')) {
					$output->writeln("<info>File '$path' already exists</info>");
					$output->writeln("<comment>Use --force to overwrite it</comment>");
					return 5;
				}
			}
			
			try {
				//write file
				$this->fs->dumpFile($path, $wsdl);
				$output->writeln("<info>Generated WSDL was stored in '$path'.</info>");
			}
			catch (IOException $e) {
				$output->writeln("<error>Failed to store WSDL file in '$path'!!!</error>");
				return 6;
			}
		}
		
		//output WSDL
		if ($input->getOption('output')) {
			$output->writeln($wsdl);
		}
		
		return 0;
	}
} 