<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;

use AppView\Model\RolTable;

class RolController
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
		$adapter = $this->adapter;
		$table = new RolTable(new TableGateway(TABLE_ROLES, $adapter));
		$data = $table->fetchAll();

		return $response->withJson($data);
	}

}