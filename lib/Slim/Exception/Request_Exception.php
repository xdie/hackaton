<?php
class Request_Exception extends Exception {
	private $iHttpStatusCode = null;

	private $aErrors = null;

	private $aCodes = array(
		-0001 => array('iHttpStatusCode'=>404, 'sMessage'=>'Route is not valid.'),
		-0002 => array('iHttpStatusCode'=>400, 'sMessage'=>'Please check body request.')
	);

	// NOTE: Validate that works fine in production environment (PHP version)
	public function __construct($psMessage=null, $piCode=null, $ptPrevious=null, $paErrors=null) {
		$this->iHttpStatusCode = $this->aCodes[$piCode]['iHttpStatusCode'];

		$this->aErrors = $paErrors;

		if (is_null($psMessage))
			$psMessage = $this->aCodes[$piCode]['sMessage'];

		parent::__construct($psMessage, $piCode, $ptPrevious);
	}

	public function getHttpStatusCode() {
		return $this->iHttpStatusCode;
	}

	public function getErrors() {
		return $this->aErrors;
	}
}
