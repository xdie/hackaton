<?php
class XmlApiSimpleXMLExtended extends SimpleXMLElement {
	public function addCData($string){
		$dom = dom_import_simplexml($this);
		$cdata = $dom->ownerDocument->createCDATASection($string);
		$dom->appendChild($cdata);
	}
}