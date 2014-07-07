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
use Monolog\Logger;

class LogTest extends \PHPUnit_Framework_TestCase {
	protected $development_logger;
	protected $development_log;
	protected $production_logger;
	protected $production_log;
	
	public function setUp() {
		$app = Application::getInstance();
		
		//set 'development' environment
		{
			$envContainer = new Pimple\Container();
			$envContainer['environment'] = 'development';
			$app->clear_config();
			Injector::inject($app, $envContainer);
			
			$provider = new LoggerServiceProvider();
			$loggerContainer = new Pimple\Container();
			$provider->register($loggerContainer);
			$this->development_logger = $loggerContainer['logger'];
		}
		
		//set 'production' environemnt
		{
			$envContainer = new Pimple\Container();
			$envContainer['environment'] = 'production';
			$app->clear_config();
			Injector::inject($app, $envContainer);
			
			$provider = new LoggerServiceProvider();
			$loggerContainer = new Pimple\Container();
			$provider->register($loggerContainer);
			$this->production_logger = $loggerContainer['logger'];
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
		$handlers = $this->development_logger->getHandlers();
		$this->assertCount(1, $handlers);
		$handler = current($handlers);
		$this->assertInstanceOf('Monolog\Handler\StreamHandler', $handler);
		$level = $handler->getLevel();
		$this->assertEquals(Logger::DEBUG, $level);
		
		$this->development_logger->addDebug('Debug message');
		$this->development_logger->addWarning('Something went wrong');
		
		$this->assertTrue(file_exists($this->development_log));
		$file = file($this->development_log);
		$this->assertEquals(2, count($file));
	}
	
	public function testProductionLogger() {
		$handlers = $this->production_logger->getHandlers();
		$this->assertCount(1, $handlers);
		$handler = current($handlers);
		$this->assertInstanceOf('Monolog\Handler\StreamHandler', $handler);
		$level = $handler->getLevel();
		$this->assertEquals(Logger::WARNING, $level);
		
		$this->production_logger->addDebug('Debug message');
		$this->production_logger->addWarning('Something went wrong');
	
		$this->assertTrue(file_exists($this->production_log));
		$file = file($this->production_log);
		$this->assertEquals(1, count($file));
	}
}