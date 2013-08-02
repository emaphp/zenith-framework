<?php
namespace Zenith\SOAP;

class Response {
	/**
	 * Conversion constants
	 */
	const AS_RAW = 0;
	const AS_XML = 1;
	const AS_SIMPLEXML = 2;
	const AS_DOM = 3;
	
	/**
	 * Service section
	 * @var array
	 */
	protected $service = array();
	
	/**
	 * Status section
	 * @var array
	 */
	protected $status  = array();
	
	/**
	 * Result to return
	 * @var string
	 */
	protected $result;
	
	/**
	 * Sets values for the 'service' section
	 * @param string $class
	 * @param string $method
	 */
	public function setService($class, $method) {
		$this->service = array('class' => $class, 'method' => $method);
	}
	
	/**
	 * Sets values for the 'status' section
	 * @param int $code
	 * @param string $message
	 */
	public function setStatus($code, $message) {
		$this->status = array('code' => $code, 'message' => $message);
	}
	
	/**
	 * Sets the response result
	 * @param string $result
	 * @param bool $wrap
	 */
	public function setResult($result, $wrap = true) {
		$this->result = $wrap ? array('any' => $result) : $result;
	}
	
	/**
	 * Obtains the 'class' value from the 'service' section
	 * @return string
	 */
	public function getClass() {
		return $this->service['class'];
	}
	
	/**
	 * Obtains the 'method' value from the 'service' section
	 * @return string
	 */
	public function getMethod() {
		return $this->service['method'];
	}
	
	/**
	 * Obtains the 'code' value from the 'status' section
	 * @return string
	 */
	public function getStatusCode() {
		return $this->status['code'];
	}
	
	/**
	 * Obtains the 'message' value from the 'status' section
	 * @return string
	 */
	public function getStatusMessage() {
		return $this->status['message'];
	}
	
	/**
	 * Obtains the result returned in the request
	 * @param int $as
	 * @return SimpleXMLElement|\DOMDocument|\stdClass|string
	 */
	public function getResult($as = self::AS_RAW) {
		//check if result is a simple string
		if (is_array($this->result->any) && array_key_exists('text', $this->result->any)) {
			return $this->result->any['text'];
		}
		elseif ($as == self::AS_XML) {
			return $this->result->any;
		}
		elseif ($as == self::AS_SIMPLEXML) {
			//convert to SimpleXMLElement
			$success = simplexml_load_string($this->result->any);
			
			if ($success === false) {
				$error = libxml_get_last_error();
				throw new \RuntimeException("XML Syntax error: " . $error->message);
			}
			
			return $success;
		}
		elseif ($as == self::AS_DOM) {
			//convert to DOMDocument
			$dom = new \DOMDocument();
			
			if (!$dom->loadXML($this->result->any)) {
				$error = libxml_get_last_error();
				throw new \RuntimeException("XML Syntax error: " . $error->message);
			}
			
			return $dom;
		}
		
		return $this->result;
	}
	
	/**
	 * Builds response
	 * @return array
	 */
	public function build() {
		$response = array('service' => $this->service,
						  'status' => $this->status,
						  'result' => $this->result);
		return $response;
	}
}