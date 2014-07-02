<?php
/**
 * Tests both development and production loggers
 * @group logger
 * Author: Emmanuel Antico
 */
use Zenith\Log\DevelopmentLogger;
use Zenith\Log\ProductionLogger;
use Zenith\Application;
use Zenith\IoC\Provider\LoggerServiceProvider;
use Injector\Injector;

class LogTest extends \PHPUnit_Framework_TestCase {
	protected $development_logger;
	protected $development_log;
	protected $production_logger;
	protected $production_log;
	
	public function setUp() {
		$app = Application::getInstance();
		
		//set 'development' environment
		{
			$provider = new LoggerServiceProvider();
			
			$envContainer = new Pimple\Container();
			$envContainer['environment'] = 'development';
			Injector::inject($app, $container);
			
			$this->development_logger = $provider['logger'];
		}
		
		//set 'production' environemnt
		{
			$provider = new LoggerServiceProvider();
				
			$envContainer = new Pimple\Container();
			$envContainer['environment'] = 'production';
			Injector::inject($app, $container);
			
			$this->production_logger = $provider['logger'];
		}
		
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
		$this->development_logger->addDebug('Debug message');
		$this->development_logger->addWarning('Something went wrong');
		
		$this->assertTrue(file_exists($this->development_log));
		$file = file($this->development_log);
		$this->assertEquals(2, count($file));
	}
	
	public function testProductionLogger() {
		$this->production_logger->addDebug('Debug message');
		$this->production_logger->addWarning('Something went wrong');
	
		$this->assertTrue(file_exists($this->production_log));
		$file = file($this->production_log);
		$this->assertEquals(1, count($file));
	}
}