<?php
namespace Zenith\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DevelopmentLogger extends Logger {
	public function __construct() {
		parent::__construct('development');
		$this->pushHandler(new StreamHandler(LOGS_DIR . 'development_' . date('Y-m-d') . '.log', Logger::DEBUG));
	} 
}