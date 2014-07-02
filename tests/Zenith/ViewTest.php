<?php
/**
 * Tests views generation through View class
 * @group view
 * Author: Emmanuel Antico
 */
use Zenith\View\View;

class ViewTest extends \PHPUnit_Framework_TestCase {
	public function testPHPEngine() {
		$view = new View([]);
		$output = $view->render('user', ['fullname' => 'John Doe', 'email' => 'john.doe@zenith.com', 'last_login' => '2013-07-18 21:34:23']);

		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/user');
		$this->assertEquals($content, $output);
	}
	
	public function testPHPEngineExtension() {
		$view = new View([]);
		$output = $view->render('user.php', ['fullname' => 'John Doe', 'email' => 'john.doe@zenith.com', 'last_login' => '2013-07-18 21:34:23']);
	
		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/user');
		$this->assertEquals($content, $output);
	}
	
	public function testTwigEngine() {
		$view = new View([]);
		$output = $view->render('request', ['classname' => 'Acme\HelloWorld', 'method' => 'sayHi']);
		
		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/request');
		$this->assertEquals($content, $output);
	}
	
	public function testTwigEngineExtension() {
		$view = new View([]);
		$output = $view->render('request.twig', ['classname' => 'Acme\HelloWorld', 'method' => 'sayHi']);
	
		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/request');
		$this->assertEquals($content, $output);
	}
}