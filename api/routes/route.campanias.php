<?php 
	$app->group('/campanias', function() use($app) {
	    $app->get('/', function() use($app) {
			$result = $db->get('campanias'); 
	        $app->render(200, array(
	            'code' => 200,
	            'status' => 'success',
	            'error' => false,
	            'data' => $result
	        ));
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