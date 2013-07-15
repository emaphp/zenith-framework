<?php
namespace Zenith\SOAP;

class Response {
	protected $service = array();
	protected $status  = array();
	protected $result;
	
	public function setService($class, $method) {
		$this->service = array('class' => $class, 'method' => $method);
	}
	
	public function setStatus($code, $message) {
		$this->status = array('code' => $code, 'message' => $message);
	}
	
	public function setResult($result) {
		$this->result = array('any' => $this->result);
	}
	
	public function build() {
		$response = array('service' => $this->service,
						  'status' => $this->status,
						  'result' => $this->result);
		return $response;
	}
}