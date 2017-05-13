<?php
/**
 * jsonAPI - Slim extension to implement fast JSON API's
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */

/**
 * JsonApiView - view wrapper for json responses (with error code).
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */
class JsonApiView extends \Slim\View {
    public $encodingOptions = 0;

    public function render($status=200, $data = NULL) {
        $app = \Slim\Slim::getInstance();

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
        $app->response()->header('Content-Type', 'application/json');

        $jsonp_callback = $app->request->get('callback', null);

        if($jsonp_callback !== null){
            $app->response()->body($jsonp_callback.'('.json_encode($response, $this->encodingOptions).')');
        } else {
            $app->response()->body(json_encode($response, $this->encodingOptions));
        }

        $app->stop();
    }
}
