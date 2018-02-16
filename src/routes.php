<?php

use Slim\Http\Request;
use Slim\Http\Response;

use AppView\Model\UserTable;
use AppView\Model\GroupTable;
use AppView\Model\RolTable;
use \Zend\Db\TableGateway\TableGateway;

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
 * list all users
 */
$app->get('/users', function ($request, $response, $args) {

	$adapter = $this->get('adapter');
	$table = new UserTable(new TableGateway(TABLE_USERS, $adapter));
	$data = $table->fetchAll();

	return $response->withJson($data);
});

/**
 * list all groups
 */
$app->get('/groups', function ($request, $response, $args) {

	$adapter = $this->get('adapter');
	$table = new GroupTable(new TableGateway(TABLE_GROUPS, $adapter));
	$data = $table->fetchAll();

	return $response->withJson($data);
});

/**
 * list all roles
 */
$app->get('/roles', function ($request, $response, $args) {

	$adapter = $this->get('adapter');
	$table = new RolTable(new TableGateway(TABLE_ROLES, $adapter));
	$data = $table->fetchAll();

	return $response->withJson($data);
});

$app->post('/login', function ($request, $response, $args) {
	
	$rs = false;

	$adapter = $this->get('adapter');
	$username = $request->getParam('username');
	$password = $request->getParam('password');

	if (!empty($username) && !empty($password)) {
		$table = new UserTable(new TableGateway(TABLE_USERS, $adapter));
		$rs = $table->login($username, $password);
	}

	return $response->withJson($rs);
});


/**
 * Create data fake for all tables
 * /faker/fill
 */
$app->group('/faker', function () use ($app) {

	$app->get('/fill', '\AppView\Controller\UserController:getAll');
});

