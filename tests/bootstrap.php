<?php
/**
 * Load composer autoloader
 */
$loader = require __DIR__ . "/../vendor/autoload.php";

/**
 * Setup library directory
 */
$loader->add('Zenith\\', __DIR__ . '/../src/');

/**
 * Load application directories
 */
$paths = require __DIR__ . '/Zenith/bootstrap/paths.php';

/**
 * Setup application paths
 */
$app = Zenith\Application::getInstance();

$container = new Pimple\Container;
$container['environment'] = 'testing';
$container['paths'] = $paths;
Injector\Injector::inject($app, $container);

/**
 * Add autoload folders
 */
$loader->add('', Zenith\Application::getInstance()->path('services'));
$loader->add('', Zenith\Application::getInstance()->path('components'));