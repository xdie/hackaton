<?php
class Campania extends Model {
	protected static $oInstance = null;

	private $sTable = 'campanias';

	public static function getInstance() {
		if (!isset(self::$oInstance))
			self::$oInstance = new self();

		return self::$oInstance;
	}

	public function get($piIdCampania=false) {
		$sSql = "SELECT * FROM {$this->sTable}".(((bool) $piIdCampania) ? " WHERE id_campanias=:id" : "");

		return $this->db->query($sSql, array('id'=>$piIdCampania));
	}
}