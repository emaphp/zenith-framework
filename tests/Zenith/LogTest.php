<?php
/**
 * Tests both development and production loggers
 * Author: Emmanuel Antico
 */
use Zenith\Log\DevelopmentLogger;
use Zenith\Log\ProductionLogger;
use Zenith\Application;

class LogTest extends \PHPUnit_Framework_TestCase {
	protected $development_log;
	protected $production_log;
	
	public function setUp() {
		$this->development_log = Application::getInstance()->path('logs', 'development_' . date('Y-m-d') . '.log'); 
		$this->production_log = Application::getInstance()->path('logs', 'production_' . date('Y-m-d') . '.log');
	}
	
	public function tearDown() {
		if (file_exists($this->development_log)) {
			unlink($this->development_log);
		}
		
		if (file_exists($this->production_log)) {
			unlink($this->production_log);
		}
	}
	
	public function testDevelopmentLogger() {
		$logger = new DevelopmentLogger('unit');
		$logger->addDebug('Debug message');
		$logger->addWarning('Something went wrong');
		
		$this->assertTrue(file_exists($this->development_log));
		$file = file($this->development_log);
		$this->assertEquals(2, count($file));
	}
	
	public function testProductionLogger() {
		$logger = new ProductionLogger('unit');
		$logger->addDebug('Debug message');
		$logger->addWarning('Something went wrong');
	
		$this->assertTrue(file_exists($this->production_log));
		$file = file($this->production_log);
		$this->assertEquals(1, count($file));
	}
}