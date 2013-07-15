<?php
namespace Zenith\IoC;

abstract class Container extends \Injector {
	public abstract function configure();
}