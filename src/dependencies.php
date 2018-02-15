<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// $container['adapter'] = function($c) {
// 	$adapter = new Zend\Db\Adapter\Adapter([
// 		'driver'   => 'Pdo_Sqlite',
// 		'database' => dirname(__FILE__) . '/database.sqlite3',
// 	]);

// 	return $adapter;
// };

$container['pdo'] = function ($c) {
	$db = $c->get('settings')['database'];
	$pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
		$db['user'], $db['pass']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $pdo;
};

$container['adapter'] = function ($c) {

	$db = $c->get('settings')['database'];
	$adapter = new \Zend\Db\Adapter\Adapter(array(
		'driver' => 'Mysqli',
		'database' => $db['dbname'],
		'username' => $db['user'],
		'password' => $db['pass']
	));

	return $adapter;
};

