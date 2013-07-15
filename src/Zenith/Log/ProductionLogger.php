<?php
namespace Zenith\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ProductionLogger extends Logger {
	public function __construct() {
		parent::__construct('production');
		$this->pushHandler(new StreamHandler(LOGS_DIR . 'production_' . date('Y-m-d') . '.log', Logger::WARNING));
	} 
}