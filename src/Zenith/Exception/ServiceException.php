<?php
namespace Zenith\Exception;

class ServiceException extends \Exception {
	/**
	 * Status code
	 * @var int
	 */
	protected $statusCode;
	
	/**
	 * Status message
	 * @var string
	 */
	protected $statusMessage;
	
	public function __construct($statusCode, $statusMessage) {
		$this->statusCode = $statusCode;
		$this->statusMessage = $statusMessage;
		$this->message = $statusMessage;
	}
	
	public function getStatusCode() {
		return $this->statusCode;
	}
	
	public function getStatusMessage() {
		return $this->statusMessage;
	}
}