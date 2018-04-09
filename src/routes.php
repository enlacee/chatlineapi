<?php

use Slim\Http\Request;
use Slim\Http\Response;

const TABLE_USERS = 'users';
const TABLE_GROUPS = 'groups';
const TABLE_ROLES = 'roles';
const TABLE_GROUPS_USERS = 'groups_users';
// groupchat
const TABLE_MESSAGES = 'messages';

// $app->options('/{routes:.+}', function ($request, $response, $args) {
//     return $response;
// });

// $app->add(function ($request, $response, $next) {
// 	$response->getBody()->write('BEFORE');
// 	$response = $next($request, $response);
// 	$response->getBody()->write('AFTER');

// 	return $response;
// });

// Routes
/*
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            // ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
*/

$app->get('/', function (Request $request, Response $response, array $args) {
	// Sample log message
	$this->logger->info("Slim-Skeleton '/' route");

	// Render index view
	return $this->renderer->render($response, 'index.phtml', $args);
});
$app->get('/test', function (Request $request, Response $response, array $args) {
	// phpinfo();

    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "emailtest@YOURDOMAIN";
    $to = "acopitan@gmail.com";
    $subject = "PHP Mail Test script";
    $message = "This is a test to check the PHP Mail functionality";
    $headers = "From:" . $from;
    mail($to,$subject,$message, $headers);
    echo "Test email sent";

	exit;
});

/**
 * all routers
 */
$app->group('/v1', function () use ($app) {
	// generate test data
	$app->get('/faker-data',	'\AppView\Controller\FakerDataController:getAll');

	// Users
	$app->get('/users',			'\AppView\Controller\UserController:getAll');
	$app->get('/users-diccionary','\AppView\Controller\UserController:getUserDiccionary');
	$app->get('/users/{id}',	'\AppView\Controller\UserController:getById');
	$app->post('/users',		'\AppView\Controller\UserController:post');
	$app->put('/users/{id}',	'\AppView\Controller\UserController:put');
	$app->delete('/users/{id}',	'\AppView\Controller\UserController:delete');
	$app->post('/users-login',	'\AppView\Controller\UserController:login');
	$app->post('/users-forgot-password', '\AppView\Controller\UserController:forgotPassword');

	// Groups
	$app->get('/groups',		'\AppView\Controller\GroupController:getAll');
	$app->post('/groups',		'\AppView\Controller\GroupController:post');
	$app->get('/groups/{id}',	'\AppView\Controller\GroupController:getById');
	$app->put('/groups/{id}',	'\AppView\Controller\GroupController:put');
	$app->delete('/groups/{id}','\AppView\Controller\GroupController:delete');

	// Groups Users
	$app->get('/groups-users',		'\AppView\Controller\GroupUserController:getAll');
	$app->post('/groups-users',		'\AppView\Controller\GroupUserController:post');
	$app->get('/groups-users/{id}',	'\AppView\Controller\GroupUserController:getById');
	// $app->put('/groups-users/{id}',	'\AppView\Controller\GroupUserController:put'); // not usefull for this table
	$app->delete('/groups-users/{id}','\AppView\Controller\GroupUserController:delete');

	// Roles
	$app->get('/roles',			'\AppView\Controller\RolController:getAll');

	// Groups
	$app->get('/messages',		'\AppView\Controller\MessageController:getAll');
	$app->post('/messages',		'\AppView\Controller\MessageController:post');
	// $app->get('/messages/{id}',	'\AppView\Controller\MessageController:getById');
	// $app->put('/messages/{id}',	'\AppView\Controller\MessageController:put');
	// $app->delete('/messages/{id}','\AppView\Controller\MessageController:delete');

	//chat: obtener la lista de los usuarios por grupo
	$app->get('/chats-groups',	'\AppView\Controller\GroupUserController:getlistGroupByIdUser');
	$app->post('/upload-file', '\AppView\Controller\MessageController:uploadFile');

});

