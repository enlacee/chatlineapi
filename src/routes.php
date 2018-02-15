<?php

use Slim\Http\Request;
use Slim\Http\Response;


// Routes

// $app->get('/[{name}]', function (Request $request, Response $response, array $args) {
//     // Sample log message
//     $this->logger->info("Slim-Skeleton '/' route");

//     // Render index view
//     return $this->renderer->render($response, 'index.phtml', $args);
// });
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


$app->get('/users', function ($request, $response, $args) {

	$data = array(
		array('id'=> '1', 'name'=> 'pepe lucho'),
		array('id'=> '2', 'name'=> 'juan carlos'),
		array('id'=> '3', 'name'=> 'liza malitr')
	);



	return $response->withJson($data);
});


$app->post('/login', function ($request, $response, $args) {
	// Show book identified by $args['id']
	$data['status'] = true;
	$data['data'] = 'pepe lucho';

	return $response->withJson($data);
});


/**
 * API REST
 */
$app->group('/faker', function () use ($app) {

	$app->get('/fill', '\AppView\Controller\UserController:getAll');
});

