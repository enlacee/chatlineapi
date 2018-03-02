<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;
use \Psr\Container\ContainerInterface;

use AppView\Model\ChatTable;


class UserController extends BaseController
{
	private $adapter;
	private $pdo;
	private $table;

	public function __construct(ContainerInterface $container)
	{
		$this->pdo = $container->get('pdo');
		$this->adapter = $container->get('adapter');
		$this->table = new ChatTable(new TableGateway(TABLE_MESSAGES_RECIPIENTS, $this->adapter));
	}

	/**
	 * Get all uses
	 */
	public function getAll($request, $response, $args)
	{
		// $params = $this->getParamGET($request, array('id_user'));
		$params = $request->getParams();

		$data = $this->table->fetchAll($params);

		return $response->withJson($data);
	}

}
