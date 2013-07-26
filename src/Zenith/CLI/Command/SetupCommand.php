<?php
namespace Zenith\CLI\Command;

use Zenith\CLI\Command\BleachCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class SetupCommand extends BleachCommand {
	protected function configure() {
		$this->setName('setup')
		->setDescription("Setup application directories");
	}
	
	public function execute(InputInterface $input, OutputInterface $output) {
		//storage directory creation
		$output->writeln("Creating the storage directory...");
		
		if ($this->fs->exists(STORAGE_DIR)) {
			$output->writeln("<info>The storage directory already exists!</info>");
		}
		else {
			try {
				$this->fs->mkdir(STORAGE_DIR);
				$output->writeln('<info>Directory \'' . STORAGE_DIR . '\' has been created!</info>');
			}
			catch (IOException $e) {
				$output->writeln('<error>An error occurred while creating the directory \'' . STORAGE_DIR . '\': ' . $e->getMessage() . '</error>');
				return 1;
			}
		}
	
		//logs directory
		$output->writeln("Creating the logs directory...");
		
		if ($this->fs->exists(LOGS_DIR)) {
			$output->writeln("<info>The logs directory already exists!</info>");
		}
		else {
			try {
				$this->fs->mkdir(LOGS_DIR);
				$output->writeln('<info>Directory \'' . LOGS_DIR . '\' has been created!</info>');
			}
			catch (IOException $e) {
				$output->writeln('<error>An error occurred while creating the directory \'' . LOGS_DIR . '\': ' . $e->getMessage() . '</error>');
				return 2;
			}
		}
		
		try {
			//change owner
			$this->fs->chown(LOGS_DIR, 'www-data');
		}
		catch (IOException $e) {
			$output->writeln('<error>An error occurred while changing the owner of the directory \'' . LOGS_DIR . '\': ' . $e->getMessage() . '</error>');
			return 3;
		}

		
		//twig directory
		$output->writeln("Creating the templates cache directory...");
		
		if ($this->fs->exists(TWIG_DIR)) {
			$output->writeln("<info>The templates directory already exists!</info>");
		}
		else {
			try {
				$this->fs->mkdir(TWIG_DIR);
				$output->writeln('<info>Directory \'' . TWIG_DIR . '\' has been created!</info>');
			}
			catch (IOException $e) {
				$output->writeln('<error>An error occurred while creating the directory \'' . TWIG_DIR . '\': ' . $e->getMessage() . '</error>');
				return 4;
			}
		}
		
		try {
			//change owner
			$this->fs->chown(TWIG_DIR, 'www-data');
		}
		catch (IOException $e) {
			$output->writeln('<error>An error occurred while changing the owner of the directory \'' . TWIG_DIR . '\': ' . $e->getMessage() . '</error>');
			return 5;
		}
		
		//wsdl directory
		$output->writeln("Creating the wsdl directory...");
		
		if ($this->fs->exists(WSDL_DIR)) {
			$output->writeln("<info>The wsdl directory already exists!</info>");
		}
		else {
			try {
				$this->fs->mkdir(WSDL_DIR);
				$output->writeln('<info>Directory \'' . WSDL_DIR . '\' has been created!</info>');
			}
			catch (IOException $e) {
				$output->writeln('<error>An error occurred while creating the directory \'' . WSDL_DIR . '\': ' . $e->getMessage() . '</error>');
				return 6;
			}
		}
		
		try {
			//change owner
			$this->fs->chown(WSDL_DIR, 'www-data');
		}
		catch (IOException $e) {
			$output->writeln('<error>An error occurred while changing the owner of the directory \'' . WSDL_DIR . '\': ' . $e->getMessage() . '</error>');
			return 7;
		}
		
		return 0;
	}
}