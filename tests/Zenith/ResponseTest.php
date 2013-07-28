<?php
/**
 * Tests response construction through the Zenith\SOAP\Response class
 * Author: Emmanuel Antico
 */
use Zenith\SOAP\Response;

class ResponseTest extends PHPUnit_Framework_TestCase {
	public function testBuild() {
		$response = new Response();
		$response->setService('TestClass', 'TestMethod');
		$response->setStatus(1, 'Unexpected error');
		$response->setResult('An unexpected error ocurred');
		$resp = $response->build();
		
		$this->assertTrue(is_array($resp));
		$this->assertArrayHasKey('service', $resp);
		$this->assertArrayHasKey('status', $resp);
		$this->assertArrayHasKey('result', $resp);
		$this->assertTrue(is_array($resp['service']));
		$this->assertArrayHasKey('class', $resp['service']);
		$this->assertEquals('TestClass', $resp['service']['class']);
		$this->assertArrayHasKey('method', $resp['service']);
		$this->assertEquals('TestMethod', $resp['service']['method']);
		$this->assertTrue(is_array($resp['status']));
		$this->assertArrayHasKey('code', $resp['status']);
		$this->assertEquals(1, $resp['status']['code']);
		$this->assertArrayHasKey('message', $resp['status']);
		$this->assertEquals('Unexpected error', $resp['status']['message']);
		$this->assertTrue(is_array($resp['result']));
		$this->assertArrayHasKey('any', $resp['result']);
		$this->assertEquals('An unexpected error ocurred', $resp['result']['any']);
	}
}