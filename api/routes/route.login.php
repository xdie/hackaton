<?php 
$app->group('/login', function() use($app, $db) {
    $app->post('/', function() use($app, $db) {
    	print_r(array($app->request->post('email'), $app->request->post('password')));exit;
    	$db->where('email', $app->request->post('email'));
    	$db->where('password', md5($app->request->post('password')));

    	$result = $db->get('users');
    	print_r($result);exit;
    });
});