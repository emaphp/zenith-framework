<?php
namespace Zenith\CLI\Command;

use Zenith\Application;
use Zenith\CLI\Command\BleachCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateWSDLCommand extends BleachCommand {
	protected function configure() {
		//add command properties
		$this->setName('generate-wsdl')
		->setDescription("Generates and stores the application WSDL")
		->addArgument('path', InputArgument::OPTIONAL)
		->addOption('output', null, InputOption::VALUE_NONE, 'If set, the generated WSDL will be printed to console');
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
				$output->writeln('<comment>No target path specified. Generated WSDL will not be stored.</comment>');
				$path = null;
			}
			else {
				$path = $config['wsdl'];
			}
		}
		
		//load wsdl configuration
		$wsdl_config = Application::getInstance()->load_config('wsdl');
		
		if (is_null($wsdl_config)) {
			$output->writeln('<error>WSDL configuration not found!</error>');
			return;
		}
		
		if (!array_key_exists('template', $wsdl_config) || !is_string($wsdl_config['template']) || empty($wsdl_config['template'])) {
			$output->writeln('<error>No WSDL template defined</error>');
			return;
		}
		
		$template = $wsdl_config['template'];
		
		if (!array_key_exists('template_params', $wsdl_config) || !is_array($wsdl_config['template_params'])) {
			$output->writeln('<error>No WSDL template parameters defined</error>');
			return;
		}
		
		$template_params = $wsdl_config['template_params'];
		
		$wsdl = $this->view->render($template, $template_params);
		$output->writeln("<info>WDSL generated successfully!!!</info>");
		
		if (isset($path)) {
			if (!file_put_contents($path, $wsdl)) {
				$output->writeln("<error>Failed to store file in $path. Check folder permissions.</error>");
			}
			else {
				$output->writeln("<info>Generated WSDL was stored in $path.</info>");
			}
		}
		
		if ($input->getOption('output')) {
			$output->writeln($wsdl);
		}
	}
} 