<?php
namespace Zenith;

/**
 * SOAP service base class
 * @author emaphp
 * 
 * Dependencies:
 * 
 * @inject.provider Zenith\IoC\Provider\ViewServiceProvider
 * @inject.provider Zenith\IoC\Provider\LoggerServiceProvider
 */
class SOAPService {
	/**
	 * @inject.service view
	 */
	protected $view;
	
	/**
	 * @inject.service logger
	 */
	protected $logger;
}