<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;

use AppView\Model\GroupTable;

class GroupController extends BaseController
{
	private $adapter;
	private $tableGateway;

	public function __construct($container)
	{
		$this->adapter = $container->get('adapter');
		$this->tableGateway = new GroupTable(new TableGateway(TABLE_GROUPS, $this->adapter));

	}

	/**
	 * Get all
	 */
	public function getAll($request, $response, $args)
	{
		$params = $this->getParamGET($request, array('name')); // params allowed

		$data = $this->tableGateway->fetchAll($params);

		return $response->withJson($data);
	}

	/**
	 * @return json
	 **/
	public function getById($request, $response, $args)
	{
		$rs = false;
		$id = $args['id'];
		
		if ($id) {
			$rs = $this->tableGateway->getById($id);
		}

		return $response->withJson($rs);
	}

	/**
	 * @return bool | 
	 **/
	private function _postPut($request, $response, $args)
	{
		$rs = false;
		$data = $this->getParamGET($request, array('id_group', 'name')); // params allowed

		// if exist param id (REST PUT) 
		if (isset($args['id']) === true) {
			$data['id_group'] = $args['id'];
		}

		$rs =  $this->tableGateway->save($data);

		return $rs;
	}

	/**
	 * @return json
	 **/
	public function post($request, $response, $args)
	{
		$rs = $this->_postPut($request, $response, $args);

		return $response->withJson($rs);
	}

	/**
	 * @return json
	 **/
	public function put($request, $response, $args)
	{
		$rs = $this->_postPut($request, $response, $args);

		return $response->withJson($rs);
	}

	/**
	 * @return json
	 **/
	public function delete($request, $response, $args)
	{
		$rs = false;

		if (isset($args['id']) === true) { 
			$rs = $this->tableGateway->delete($args['id']);
		}

		return $response->withJson($rs);
	}

}
