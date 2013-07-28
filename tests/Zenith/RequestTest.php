<?php
/**
 * Tests request processing through the Zenith\SOAP\Request class
 * Author: Emmanuel Antico
 */
use Monolog\Handler\StreamHandler;
use Zenith\SOAP\Request;

class RequestTest extends PHPUnit_Framework_TestCase {
	protected $service;
	protected $configuration;
	protected $parameter;
	
	public function setUp() {
		$this->service = new stdClass();
		$this->service->class = 'DummyService';
		$this->service->method = 'run';

		$this->configuration = new stdClass();
		
		$this->parameter = new stdClass();
		$this->parameter->any = '<test><testname>RequestTest</testname><package>Zenith\SOAP</package></test>';
	}
	
	public function testConstructor() {
		$request = new Request($this->service, $this->configuration, $this->parameter);
		
		$service = $request->getService();
		$this->assertTrue(is_object($service));
		$this->assertEquals('stdClass', get_class($service));
		$this->assertObjectHasAttribute('class', $service);
		$this->assertEquals('DummyService', $service->class);
		$this->assertObjectHasAttribute('method', $service);
		$this->assertEquals('run', $service->method);
		
		$configuration = $request->getConfiguration();
		$this->assertTrue(is_array($configuration));
		$this->assertTrue(empty($configuration));
		
		$parameter = $request->getParameter();
		$this->assertTrue(is_object($parameter));
		$this->assertEquals('stdClass', get_class($parameter));
		$this->assertObjectHasAttribute('any', $parameter);
		$this->assertTrue(is_string($parameter->any));
		$this->assertEquals('<test><testname>RequestTest</testname><package>Zenith\SOAP</package></test>', $parameter->any);
		
		$parameter = $request->getParameter(Request::AS_XML);
		$this->assertTrue(is_string($parameter));
		$this->assertEquals('<test><testname>RequestTest</testname><package>Zenith\SOAP</package></test>', $parameter);
		
		$parameter = $request->getParameter(Request::AS_SIMPLEXML);
		$this->assertEquals('SimpleXMLElement', get_class($parameter));
		$this->assertObjectHasAttribute('testname', $parameter);
		$this->assertEquals('RequestTest', (string) $parameter->testname);
		$this->assertObjectHasAttribute('package', $parameter);
		$this->assertEquals('Zenith\SOAP', (string) $parameter->package);
		
		$parameter = $request->getParameter(Request::AS_DOM);
		$this->assertEquals('DOMDocument', get_class($parameter));
		$test = $parameter->getElementsByTagName('testname')->item(0);
		$this->assertEquals('RequestTest', $test->nodeValue);
		$test = $parameter->getElementsByTagName('package')->item(0);
		$this->assertEquals('Zenith\SOAP', $test->nodeValue);
	}
	
	public function testOneOption() {
		$test_configuration = new stdClass();
		$test_configuration->option = new stdClass();
		$test_configuration->option->name = 'option_name';
		$test_configuration->option->value = 'option_value';
		
		$request = new Request($this->service, $test_configuration, $this->parameter);
		$option = $request->option('non_existant');
		$this->assertNull($option);
		$option = $request->option('option_name');
		$this->assertEquals('option_value', $option);
	}
	
	public function testVariousOptions() {
		$option_a = new stdClass();
		$option_a->name = 'option_a';
		$option_a->value = 'value_a';
		
		$option_b = new stdClass();
		$option_b->name = 'option_b';
		$option_b->value = 'value_b';
		
		$test_configuration = new stdClass();
		$test_configuration->option = array($option_a, $option_b);
		
		$request = new Request($this->service, $test_configuration, $this->parameter);
		$option = $request->option('non_existant');
		$this->assertNull($option);
		$option = $request->option('option_a');
		$this->assertEquals('value_a', $option);
		$option = $request->option('option_b');
		$this->assertEquals('value_b', $option);
	}
	
	public function testOverrideOption() {
		$option_a = new stdClass();
		$option_a->name = 'option_a';
		$option_a->value = 'value_a';
		
		$option_b = new stdClass();
		$option_b->name = 'option_a';
		$option_b->value = 'value_b';
		
		$test_configuration = new stdClass();
		$test_configuration->option = array($option_a, $option_b);
		
		$request = new Request($this->service, $test_configuration, $this->parameter);
		$option = $request->option('non_existant');
		$this->assertNull($option);
		$option = $request->option('option_a');
		$this->assertEquals('value_b', $option);
	}
	
	public function testOptionSet() {
		$request = new Request($this->service, $this->configuration, $this->parameter);
		$request->option('test', 'value');
		$option = $request->option('test');
		$this->assertEquals('value', $option);
	}
}