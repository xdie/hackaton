<?php 
$app->group('/login', function() use($app, $db) {
    $app->post('/', function() use($app, $db) {
    	$db->where('email', $app->request->post('email'));
    	$db->where('contraseña', md5($app->request->post('password')));

    	$result = $db->get('usuarios');
    	echo json_encode($result[0]);
    });
});