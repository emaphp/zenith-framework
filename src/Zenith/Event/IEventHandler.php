<?php
namespace Zenith\Event;

use Monolog\Logger;

interface IEventHandler {
	public function setEventLogger(Logger $logger);
	public function addListener($event, \Closure $c);
	public function trigger($event);
	public function error_handler($errno, $errstr, $errfile, $errline, $errcontext);
	public function exception_handler(\Exception $ex);
	public function shutdown_handler();
}