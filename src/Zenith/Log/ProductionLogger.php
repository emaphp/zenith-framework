<?php
namespace Zenith\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Zenith\Application;

class ProductionLogger extends Logger {
	public function __construct() {
		parent::__construct('production');
		$log_path = Application::getInstance()->path('logs', 'production_' . date('Y-m-d') . '.log');
		$this->pushHandler(new StreamHandler($log_path, Logger::WARNING));
	} 
}