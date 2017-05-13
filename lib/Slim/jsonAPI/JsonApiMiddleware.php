<?php
/**
 * jsonAPI - Slim extension to implement fast JSON API's
 *
 * @package Slim
 * @subpackage Middleware
 * @author
 * @license GNU General Public License, version 3
 */

/**
 * JsonApiMiddleware - Middleware that sets a bunch of static routes for easy bootstrapping of json API's
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */
class JsonApiMiddleware extends \Slim\Middleware {
	/**
	 * Sets a buch of static API calls
	 *
	 */
	function __construct(array $config=array()){
		$app = \Slim\Slim::getInstance();
		$app->config('debug', false);

		$default_config = array(
			// If you want to get json value as object, set `true`.
			// If this value is `false`, set json value as array.
			'json_as_object' => false
		);

		$this->config = array_merge($default_config, $config);

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
			throw new JsonApiException(null, -0001);
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

			if ($app->request->getMediaType()=='application/json' && !empty($body)) {
				try {
					$params = json_decode($body, !$this->config['json_as_object']);
				} catch (ErrorException $e) {
					$err_msg = sprintf(
						'Unknown error occured: %s, json: %s',
						str_replace("json_decode(): ", "", $e->getMessage()),
						$body);
					throw new JsonApiException(null, -0002, null, array($err_msg));
				}

				if (json_last_error() !== JSON_ERROR_NONE) {
					$err_msg = sprintf('Body is not json format: %s', $body);
					throw new JsonApiException(null, -0002, null, array($err_msg));
				}

				$app->request()->setBody($params);
			}

			preg_match('/\/v\d+/i', $app->request->getRootUri(), $aMatches);

			if ($app->request->isPost() || $app->request->isPut()) {
				if (empty($params))
					throw new JsonApiException(null, -0002, null, array('Body cannot be empty.'));

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
}
