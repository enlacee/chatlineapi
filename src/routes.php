<?php

use Slim\Http\Request;
use Slim\Http\Response;

const TABLE_USERS = 'users';
const TABLE_GROUPS = 'groups';
const TABLE_ROLES = 'roles';

// Routes
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

/**
 * all routers
 */
$app->group('/v1', function () use ($app) {

	$app->get('/faker-data',	'\AppView\Controller\FakerDataController:getAll');

	// Users
	$app->get('/users',			'\AppView\Controller\UserController:getAll');
	$app->get('/users/{id}',	'\AppView\Controller\UserController:getById');
	$app->post('/users',		'\AppView\Controller\UserController:post');
	$app->put('/users',			'\AppView\Controller\UserController:put');
	$app->delete('/users',		'\AppView\Controller\UserController:delete');
	$app->post('/users-login',	'\AppView\Controller\UserController:login');

	// Groups
	$app->get('/groups',		'\AppView\Controller\GroupController:getAll');

	// Roles
	$app->get('/roles',			'\AppView\Controller\RolController:getAll');
});

