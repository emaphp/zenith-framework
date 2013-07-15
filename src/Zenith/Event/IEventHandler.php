<?php
namespace Zenith\Event;

interface IEventHandler {
	public function error_handler($errno, $errstr, $errfile, $errline, $errcontext);
	public function exception_handler(\Exception $ex);
	public function shutdown_handler();
	public function addListener($event, \Closure $c);
	public function trigger($event);
}