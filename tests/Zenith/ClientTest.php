<?php
/**
 * Tests client implementation against a working Zenith application
 * Author: Emmanuel Antico
 * Note: This test requires a working Zenith application to work properly.
 * A default WSDL is supplied with a test service endpointb (see app/storage/clienttest.wsld).
 * Remeber to modify application URI before running this test.
 */
use Zenith\Client;
use Zenith\SOAP\Response;

class ClientTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Request tests
	 */
	
	public function testBuildRequest() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'hello');
	
		$success = $client->invoke();
		$this->assertTrue($success);
		
		$request = $client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/client/request1.xml');
		$this->assertEquals($assert, $request);
	}
	
	public function testBuildRequest2() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'hello');
		$client->setOption('test', 'value');
		
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$request = $client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/client/request2.xml');
		$this->assertEquals($assert, $request);
	}
	
	public function testBuildRequest3() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'hello');
		$client->setOption('test', 'value');
		$client->setOption('another', 'value2');
	
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$request = $client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/client/request3.xml');
		$this->assertEquals($assert, $request);
	}
	
	public function testBuildRequest4() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'hello');
		$client->setOption('test', 'value');
		$client->setOption('another', 'value2');
		$client->setParameter('<to>World</to>');
	
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$request = $client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/client/request4.xml');
		$this->assertEquals($assert, $request);
	}
	
	/**
	 * XML response tests
	 */
	
	public function testHello() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'hello');
		
		$success = $client->invoke();
		$this->assertTrue($success);
		
		$response = $client->getResponse();
		$code = $response->getStatusCode();
		$this->assertEquals(0, $code);
		$message = $response->getStatusMessage();
		$this->assertEquals("Ok", $message);
		
		$result = $response->getResult(Response::AS_RAW);
		$this->assertEquals('Hello World :)', $result);
		
		$result = $response->getResult(Response::AS_DOM);
		$this->assertEquals('Hello World :)', $result);
		
		$result = $response->getResult(Response::AS_XML);
		$this->assertEquals('Hello World :)', $result);
		
		$result = $response->getResult(Response::AS_SIMPLEXML);
		$this->assertEquals('Hello World :)', $result);
	}
	
	public function testGoodbye() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'sayGoodbye');
		
		$success = $client->invoke();
		$this->assertTrue($success);
		
		$response = $client->getResponse();
		$code = $response->getStatusCode();
		$this->assertEquals(0, $code);
		$message = $response->getStatusMessage();
		$this->assertEquals("Ok", $message);
		
		$result = $response->getResult(Response::AS_XML);
		$this->assertEquals("<message>Goodbye World!!!</message><destination>Earth</destination>", $result);
	}
	
	public function testGoodbye2() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'sayGoodbye');
		$client->setOption('lang', 'sp');
		
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$response = $client->getResponse();
		$code = $response->getStatusCode();
		$this->assertEquals(0, $code);
		$message = $response->getStatusMessage();
		$this->assertEquals("Ok", $message);
	
		$result = $response->getResult(Response::AS_XML);
		$this->assertEquals("<message>Adios Mundo!!!</message><destination>Tierra</destination>", $result);
	}
	
	/**
	 * Unwrapped XML response tests
	 */
	
	public function testExpose() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'expose');
		
		$success = $client->invoke();
		$this->assertTrue($success);
		
		$response = $client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("Ok", $statusMessage);
		$result = $response->getResult(Response::AS_XML);
		$this->assertEquals("<class>Acme\HelloWorld</class><methods><method>hello</method><method>sayHi</method><method>sayGoodbye</method><method>expose</method><method>parseRequest</method><method>throw_fault</method><method>throw_exception</method><method>throw_service_exception</method></methods>", $result);
	}
	
	/**
	 * @expectedException RuntimeException
	 */
	public function testExpose2() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'expose');
	
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$response = $client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("Ok", $statusMessage);
		libxml_use_internal_errors(true);
		//this will trigger an exception
		$result = $response->getResult(Response::AS_SIMPLEXML);
	}
	
	/**
	 * @expectedException RuntimeException
	 */
	public function testExpose3() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'expose');
	
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$response = $client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("Ok", $statusMessage);

		//this will trigger an exception
		$result = $response->getResult(Response::AS_DOM);
	}
	
	/**
	 * Wrapped XML response test
	 */
	
	public function testParameter() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'parseRequest');
		$client->setParameter('<user><id>536</id><name>Charles</name></user>');
		
		$success = $client->invoke();
		$this->assertTrue($success);
		
		$response = $client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("XML parsed correctly", $statusMessage);
		$result = $response->getResult(Response::AS_XML);
		$this->assertEquals("<data><userid>536</userid><username>Charles</username></data>", $result);
	}
	
	public function testParameter2() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'parseRequest');
		$client->setParameter('<user><id>15623</id><name>Peter</name></user>');
	
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$response = $client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("XML parsed correctly", $statusMessage);
		$result = $response->getResult(Response::AS_DOM);
		$id = $result->getElementsByTagName('userid')->item(0);
		$this->assertEquals('15623', $id->nodeValue);
		$name = $result->getElementsByTagName('username')->item(0);
		$this->assertEquals('Peter', $name->nodeValue);
	}
	
	public function testParameter3() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'parseRequest');
		$client->setParameter('<user><id>74426</id><name>Jeff</name></user>');
	
		$success = $client->invoke();
		$this->assertTrue($success);
	
		$response = $client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("XML parsed correctly", $statusMessage);
		$result = $response->getResult(Response::AS_SIMPLEXML);
		$id = (int) $result->userid;
		$this->assertEquals(74426, $id);
		$name = (string) $result->username;
	}
	
	/**
	 * Fault/Exception tests
	 */
	
	public function testFault() {
		$this->markTestSkipped(
				'...'
		);
		
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'throw_fault');
		
		$success = $client->invoke();
		$this->assertFalse($success);
		$faultCode = $client->getFaultCode();
		$this->assertEquals("Server", $faultCode);
		$faultString = $client->getFaultString();
		$this->assertEquals("Unexpected error", $faultString);
	}
	
	public function testException() {
		$this->markTestSkipped(
				'...'
		);
		
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'throw_exception');
		
		$success = $client->invoke();
		$this->assertFalse($success);
		$faultCode = $client->getFaultCode();
		$this->assertEquals("Server", $faultCode);
		$faultString = $client->getFaultString();
		$this->assertEquals("Something bad happened...", $faultString);
	}
	
	public function testServiceException() {
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'throw_service_exception');
		
		$success = $client->invoke();
		$this->assertTrue($success);
		
		$response = $client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(5, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("A customized error response", $statusMessage);
	}
}