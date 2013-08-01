<?php
namespace Zenith\Error;

class ErrorHandler {
	/**
	 * Error logger
	 * @var Monolog\Logger
	 */
	public $logger;
	
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
	 * When true, a custom soap fault will be generated in case a critical error is thrown
	 * @var bool
	 */
	public $safe_mode = true;
	
	/**
	 * Logs an exception
	 * @param \Exception $ex
	 */
	public function logException(\Exception $ex) {		
		if (isset($this->logger)) {
			$message = sprintf("%s on file %s (line %d) with message '%s'", get_class($ex), $ex->getFile(), $ex->getLine(), $ex->getMessage());
			call_user_func(array($this->logger, $this->exception_method), $message);
			return;
		}
	}
	
	/**
	 * Logs an error
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @param array $errcontext
	 */
	public function logError($errno, $errstr, $errfile, $errline, $errcontext) {		
		if (isset($this->logger)) {
			$message = sprintf("%s on file %s (line %d)", $errstr, $errfile, $errline);
			$log_method = array_key_exists($errno, $this->error_methods) ? $this->error_methods[$errno] : 'addError';
			call_user_func(array($this->logger, $log_method), $message, $errcontext);
		}
	}

	/**
	 * Determines if a given error type will halt the application
	 * @param int $errno
	 * @return boolean
	 */
	protected function error_wont_halt($errno) {
		$flags = E_NOTICE | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING |
		E_USER_NOTICE | E_STRICT | E_DEPRECATED | E_USER_DEPRECATED;
	
		return $errno & $flags;
	}
	
	/**
	 * Sends a custom soap fault to output
	 * @param string $message
	 */
	protected function send_custom_fault($message) {
		//build fault content
		$fault = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><SOAP-ENV:Fault><faultcode>Server</faultcode><faultstring>$message</faultstring></SOAP-ENV:Fault></SOAP-ENV:Body></SOAP-ENV:Envelope>
XML;
		if (!headers_sent()) {
			//send headers
			header('HTTP/1.1 500 Internal Service Error');
			header('Content-Type: text/xml; charset=utf-8');
			echo $fault;
		}
	}
	
	/**
	 * Error handling method
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @param array $errcontext
	 */
	public function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
		//log error data
		$this->logError($errno, $errstr, $errfile, $errline, $errcontext);
		
		//if the error will halt the application a custom soap fault is builded
		if (!$this->error_wont_halt($errno) && php_sapi_name() != 'cli' && $this->safe_mode) {
			$this->send_custom_fault($errstr);
		}
		
		return isset($this->logger);
	}
	
	/**
	 * (Non-catched) Exception handler method
	 * @param \Exception $ex
	 */
	public function exception_handler(\Exception $ex) {
		$this->logException($ex);
		
		if (php_sapi_name() != 'cli' && $this->safe_mode) {
			$this->send_custom_fault($ex->getMessage());
		}
	}
	
	/**
	 * Shutdown handler
	 */
	public function shutdown_handler() {
		if (is_null($err = error_get_last())) {
			return;
		}
	
		//log error
		$this->logError($err['type'], $err['message'], $err['file'], $err['line'], array());
		
		if (!$this->error_wont_halt($err['type']) && php_sapi_name() != 'cli' && $this->safe_mode) {
			$this->send_custom_fault($err['message']);
		}
	}
}