<?php 
	$app->group('/campanias', function() use($app, $db) {
	    $app->get('/', function() use($app, $db) {
			$result['data'] = $db->get('campanias'); 
	        if($result['data'])
	        	$result['status'] = 200;
	    });

	    $app->get('/:idCampania(/)', function($idCampania) use($app) {
	        $oCampania = Campania::getInstance();
	        $aCampania = $oCampania->get($idCampania);

	        $app->render(200, array(
	            'code' => 200,
	            'status' => 'success',
	            'error' => false,
	            'data' => $aCampania
	        ));
	    });
	});