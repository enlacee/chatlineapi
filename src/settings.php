<?php
date_default_timezone_set('America/lima');

return [
	'settings' => [
		'displayErrorDetails' => true, // set to false in production
		'addContentLengthHeader' => false, // Allow the web server to send the content-length header

		// Renderer settings
		'renderer' => [
			'template_path' => __DIR__ . '/../templates/',
		],

		// Monolog settings
		'logger' => [
			'name' => 'slim-app',
			'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
			'level' => \Monolog\Logger::DEBUG,
		],
		'database' => [
			'host' => 'localhost',
			'user' => 'admin',
			'pass' => 'admin',
			'dbname' => 'chatline',
		],
		// 'globalVariables' => [
		// 	'salt' => 'h[2Q+z3H8mdOy|4BkeXn@pWsGq{(1ex1A>*LZ@vMHky-eUL5&/l8j03~.4ws(pn7',
		// ]
	],
];
