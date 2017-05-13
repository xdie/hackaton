<?php 
$app->group('/causas', function() use($app, $db) {
    $app->get('/', function() use($app, $db) {
		$result['data'] = $db->get('causa'); 
        if($result['data'])
        	$result['status'] = 200;
        echo json_encode($result);
    });

    $app->get('/:idCausa(/)', function($idCausa) use($app, $db) {
    	$db->where('id_causa', $idCausa);
		$result['data'] = $db->getOne('causa'); 
        if($result['data'])
        	$result['status'] = 200;
        echo json_encode($result);
    });
});