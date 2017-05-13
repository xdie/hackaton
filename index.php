<?php
include 'vendor/autoload.php';

$app = new \Slim\Slim();

require 'api/routes/route.campanias.php';

$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});

$app->run();