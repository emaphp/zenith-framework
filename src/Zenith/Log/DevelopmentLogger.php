<?php
namespace Zenith\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Zenith\Application;

class DevelopmentLogger extends Logger {
	public function __construct() {
		parent::__construct('development');
		$log_path = Application::getInstance()->path('logs', 'development_' . date('Y-m-d') . '.log');
		$this->pushHandler(new StreamHandler($log_path, Logger::DEBUG));
	} 
}