<?php
require __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use PHPSocketIO\SocketIO;

$usernames = [];
$numUsers = 0;

$io = new SocketIO(2020);

// iniciamos la conexión de cada socket que ha entrado en la aplicación
$io->on('connection', function($socket){

	$socket->loggedUser = false;

	// cuando se ejecute en el cliente el evento add user 
	$socket->on('add user', function($username) use ($socket){
		global $usernames, $numUsers;
echo "entro ADD USER";
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
		$socket->emit('new message', array(
			'action' => 'yo',
			'message'=> 'Yo: '. $message
		));

		// notificamos al resto del mensaje que he escrito
		$socket->broadcast->emit('new message', array(
			'action' => 'chat',
			'message'=> $socket->username .' dice: ' . $message
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