<?php
/**
 * Tests the WSDL and service generation
 * @group command
 * Author: Emmanuel Antico
 */
use Zenith\CLI\Command\CreateServiceCommand;
use Zenith\CLI\Command\GenerateWSDLCommand;
use Zenith\Application;
use Injector\Injector;
use Zenith\IoC\Provider\ViewServiceProvider;
use Zenith\IoC\Provider\FilesystemServiceProvider;

class CLITest extends \PHPUnit_Framework_TestCase {
	//directory to create in services folder
	protected $test_dir = 'route/to/class';
	//file to create in directory
	protected $test_file = 'Sales.php';
	//WSDL path
	protected $wsdl_path;
	
	public function setUp() {
		$this->validation_regex = CreateServiceCommand::$validation_regex;
		
		//build routes to services drectory
		$this->class_dir = Application::getInstance()->path('services', $this->test_dir);
		$this->class_file = Application::getInstance()->build_path($this->class_dir, 'Sales.php');
		
		//build wsdl path
		$server_config = Application::getInstance()->load_config('server');
		$this->wsdl_path = Application::getInstance()->path('wsdl', $server_config['wsdl']);
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
			
			for ($i = 0, $n = count($explode); $i < $n; $i++) {
				$class_dir = Application::getInstance()->path('services', implode(DIRECTORY_SEPARATOR, array_slice($explode, 0, $n - $i)));
				rmdir($class_dir);
			}
		}
	}
	
	public function testGenerateWSDL() {
		$container = new Pimple\Container;
		$provider = new ViewServiceProvider();
		$provider->register($container);
		
		$wsdl_config = Application::getInstance()->load_config('wsdl');
		$template = $wsdl_config['template'];
		$params = $wsdl_config['args'];
		$wsdl = $container['view']->render($template, $params);
		$content = file_get_contents(__DIR__ . '/assert/application-wsdl');
		$this->assertEquals($content, $wsdl);
		
		//write file
		$success = file_put_contents($this->wsdl_path, $wsdl);
		$this->assertTrue(is_int($success));
	}
	
	public function testMakeService1() {
		$container = new Pimple\Container;
		$provider = new ViewServiceProvider();
		$provider->register($container);
		
		$service = $container['view']->render('service', ['namespace' => null, 'classname' => 'TestService', 'methods' => null]);
		$content = file_get_contents(__DIR__ . '/assert/TestService');
		$this->assertEquals($content, $service);
	}
	
	public function testMakeService2() {
		$container = new Pimple\Container;
		$provider = new ViewServiceProvider();
		$provider->register($container);
		$service = $container['view']->render('service', ['namespace' => 'Greetings', 'classname' => 'HelloWorld', 'methods' => ['helloWorld']]);
		$content = file_get_contents(__DIR__ . '/assert/HelloWorld');
		$this->assertEquals($content, $service);
	}
	
	public function testMakeService3() {
		$container = new Pimple\Container;
		$provider = new ViewServiceProvider();
		$provider->register($container);
		$service = $container['view']->render('service', ['namespace' => 'Acme\Company', 'classname' => 'Sales', 'methods' => ['getTotal', 'getItem']]);
		$content = file_get_contents(__DIR__ . '/assert/Sales');
		$this->assertEquals($content, $service);
	}
	
	public function testWriteService() {
		$container = new Pimple\Container;
		$viewProvider = new ViewServiceProvider();
		$fsProvider = new FilesystemServiceProvider();
		$viewProvider->register($container);
		$fsProvider->register($container);
		
		//create directory
		$container['fs']->mkdir($this->class_dir);
		
		//write service
		$service = $container['view']->render('service', ['namespace' => 'Acme\Company', 'classname' => 'Sales', 'methods' => ['getTotal', 'getItem']]);
		$success = file_put_contents($this->class_file, $service);
		$this->assertTrue(is_int($success));
	}
	
	/**
	 * String validation tests
	 */
	
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