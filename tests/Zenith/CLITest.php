<?php
class CLITest extends \PHPUnit_Framework_TestCase {
	protected $validation_regex = '/^[A-z|_]{1}[\w]*$/';
	
	public function testValidationRegex() {
		$class = 'Acme';
		$result = preg_match($this->validation_regex, $class, $matches);
		
		$this->assertEquals(1, $result);
		$this->assertEquals('Acme', $matches[0]);
	}
	
	public function testValidationRegex2() {
		$class = '_Acme';
		$result = preg_match($this->validation_regex, $class, $matches);
	
		$this->assertEquals(1, $result);
		$this->assertEquals('_Acme', $matches[0]);
	}
	
	public function testValidationRegex3() {
		$class = '1Acme';
		$result = preg_match($this->validation_regex, $class, $matches);
	
		$this->assertEquals(0, $result);
	}
	
	public function testValidationRegex4() {
		$class = 'Acme/Service';
		$explode = explode('/', $class);
		$this->assertEquals(2, count($explode));
		
		$result = preg_match($this->validation_regex, $explode[0], $matches);
		$this->assertEquals(1, $result);
		$this->assertEquals('Acme', $matches[0]);
		
		$result = preg_match($this->validation_regex, $explode[1], $matches);
		$this->assertEquals(1, $result);
		$this->assertEquals('Service', $matches[0]);
	}
}