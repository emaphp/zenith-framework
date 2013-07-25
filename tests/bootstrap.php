<?php
/**
 * Load composer autoloader
 */
$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->add('Zenith\\', __DIR__ . '/../src/');

/**
 * Directories
 */
//root directory
define('ROOT_DIR', __DIR__ . '/Zenith/');

//application directory
define('APP_DIR', ROOT_DIR . 'app/');

//storage directory
define('STORAGE_DIR', ROOT_DIR . 'app/storage/');

//services directory
define('SERVICES_DIR', APP_DIR . 'services/');

//configuration directory
define('CONFIG_DIR', APP_DIR . 'config/');

//views directory
define('VIEWS_DIR', APP_DIR . 'views/');

//application WSDL directory
define('WSDL_DIR', STORAGE_DIR . 'wsdl/');

//compiled templates directory
define('TWIG_DIR', STORAGE_DIR . 'twig');

//logs directory
define('LOGS_DIR', STORAGE_DIR . 'logs/');