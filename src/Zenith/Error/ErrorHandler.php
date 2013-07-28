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
	
		$method = array_key_exists($err['type'], $this->error_methods) ? $this->error_methods['type'] : 'addCritical';
		$errstr = sprintf("%s on file %s (line %d)", $err['message'], $err['file'], $err['line']);
		call_user_func(array($this->logger, $method), $errstr);
	}
}