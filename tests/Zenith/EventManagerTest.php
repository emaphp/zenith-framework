<?php
use Zenith\Event\EventManager;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class EventManagerTest extends \PHPUnit_Framework_TestCase {
	protected $log_file;
	
	public function setUp() {
		$this->log_file = __DIR__ . '/events.log';
	}
	
	public function tearDown() {
		if (file_exists($this->log_file)) {
			unlink($this->log_file);
		}
	}
	
	public function testEvent() {
		//create logger
		$logger = new Logger('unit');
		$logger->pushHandler(new StreamHandler($this->log_file), Logger::DEBUG);
		
		//create event manager
		$eventManager = new EventManager();
		$eventManager->setEventLogger($logger);
		
		$x = 0;
		
		//add listener
		$eventManager->addListener('inc_x', function ($event, EventManager $manager) use (&$x) {
			$manager->logger->addDebug("Event $event has been triggered...");
			$manager->logger->addDebug("Value of x: $x");
			$manager->logger->addDebug("Incrementing x...");
			$x++;
			$this->assertTrue(file_exists($this->log_file));
		});
		
		//trigger event
		$eventManager->trigger('inc_x');
		$this->assertEquals(1, $x);
	}
}