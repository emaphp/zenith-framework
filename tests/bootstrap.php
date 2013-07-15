<?php
$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->add('Zenith\\', __DIR__ . '/../src/');

//application root directory
define(ROOT_DIR, __DIR__ . '/Zenith');

//services directory
define('SERVICES_DIR',  ROOT_DIR . '/app/services/');

//configuration directory
define('CONFIG_DIR',    ROOT_DIR . '/app/config/');

//views directory
define('VIEWS_DIR',     ROOT_DIR . '/app/views/');

//cache directory
define('CACHE_DIR',     ROOT_DIR . '/store/cache/');

//cached templates directory
define('TPL_CACHE_DIR', ROOT_DIR . '/store/tpl');

//logs directory
define('LOGS_DIR',      ROOT_DIR . '/store/logs/');