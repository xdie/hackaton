<?php
/**
 * FormData - Slim extension to implement fast FormData API's
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
*/

/**
 * FormDataView - view wrapper for xml responses (with error code).
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */
class FormDataApiView extends \Slim\View {
    public function render($status=200, $data = NULL) {
    	$app = \Slim\Slim::getInstance();

    	$responseFormat = ($app->request->headers->get('responseFormat')) ? $app->request->headers->get('responseFormat') : "application/json";

        $status = intval($status);

        $response = $this->all();

        //append error bool
        if (!$this->has('error')) {
            $response['error'] = false;
        }

        //append status code
        //$response['status'] = $status;

		//add flash messages
		if (isset($this->data->flash) && is_object($this->data->flash)) {
		    $flash = $this->data->flash->getMessages();
            if (count($flash)) {
                $response['flash'] = $flash;   
            } else {
                unset($response['flash']);
            }
		}

        $app->response->setStatus($status);
        $app->response()->header('Content-Type', $responseFormat);

        $responseFinal = ($responseFormat=='application/xml') ? $this->array2xml($response) : json_encode($response, false);
        $app->response()->body($responseFinal);

        $app->stop();
    }

    public function array2xml($array, $xml = false) {
    	if ($xml === false) {
    		$xml = new SimpleXMLElement('<root/>');
    	}
    	foreach ($array as $key => $value) {
    		if (is_array($value)) {
    			$this->array2xml($value, $xml->addChild($key));
    		} else {
    			$xml->addChild($key, $value);
    		}
    	}
    	return $xml->asXML();
    }
}
