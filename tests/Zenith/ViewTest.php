<?php
/**
 * Tests views generation through View class
 * Author: Emmanuel Antico
 */
use Zenith\View\View;

class ViewTest extends \PHPUnit_Framework_TestCase {
	public function testPHPEngine() {
		$view = new View(VIEWS_DIR, array());
		$output = $view->render('user', array('fullname' => 'John Doe', 'email' => 'john.doe@zenith.com', 'last_login' => '2013-07-18 21:34:23'));

		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/user');
		$this->assertEquals($content, $output);
	}
	
	public function testPHPEngineExtension() {
		$view = new View(VIEWS_DIR, array());
		$output = $view->render('user.php', array('fullname' => 'John Doe', 'email' => 'john.doe@zenith.com', 'last_login' => '2013-07-18 21:34:23'));
	
		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/user');
		$this->assertEquals($content, $output);
	}
	
	public function testTwigEngine() {
		$view = new View(VIEWS_DIR, array());
		$output = $view->render('request', array('classname' => 'Acme\HelloWorld', 'method' => 'sayHi'));
		
		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/request');
		$this->assertEquals($content, $output);
	}
	
	public function testTwigEngineExtension() {
		$view = new View(VIEWS_DIR, array());
		$output = $view->render('request.twig', array('classname' => 'Acme\HelloWorld', 'method' => 'sayHi'));
	
		//load content to assert
		$content = file_get_contents(__DIR__ . '/assert/request');
		$this->assertEquals($content, $output);
	}
}