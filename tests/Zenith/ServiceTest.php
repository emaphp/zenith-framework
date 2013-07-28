<?php
class ServiceTest extends PHPUnit_Framework_TestCase {
	protected $service;
	
	public function setUp() {
		$this->service = new DummyService();
		$this->service->__setup();
	}
	
	public function testProperties() {
		$this->assertObjectHasAttribute('view', $this->service);
		$this->assertEquals('Zenith\View\View', get_class($this->service->view));
	}
}