<?php
namespace Zenith\Error;

interface IErrorHandler {
	public function error_handler($errno, $errstr, $errfile, $errline, $errcontext);
	public function exception_handler(\Exception $ex);
	public function shutdown_handler();
}