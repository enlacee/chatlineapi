<?php

namespace AppView\Controller;

// use \Faker\Factory;
// use \Zend\Db\Adapter;

class UserController
{
	private $adapter;
	private $pdo;

	public function __construct($container)
	{
		$this->adapter = $container->get('adapter');
		$this->pdo = $container->get('pdo');

	}

	/**
	 * Get all uses
	 */
	public function getAll($request, $response, $args)
	{
		$faker = \Faker\Factory::create('es_PE');
		$dbh = $this->pdo;

		// insert roles
		$sth = $dbh->prepare('SELECT id_rol FROM roles');
		$sth->execute();
		$rsDat = $sth->fetchAll();
		if (is_array($rsDat) && count($rsDat) == 0) {
			$roles = array('superadmin', 'admin', 'user');
			foreach ($roles as $key => $value) {
				$sth = $dbh->prepare('INSERT INTO roles ( name ) VALUES (?)');
				$sth->execute(array($value));
			}
		}

		// insert user
		$sth = $dbh->prepare('SELECT id_user FROM users WHERE id_rol = ?');
		$sth->execute(array(1));
		$rsDat = $sth->fetchAll();
		if (is_array($rsDat) && count($rsDat) == 0) {
			$sth = $dbh->prepare('INSERT INTO users ( firstname, lastname, username, password, id_rol) VALUES (?, ?, ?, ?, ?)');
			$sth->bindValue(1, 'juan');
			$sth->bindValue(2, 'suarez');
			$sth->bindValue(3, 'juan@pprios.com');
			$sth->bindValue(4, 'juan');
			$sth->bindValue(5, 1, \PDO::PARAM_INT);
			$sth->execute();
		}

		$area = array('Administracion', 'Contabilidad', 'Sistemas', 'Producccion', 'Marketing', 'Diseño', 'Ventas');
		$cargo = array('Abogado', 'Ingeniero de sistemas', 'Asistente de ventas', 'Recepcionista', 'Ensamblador', 'Fontanero', 'Carpintero');
		for ($i=0; $i < 25; $i++) {
			$randonArea  = $area[\Faker\Provider\Base::numberBetween(0, count($area)-1)];
			$randonCargo  = $cargo[\Faker\Provider\Base::numberBetween(0, count($cargo)-1)];
			$dateCreated = \Faker\Provider\DateTime::dateTimeBetween('-2 days', 'now', 'America/Lima');
			$dateUpdated = \Faker\Provider\DateTime::dateTimeBetween('-2 days', 'now', 'America/Lima');

			$sth = $dbh->prepare(
				'INSERT INTO users ( firstname, lastname, username, password, id_rol, area, cargo, status, chat_plus, at_created, at_updated)' .
				' VALUES (?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?)'
			);
			$sth->bindValue(1, $faker->firstname, \PDO::PARAM_INT);
			$sth->bindValue(2, $faker->lastname, \PDO::PARAM_STR);
			$sth->bindValue(3, $faker->email, \PDO::PARAM_STR);
			$sth->bindValue(4, \Faker\Provider\Base::numberBetween(0, 999999999), \PDO::PARAM_STR);
			$sth->bindValue(5, \Faker\Provider\Base::numberBetween(2, 3), \PDO::PARAM_INT);

			$sth->bindValue(6, $randonArea, \PDO::PARAM_STR);
			$sth->bindValue(7, $randonCargo, \PDO::PARAM_STR);
			$sth->bindValue(8, \Faker\Provider\Base::numberBetween(0, 1), \PDO::PARAM_INT);
			$sth->bindValue(9, \Faker\Provider\Base::numberBetween(0, 1), \PDO::PARAM_INT);
			$sth->bindValue(10, $dateCreated->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
			$sth->bindValue(11, $dateUpdated->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
			$sth->execute();
		}

		// insert groups
		$sth = $dbh->prepare('SELECT id_group FROM groups');
		$sth->execute();
		$rsDat = $sth->fetchAll();
		if (is_array($rsDat) && count($rsDat) == 0) {
			foreach ($area as $key => $value) {
				$dateCreated = \Faker\Provider\DateTime::dateTimeBetween('-2 days', 'now', 'America/Lima');
				$sth = $dbh->prepare('INSERT INTO groups ( name, at_created ) VALUES (?, ?)');
				$sth->execute(array($value, $dateCreated->format('Y-m-d H:i:s')));
			}
		}

		echo "data faker generated!.<br/>";

		return $response->withJson(array());
	}

}
