<?php
header('Content-Type: application/json');

include '../vendor/autoload.php';
include '../config.php';

$app = new \Slim\Slim();

require 'routes/route.campanias.php';
require 'routes/route.login.php';

$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});

$app->run();