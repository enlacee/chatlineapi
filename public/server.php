<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app-view/autoload.php'; // add module app-view

use Workerman\Worker;
use PHPSocketIO\SocketIO;

// start slimframework
$settings = require __DIR__ . '/../src/settings.php'; // here are return ['settings'=>'']

$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';  //$app->getContainer();

// end slimframework
$adapter = $app->getContainer()->get('adapter');
$tableGateWay = new \Zend\Db\TableGateway\TableGateway('messages', $adapter);
$table = new \AppView\Model\UserTable($tableGateWay);
// $tableGateWay->insert($dataPersona);
// $data = $table->fetchAll();
// var_dump($data);exit;


// Construct socket
$usernames = [];
$numUsers = 0;

$io = new SocketIO(2020);

// iniciamos la conexión de cada socket que ha entrado en la aplicación
$io->on('connection', function($socket){

	$socket->loggedUser = false;

	// cuando se ejecute en el cliente el evento add user 
	$socket->on('add user', function($username) use ($socket){
		global $usernames, $numUsers;

		// guardamos el usuario en sesión
		$socket->username = $username;

		// añadimos al cliente a la lista global
		$usernames[$username] = $username;
		++$numUsers;
		$socket->loggedUser = true;
		$socket->emit('login', array(
			'numUsers' => $numUsers,
			'usernames'=> $usernames,
		));

		// notificamos a todos que un usuario ha entrado
		$socket->broadcast->emit('user joined', array(
			'username' => $socket->username,
			'numUsers' => $numUsers,
			'usernames'=> $usernames
		));
	});

	// cuando se ejecute en el cliente el evento new message
	$socket->on('new message', function($message) use($socket){

		// me notifico del mensaje que he escrito
		$message = array_merge($message, array(
			'at_created'		=> date('Y-m-d H:i:s'),
			'username_on_server'	=> $socket->username
		));
		$socket->emit('new message', array(
			'action' => 'yo',
			'message'=> $message
		));

		// notificamos al resto del mensaje que he escrito
		$socket->broadcast->emit('new message', array(
			'action' => 'chat',
			// 'message'=> $socket->username .' dice: ' . $message
			'message'=> $message
		));
	});

	// cuando se ejecute en el cliente el evento user logout
	$socket->on('user logout', function() use($socket){
		global $usernames, $numUsers;

		if ($socket->loggedUser){
			
			// actualizamos la lista de usuarios conectados
			unset($usernames[$socket->username]);
			--$numUsers;

			// actualizamos el usuario que sale
			$socket->emit('user left', array(
				'numUsers' => $numUsers,
				'usernames' => $usernames,
			));

			// notificamos de forma global que el usuario está fuera
			$socket->broadcast->emit('user left', array(
				'username' => $socket->username,
				'numUsers' => $numUsers,
				'usernames' => $usernames
			));
		}
	});

	// evento de socketio cada vez que un nuevo socket se desconecta (cierra la web o actuliza el navegador)
	$socket->on('disconnect', function() use($socket){
		global $usernames, $numUsers;

		// eliminamos al usuario de la lista de usuarios
		if($socket->loggedUser){

			// actualizamos la lista de usuarios conectados 
			unset($usernames[$socket->username]);
			--$numUsers;

			// notificamos de forma global que el usuario está fuera
			$socket->broadcast->emit('user left', array(
				'username' => $socket->username,
				'numUsers' => $numUsers
			));
		}
	});
});

Worker::runAll();