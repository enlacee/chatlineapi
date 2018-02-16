<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;

use AppView\Model\GroupTable;

class GroupController
{
	private $adapter;
	private $pdo;

	public function __construct($container)
	{
		$this->adapter = $container->get('adapter');
		$this->pdo = $container->get('pdo');
	}

	/**
	 * Get all
	 */
	public function getAll($request, $response, $args)
	{
		$adapter = $this->adapter;
		$table = new GroupTable(new TableGateway(TABLE_GROUPS, $adapter));
		$data = $table->fetchAll();

		return $response->withJson($data);
	}

}
