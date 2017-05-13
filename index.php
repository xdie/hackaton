<?php
include 'vendor/autoload.php';

require 'api/routes/route.campanias.php';



$app = new \Slim\Slim();
$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});
$app->run();