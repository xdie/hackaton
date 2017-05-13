<?php
class Model {
	protected static $oInstance = null;

	protected $db = null;
	protected $dbe = null;

	public static function getInstance() {
		if (!isset(self::$oInstance))
			self::$oInstance = new self();

		return self::$oInstance;
	}

	protected function __construct() {
		if (!isset($this->db)) {
			$this->db = PDO_Wrapper::getInstance();

			if (!isset($this->dbe))
				$this->dbe = PDO_Wrapper_Extended::getInstance();
		}
	}
}