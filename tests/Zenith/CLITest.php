<?php
/**
 * Tests the WSDL and service generation
 * Author: Emmanuel Antico
 */
use Zenith\CLI\Command\CreateServiceCommand;
use Zenith\CLI\Command\GenerateWSDLCommand;
use Zenith\Application;

class CLITest extends \PHPUnit_Framework_TestCase {
	//directory to create in services folder
	protected $test_dir = 'route/to/class';
	//file to create in directory
	protected $test_file = 'Sales.php';
	//WSDL path
	protected $wsdl_path;
	
	public function setUp() {
		$this->validation_regex = CreateServiceCommand::$validation_regex;
		
		//build relative routes to services drectory
		$this->class_dir = substr(str_replace(getcwd(), '', SERVICES_DIR), 1) . $this->test_dir;
		$this->class_file = $this->class_dir . DIRECTORY_SEPARATOR . 'Sales.php';
		
		//build wsdl path
		$server_config = Application::getInstance()->load_config('server');
		$this->wsdl_path = substr(str_replace(getcwd(), '', $server_config['wsdl']), 1);
	}
	
	public function tearDown() {
		if (file_exists($this->wsdl_path)) {
			unlink($this->wsdl_path);
		}
		
		if (file_exists($this->class_file)) {
			unlink($this->class_file);
		}
		
		if (is_dir($this->class_dir)) {
			$explode = explode(DIRECTORY_SEPARATOR, $this->test_dir);
			$dir = substr(str_replace(getcwd(), '', SERVICES_DIR), 1);
			
			for ($i = 0, $n = count($explode); $i < $n; $i++) {
				$class_dir = $dir . implode(DIRECTORY_SEPARATOR, array_slice($explode, 0, $n - $i));
				rmdir($class_dir);
			}
		}
	}
	
	public function testGenerateWSDL() {
		$command = new GenerateWSDLCommand();
		$container = $command->container;
		$c = new $container();
		$c->configure();
		
		$wsdl_config = Application::getInstance()->load_config('wsdl');
		$template = $wsdl_config['template'];
		$params = $wsdl_config['args'];
		$wsdl = $c['view']->render($template, $params);
		$content = file_get_contents(__DIR__ . '/assert/application-wsdl');
		$this->assertEquals($content, $wsdl);
		
		//write file
		$success = file_put_contents($this->wsdl_path, $wsdl);
		$this->assertTrue(is_int($success));
	}
	
	public function testMakeDir() {
		$command = new CreateServiceCommand();
		$success = $command->make_directory($this->class_dir);
		$this->assertTrue($success);
	}
	
	public function testMakeService1() {
		$command = new CreateServiceCommand();
		$container = $command->container;
		$c = new $container();
		$c->configure();
		$service = $c['view']->render('command/service', array('namespace' => null, 'classname' => 'TestService', 'methods' => null));
		$content = file_get_contents(__DIR__ . '/assert/TestService');
		$this->assertEquals($content, $service);
	}
	
	public function testMakeService2() {
		$command = new CreateServiceCommand();
		$container = $command->container;
		$c = new $container();
		$c->configure();
		$service = $c['view']->render('command/service', array('namespace' => 'Greetings', 'classname' => 'HelloWorld', 'methods' => array('helloWorld')));
		$content = file_get_contents(__DIR__ . '/assert/HelloWorld');
		$this->assertEquals($content, $service);
	}
	
	public function testMakeService3() {
		$command = new CreateServiceCommand();
		$container = $command->container;
		$c = new $container();
		$c->configure();
		$service = $c['view']->render('command/service', array('namespace' => 'Acme\Company', 'classname' => 'Sales', 'methods' => array('getTotal', 'getItem')));
		$content = file_get_contents(__DIR__ . '/assert/Sales');
		$this->assertEquals($content, $service);
	}
	
	public function testWriteService() {
		$command = new CreateServiceCommand();
		$container = $command->container;
		$c = new $container();
		$c->configure();
		
		//create directory
		$success = $command->make_directory($this->class_dir);
		$this->assertTrue($success);
		
		//write service
		$service = $c['view']->render('command/service', array('namespace' => 'Acme\Company', 'classname' => 'Sales', 'methods' => array('getTotal', 'getItem')));
		$success = file_put_contents($this->class_dir . DIRECTORY_SEPARATOR . 'Sales.php', $service);
		$this->assertTrue(is_int($success));
	}
	
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