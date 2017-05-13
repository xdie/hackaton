<?php
header('Content-Type: application/json');

require '../lib/db/MysqliDb.php';
require '../lib/config.php';

//require 'models/Model.php';
//require 'models/Campania.php';

/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require '../lib/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

require '../lib/Slim/Exception/Request_Exception.php';
require '../lib/Slim/jsonAPI/JsonApiException.php';
require '../lib/Slim/jsonAPI/JsonApiMiddleware.php';
require '../lib/Slim/jsonAPI/JsonApiView.php';

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

// GET route
$app->group('/entidades', function() use($app) {
    $app->get('/', function() use($app) {
        echo "entidades/";
    });

    $app->get('/:idEntidad(/)', function($idEntidad) use($app) {
        echo "entidades/".$idEntidad;
    });
});

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

$app->group('/tipos-donaciones', function() use($app) {
    $app->get('/', function() use($app) {
        echo "tipos-donaciones/";
    });

    $app->get('/:idTipoDonacion(/)', function($idTipoDonacion) use($app) {
        echo "tipos-donaciones/".$idTipoDonacion;
    });
});

$app->group('/causas', function() use($app) {
    $app->get('/', function() use($app) {
        echo "causas/";
    });

    $app->get('/:idCausa(/)', function($idCausa) use($app) {
        echo "causas/".$idCausa;
    });
});

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
