<?php
/**
 * Service property injection test
 * @group service
 */
class ServiceTest extends PHPUnit_Framework_TestCase {
	protected $service;
	
	public function setUp() {
		$this->service = Injector::create('DummyService');
	}
	
	public function testProperties() {
		$this->assertObjectHasAttribute('view', $this->service);
		$this->assertObjectHasAttribute('logger', $this->service);
		
		$reflectionClass = new \ReflectionClass('DummyService');
		
		$reflectionProperty = $reflectionClass->getProperty('view');
		$reflectionProperty->setAccessible(true);
		$view = $reflectionProperty->getValue($this->service);
		$this->assertInstanceOf('Zenith\View\View', $view);
		
		$reflectionProperty = $reflectionClass->getProperty('logger');
		$reflectionProperty->setAccessible(true);
		$logger = $reflectionProperty->getValue($this->service);
		$this->assertInstanceOf('Monolog\Logger', $logger);
	}
}