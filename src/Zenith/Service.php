<?php
namespace Zenith;

abstract class Service {
	use \Injector\Injectable;
	public $container = 'Zenith\IoC\ServiceContainer';
}