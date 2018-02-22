<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;

use AppView\Model\GroupTable;

class GroupController
{
	private $adapter;
	private $tableGateway;

	public function __construct($container)
	{
		$this->adapter = $container->get('adapter');
		$this->tableGateway = new GroupTable(new TableGateway(TABLE_GROUPS, $this->adapter));
	}

	private function getParamGET($request, $inputsAllowed){
		$data = array();
		$inputs = $inputsAllowed; // array('id_group', 'name');
		
		if (is_array($inputs) && count($request) > 0) {
			foreach ($inputs as $key => $value) {
				if ($request->getParam($value)) {
					$data[$value] = $request->getParam($value);
				}
			}
		}

		return $data;
	}

	/**
	 * Get all
	 */
	public function getAll($request, $response, $args)
	{
		$params = $this->getParamGET($request, array('name'));

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

		$data = array();
		$inputs = array('id_group', 'name');
		foreach ($inputs as $key => $value) {
			if ($request->getParam($value)) {
				$data[$value] = $request->getParam($value);
			}
		}

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
