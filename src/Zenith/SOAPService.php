<?php
namespace Zenith;

/**
 * SOAP service base class
 * @author emaphp
 * 
 * Dependencies:
 * 
 * @Provider Zenith\IoC\Provider\ViewServiceProvider
 * @Provider Zenith\IoC\Provider\LoggerServiceProvider
 */
class SOAPService {
	/**
	 * @Inject view
	 */
	protected $view;
	
	/**
	 * @Inject logger
	 */
	protected $logger;
}