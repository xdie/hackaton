<?php
header('Content-Type: application/json');

include '../vendor/autoload.php';
include '../config.php';

$app = new \Slim\Slim();

$app->response()->header('Content-Type', 'application/json');

if ($app->request->isPost() || $app->request->isPut()) {
  	$body = $app->request->getBody();
	$params = json_decode($body, true);
	$app->request()->setBody($params);
}

require 'routes/route.campanias.php';
require 'routes/route.causas.php';
require 'routes/route.login.php';

// http://help.slimframework.com/discussions/problems/760-backbonejs-slim-and-cross-domain-question
if ("OPTIONS" == $_SERVER['REQUEST_METHOD']) { exit(0); }

$app->run();