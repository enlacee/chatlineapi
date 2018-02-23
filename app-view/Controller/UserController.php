<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;

use AppView\Model\UserTable;

class UserController extends BaseController
{
	private $adapter;
	private $pdo;
	private $table;

	public function __construct($container)
	{
		$this->pdo = $container->get('pdo');
		$this->adapter = $container->get('adapter');
		$this->table = new UserTable(new TableGateway(TABLE_USERS, $this->adapter));
	}

	/**
	 * Get all uses
	 */
	public function getAll($request, $response, $args)
	{
		$params = $this->getParamGET($request, array('firstname', 'lastname', 'username', 'dni'));

		$data = $this->table->fetchAll($params);

		return $response->withJson($data);
	}

	public function getById($request, $response, $args)
	{
		$rs = false;
		$id = $args['id'];
		
		if ($id) {
			$rs = $this->table->getById($id);
		}

		return $response->withJson($rs);
	}

	public function login($request, $response, $args)
	{
		$rs = false;

		$adapter = $this->adapter;
		// var_dump($request->getParams());exit;
		$username = $request->getParam('username');
		$password = $request->getParam('password');

		if (empty($username) && empty($password)) {
			return $rs;
		}

		if ($request->getParam('is-admin')) {
			$rs = $this->table->login($username, $password, true);
		} else {
			$rs = $this->table->login($username, $password);
		}

		return $response->withJson($rs);
	}

	public function post($request, $response, $args)
	{
		$rs = $this->_postPut($request, $response, $args);

		return $response->withJson($rs);
	}

	public function put($request, $response, $args)
	{
		$rs = $this->_postPut($request, $response, $args);

		return $response->withJson($rs);
	}

	public function delete($request, $response, $args)
	{
		$rs = false;
		$id = $request->getParam('id_user');

		if ($id) {
			$rs = $this->table->delete($id);
		}

		return $response->withJson($rs);
	}

	private function _postPut($request, $response, $args)
	{
		$rs = false;
		$inputsAllowed = array(
			'id_user', 'firstname', 'lastname', 'username', 'password', 'dni',
			'area', 'cargo', 'status', 'chat_plus', 'id_rol'
		);
		$data = $this->getParamGET($request, $inputsAllowed);

		// exeption POST (auto fill data)
		if ($request->isPost() === true) {
			if (empty($data['id_rol'])) {
				$data['id_rol'] = 3;
			}

			if (empty($data['status'])) {
				$data['status'] = 0;
			}
		}

		// if exist param id (REST PUT) 
		if (isset($args['id']) === true) {
			$data['id_user'] = $args['id'];
		}

		$rs = $this->table->save($data);

		return $rs;
	}
}
