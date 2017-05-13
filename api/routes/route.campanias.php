<?php 
	$app->group('/campanias', function() use($app, $db) {
	    $app->get('/', function() use($app, $db) {
			$result['data'] = $db->get('campanias'); 
	        if($result['data'])
	        	$result['status'] = 200;
	        echo json_encode($result);
	    });

	    $app->get('/:idCampania(/)', function($idCampania) use($app, $db) {
	    	$db->where('id_campanias', $idCampania);
			$result['data'] = $db->getOne('campanias'); 
	        if($result['data'])
	        	$result['status'] = 200;
	        echo json_encode($result);
	    });
	});