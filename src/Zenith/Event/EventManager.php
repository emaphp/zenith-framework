<?php
namespace Zenith\Event;

use Monolog\Logger;
use Zenith\Event\IEventHandler;

class EventManager implements IEventHandler {
	/**
	 * Logger methods for runtime errors
	 * @var array
	 */
	public $error_methods = array(E_ERROR => 'addError', E_WARNING => 'addWarning', E_NOTICE => 'addNotice',
								  E_CORE_ERROR => 'addError', E_CORE_WARNING => 'addWarning',
								  E_USER_ERROR => 'addError', E_USER_WARNING => 'addWarning', E_USER_NOTICE => 'addNotice', E_USER_DEPRECATED => 'addNotice',
								  E_STRICT => 'addNotice',
								  E_RECOVERABLE_ERROR => 'addError',
								  E_DEPRECATED => 'addNotice',
								  E_USER_DEPRECATED => 'addNotice');
	
	/**
	 * Logger method for exceptions
	 * @var string
	 */
	public $exception_method = 'addCritical';
	
	/**
	 * Events list
	 * @var array
	 */
	public $events = array();
	
	/**
	 * Error logger
	 * @var Monolog\Logger
	 */
	public $logger;
	
	/**
	 * Sets event logger
	 * (non-PHPdoc)
	 * @see \Zenith\Event\IEventHandler::setEventLogger()
	 */
	public function setEventLogger(Logger $logger) {
		$this->logger = $logger;
	}
	
	/**
	 * Error handler
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @param array $errcontext
	 */
	public function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
		$method = array_key_exists($errno, $this->error_methods) ? $this->error_methods[$errno] : 'addError';
		$errstr = sprintf("%s on file %s (line %d)", $errstr, $errfile, $errline);
		call_user_func(array($this->logger, $method), $errstr, $errcontext);
	}
	
	/**
	 * (Non-catched) Exception handler
	 * @param \Exception $ex
	 */
	public function exception_handler(\Exception $ex) {
		call_user_func(array($this->logger, $this->exception_method), $ex->getMessage());
	}
	
	/**
	 * Shutdown handler
	 */
	public function shutdown_handler() {
		if (is_null($err = error_get_last())) {
			return;
		}
		
		$method = array_key_exists($err['type'], $this->error_methods) ? $this->error_methods[$errno] : 'addCritical';
		$errstr = sprintf("%s on file %s (line %d)", $err['message'], $err['file'], $err['line']);
		call_user_func(array($this->logger, $method), $errstr);
	}
	
	/**
	 * Adds a listener to the given method
	 * @param string $event
	 * @param \Closure $c
	 * @throws \RuntimeException
	 */
	public function addListener($event, \Closure $c) {
		if (!is_string($event) || empty($event)) {
			throw new \InvalidArgumentException("Parameter 'event' is not a valid string");
		}
		
		if (!array_key_exists($event, $this->events)) {
			$this->events[$event] = array();
		}
		
		$this->events[$event][] = $c;
	}
	
	/**
	 * Invokes all callbacks associated with an event
	 * @param string $event
	 * @return boolean
	 */
	public function trigger($event) {
		if (!array_key_exists($event, $this->events)) {
			return false;
		}
		
		foreach ($this->events[$event] as $c) {
			$c->__invoke($event, $this);
		}
		
		return true;
	}
}