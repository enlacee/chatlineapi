<?php

use Slim\Http\Request;
use Slim\Http\Response;

const TABLE_USERS = 'users';
const TABLE_GROUPS = 'groups';
const TABLE_ROLES = 'roles';
const TABLE_GROUPS_USERS = 'groups_users';
// groupchat
const TABLE_MESSAGES_RECIPIENTS = 'messages_recipients';
const TABLE_MESSAGES = 'messages';

// Routes
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            // ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
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
	$app->put('/users/{id}',	'\AppView\Controller\UserController:put');
	$app->delete('/users/{id}',	'\AppView\Controller\UserController:delete');
	$app->post('/users-login',	'\AppView\Controller\UserController:login');

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

	//chat: obtener la lista de los usuarios por grupo
	$app->get('/chats-groups',	'\AppView\Controller\GroupUserController:getlistGroupByIdUser');

});

