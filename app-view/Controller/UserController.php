<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;
use \Psr\Container\ContainerInterface;

use AppView\Model\UserTable;
use AppView\Controller\BaseController;

class UserController extends BaseController
{
	private $adapter;
	private $pdo;
	private $table;

	public function __construct(ContainerInterface $container)
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

	/**
	 * Get all uses diccionary
	 */
	public function getUserDiccionary($request, $response, $args)
	{
		$data = array();
		$users = $this->table->getUserDiccionary();

		foreach ($users as $key => $user) {
			$data["{$user['id_user']}"] = array(
				"firstname" => $user['firstname'],
				"lastname" => $user['lastname'],
			);
		}

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

		$username = $request->getParam('username');
		$password = $request->getParam('password');

		if (empty($username) && empty($password)) {
			return $rs;
		}

		// Check crypted password
		$passwordCrypt = $this->loginCryptValidation($username, $password);
		if ($passwordCrypt !== false && is_string($passwordCrypt) === true) {
			if ($request->getParam('is-admin', false) !== false) {
				$rs = $this->table->login($username, $passwordCrypt, true);
			} else {
				$rs = $this->table->login($username, $passwordCrypt);
			}
		}

		return $response->withJson($rs);
	}

	/**
	 * @ref http://php.net/manual/en/function.password-hash.php
	 * crypt validation
	 *
	 * @return string password encrypted
	 */
	private function loginCryptValidation($username, $password) {
		$rs = false;
		$user = $this->table->getByUserName($username);

		if (!empty($user)) {
			if (password_verify($password, $user->password)) {
				$rs = $user->password; // echo 'Password is valid!';
			}
		}

		return $rs;
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

		if (isset($args['id']) === true) {
			
			// no allowed delete superadministrator
			if ($args['id'] == 1) {
				$rs = false;
			} else {
				$rs = $this->table->delete($args['id']);
			}
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

			// reset hash password (no exists user)
			if (!empty($data['password'])) {
				$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
			}
		}

		// if exist param id (REST PUT) 
		if (isset($args['id']) === true) {
			$data['id_user'] = $args['id'];

			// Check crypted password
			if (isset($data['username']) && isset($data['password'])) {
				$passwordCrypt = $this->loginCryptValidation($data['username'], $data['password']);
				if ($passwordCrypt !== false) {
					unset($data['password']); // no save password (curent is iqual)
				} else {
					$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
				}
			}
		}

		$rs = $this->table->save($data);

		return $rs;
	}
}
