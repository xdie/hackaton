<?php
/**
 * FormData - Slim extension to implement fast FormData API's
 *
 * @package Slim
 * @subpackage Middleware
 * @author
 * @license GNU General Public License, version 3
 */

/**
 * FormDataMiddleware - Middleware that sets a bunch of static routes for easy bootstrapping of XML API's
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */
class FormDataApiMiddleware extends \Slim\Middleware {
	/**
	 * Sets a buch of static API calls
	 *
	 */
	function __construct() {
		$app = \Slim\Slim::getInstance();
		$app->config('debug', false);

		// Mirrors the API request
		$app->get('/return', function() use ($app) {
			$app->render(200,array(
				'method'    => $app->request()->getMethod(),
				'name'      => $app->request()->get('name'),
				'headers'   => $app->request()->headers(),
				'params'    => $app->request()->params(),
			));
		});

		// Generic error handler
		$app->error(function (Exception $e) use ($app) {
			parent::error($e);
		});

		// Not found handler (invalid routes, invalid method types)
		$app->notFound(function() use ($app) {
			throw new FormDataApiException(null, -0001);
		});

		// Handle Empty response body
		$app->hook('slim.after.router', function () use ($app) {
			//Fix sugested by: https://github.com/bdpsoft
			//Will allow download request to flow
			if($app->response()->header('Content-Type')==='application/octet-stream'){
				return;
			}

			if (strlen($app->response()->body()) == 0) {
				$app->render(500,array(
					'error' => TRUE,
					'msg'   => 'Empty response',
				));
			}
		});
	}

	/**
	 * Call next
	 */
	function call() {
		$app = $this->app;

		$aPost = null;

		$app->hook('slim.before.router', function() use ($app) {
			$body = $app->request->getBody();

			if (empty($body))
				$body = $app->request->post();

			// Decode each body parameter
			$params = $this->decodeBody($body);

			$app->request()->setBody($params);

			preg_match('/\/v\d+/i', $app->request->getRootUri(), $aMatches);

			if ($app->request->isPost() || $app->request->isPut()) {
				if (empty($params))
					throw new FormDataApiException(null, -0002, null, array('Body cannot be empty.'));

				$aPost = $params;
			}

			switch(trim($aMatches[0], '/')) {
				case 'v2':
					$oModel = new Model();
					$oModel->log($aPost);
					break;
				case 'v3':
					$oModel = Model::getInstance();
					$oModel->log($aPost);
					break;
			}
		});

		$this->next->call();
	}

	public function decodeBody($aBody) {
		foreach ($aBody as $k=>$v) {
			if (is_array($v))
				$aBody[$k] = $this->decodeBody($v);
			else
				$aBody[$k] = urldecode($v);
		}

		return $aBody;
	}
}