<?php

namespace AppView\Controller;

use \Faker\Factory;
use \Zend\Db\Adapter\Adapter;

class FakerDataController
{
	private $adapter;
	private $pdo;

	private $arrayGroup = array(
		array('name' => 'Administracion', 'status' => 1),
		array('name' => 'Contabilidad', 'status' => 1),
		array('name' => 'Sistemas', 'status' => 1),
		array('name' => 'Producccion', 'status' => 1),
		array('name' => 'Marketing', 'status' => 1),
		array('name' => 'Diseño', 'status' => 1),
		array('name' => 'Ventas', 'status' => 1),
		array('name' => 'ChatPeer', 'status' => 0),
	);
	private $arrayRoles = array(
		array('name' => 'superadmin', 'status' => 1),
		array('name' => 'admin', 'status' => 1),
		array('name' => 'user', 'status' => 0)
	);
	private $arrayCargo = array('Abogado', 'Ingeniero de sistemas', 'Asistente de ventas', 'Recepcionista', 'Ensamblador', 'Fontanero', 'Carpintero');
	private $dateCreated;

	public function __construct($container)
	{
		$this->adapter = $container->get('adapter');
		$this->pdo = $container->get('pdo');

		$this->dateCreated = \Faker\Provider\DateTime::dateTimeBetween('-2 days', 'now', 'America/Lima');
	}

	private function getPasswordGenerated() {

		return password_hash('clavefacil#123', PASSWORD_BCRYPT);
	}

	/**
	 * Get all uses
	 */
	public function getAll($request, $response, $args)
	{
		$faker = \Faker\Factory::create('es_PE');
		$dbh = $this->pdo;
		$area = $this->arrayGroup;
		$cargo = $this->arrayCargo;

		// insert roles
		$sth = $dbh->prepare('SELECT id_rol FROM roles');
		$sth->execute();
		$rsDat = $sth->fetchAll();
		if (is_array($rsDat) && count($rsDat) == 0) {

			foreach ($this->arrayRoles as $key => $value) {
				$sth = $dbh->prepare('INSERT INTO roles ( name, status ) VALUES (?, ?)');
				$sth->execute(array($value['name'], $value['status']));
			}
		}

		// insert user
		$sth = $dbh->prepare('SELECT id_user FROM users WHERE id_rol = ?');
		$sth->execute(array(1));
		$rsDat = $sth->fetchAll();
		if (is_array($rsDat) && count($rsDat) == 0) {
			// create super user
			$sth = $dbh->prepare('INSERT INTO users ( firstname, lastname, username, password, id_rol, dni, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
			$sth->bindValue(1, 'jhon');
			$sth->bindValue(2, 'dowh');
			$sth->bindValue(3, 'jhon@pprios.com');
			$sth->bindValue(4, $this->getPasswordGenerated());
			$sth->bindValue(5, 1, \PDO::PARAM_INT);
			$sth->bindValue(6, \Faker\Provider\Base::numerify('########'));
			$sth->bindValue(7, 1, \PDO::PARAM_INT);
			$sth->execute();

			// inserts data random (roles '2,3')
			for ($i=0; $i < 24; $i++) {
				$randonArea  = $area[\Faker\Provider\Base::numberBetween(0, count($area)-1)]['name'];
				$randonCargo  = $cargo[\Faker\Provider\Base::numberBetween(0, count($cargo)-1)];
				$dateCreated = \Faker\Provider\DateTime::dateTimeBetween('-2 days', 'now', 'America/Lima');
				$dateUpdated = \Faker\Provider\DateTime::dateTimeBetween('-1 day', 'now', 'America/Lima');

				$sth = $dbh->prepare(
					'INSERT INTO users (' .
					'firstname, lastname, username, password, id_rol, area, cargo, status, chat_plus, at_created, at_updated, dni)' .
					' VALUES (?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?, ?)'
				);
				
				$sth->bindValue(1, $faker->firstname, \PDO::PARAM_INT);
				$sth->bindValue(2, $faker->lastname, \PDO::PARAM_STR);
				$sth->bindValue(3, $faker->email, \PDO::PARAM_STR);
				$sth->bindValue(4, $this->getPasswordGenerated(), \PDO::PARAM_STR);
				$sth->bindValue(5, \Faker\Provider\Base::numberBetween(2, 3), \PDO::PARAM_INT); // chooice rol

				$sth->bindValue(6, $randonArea, \PDO::PARAM_STR);
				$sth->bindValue(7, $randonCargo, \PDO::PARAM_STR);
				$sth->bindValue(8, \Faker\Provider\Base::numberBetween(0, 1), \PDO::PARAM_INT);
				$sth->bindValue(9, \Faker\Provider\Base::numberBetween(0, 1), \PDO::PARAM_INT);
				$sth->bindValue(10, $dateCreated->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
				$sth->bindValue(11, $dateUpdated->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
				$sth->bindValue(12, \Faker\Provider\Base::numerify('########'));
				$sth->execute();
			}
		}

		// insert groups
		$sth = $dbh->prepare('SELECT id_group FROM groups');
		$sth->execute();
		$rsDat = $sth->fetchAll();
		if (is_array($rsDat) && count($rsDat) == 0) {
			
			$thearea = $this->arrayGroup;
			foreach ($thearea as $key => $value) {
				$dateCreated = \Faker\Provider\DateTime::dateTimeBetween('-2 days', 'now', 'America/Lima');
				$sth = $dbh->prepare('INSERT INTO groups ( name, status, at_created) VALUES (?, ?, ?)');
				$sth->execute(
					array(
						$value['name'],
						$value['status'],
						$dateCreated->format('Y-m-d H:i:s')
					)
				);
			}
		}

		// fill groups users
		$this->fillGroupUsers();

		echo "data faker generated!.<br/>";

		return $response->withJson(array());
	}

	private function fillGroupUsers() {

		$dbh = $this->pdo;
		$sth = $dbh->prepare('SELECT id_group as id FROM groups WHERE status = 1');
		$sth->execute();
		$rsDat = $sth->fetchAll();
		$dataIdsGroup = array();
		// fill ids
		foreach ($rsDat as $key => $value) {
			$dataIdsGroup[] = $value['id'];
		}


		$sth = $dbh->prepare('SELECT id_user as id FROM users');
		$sth->execute();
		$rsDat = $sth->fetchAll();
		$dataIdsUser = array();
		// fill ids
		foreach ($rsDat as $key => $value) {
			$dataIdsUser[] = $value['id'];
		}


		if (
			is_array($dataIdsGroup) && count($dataIdsGroup) > 0 &&
			is_array($dataIdsUser) && count($dataIdsUser) > 0
		) {

			// each Groups
			foreach ($dataIdsGroup as $keyGroup => $valueGroup) {
				$getNumberLimit = (count($dataIdsUser) > 10) ? 12 : count($dataIdsUser);
				$dataIndexUserRamdon = \Faker\Provider\Base::randomElements($dataIdsUser , $getNumberLimit);

				foreach ($dataIndexUserRamdon as $keyUser => $valueUser) {

					$sth = $dbh->prepare('INSERT INTO groups_users ( id_group, id_user, at_created) VALUES (?, ?, ?)');
					$sth->bindValue(1, $valueGroup, \PDO::PARAM_INT);
					$sth->bindValue(2, $valueUser, \PDO::PARAM_INT);
					$sth->bindValue(3, $this->dateCreated->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
					$sth->execute();
				}
			}
		}
	}

}
