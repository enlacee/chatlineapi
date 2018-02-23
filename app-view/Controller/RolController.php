<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;

use AppView\Model\RolTable;

class RolController
{
	private $adapter;
	private $table;

	public function __construct($container)
	{
		$this->adapter = $container->get('adapter');
		$this->table = new RolTable(new TableGateway(TABLE_ROLES, $this->adapter));

	}

	/**
	 * Get all uses
	 */
	public function getAll($request, $response, $args)
	{
		$adapter = $this->adapter;
		$data = $this->table->fetchAll();

		return $response->withJson($data);
	}

}