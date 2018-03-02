<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Sql\Select;

class UserTable 
{
	protected $tableGateWay;

	protected $fields = array(
		'id_user', 'firstname', 'lastname', 'username', 'password', 'dni', 'area', 'cargo', 'status',
		'chat_plus', 'at_created', 'at_updated', 'id_rol'
	);

	public function __construct(TableGateway $tableGateWay) {

		$this->tableGateWay = $tableGateWay;
	}

	public function fetchAll($params)
	{
		$where = array_merge(array(), $params);
		// $rs = $this->tableGateWay->select($where);
		$fields = $this->fields;
		$rs = $this->tableGateWay->select(function (Select $select) use ($where, $fields) {
			$select->columns($fields);
			$select->where(array('id_rol != ?' => 1)); // no filter to SUPERADMINISTRADOR
			$select->where($where);
		});

		return $rs->toArray();
	}

	/**
	 * Get only users with status = 1 (enable)
	 */
	public function login($username, $password, $isAdmin = false)
	{
		// $rs = $this->tableGateWay->select(array(
		// 	'username' => $username,
		// 	'password' => $password
		// ));
		$rs = false;
		$fields = $this->fields;
		$resultSet = $this->tableGateWay->select(function (Select $select) use ($username, $password, $fields, $isAdmin) {
			$select->columns($fields);
			$select->where(array('status' => 1));
			$select->where(array('username' => $username));
			$select->where(array('password' => $password));

			if ($isAdmin === true) {
				$select->where( array( 'id_rol' => array( 1, 2 ) ) ); // Access to SuperAdmin and Admin
			} else if ($isAdmin === false) {
				$select->where( array( 'id_rol' => array( 2, 3 ) ) ); // Access to Admin and User
			}

			// $select->limit(1);  
			// echo $select->getSqlString();exit;
		});

		if (!is_null($resultSet->current())) {
			$rs = $resultSet->current();
		}

		return $rs;
	}

	public function getById($id)
	{	
		$id = (int) $id;
		$fields = $this->fields;

		$row = $this->tableGateWay->select(function (Select $select) use ($id, $fields) {
			$select->columns($fields);
			$select->where(array('id_rol != ?' => 1)); // no filter to SUPERADMINISTRADOR
			$select->where(array('id_user' => $id));
			$select->limit(1);
		});

		return $row->current();
	}

	public function getByUserName($username)
	{	

		$fields = $this->fields;
		$row = $this->tableGateWay->select(function (Select $select) use ($username, $fields) {
			$select->columns($fields);
			$select->where(array('username ' => $username));
			$select->limit(1);
		});

		return $row->current();
	}

	/**
	 * Update or Insert user
	 * @return bool | int
	 */
	public function save(array $dataPersona)
	{	
		$rs = false;
		$id = isset($dataPersona['id_user']) ? $dataPersona['id_user'] : false; 
		unset($dataPersona['id_user']);
		
		if ($id === false) {
			$dataPersona['at_created'] = date('Y-m-d H:i:s');
			$rs = $this->tableGateWay->insert($dataPersona); //$this->tableGateWay->getLastInsertValue();

		} else {
			if ($this->getById($id)) {
				$dataPersona['at_updated'] = date('Y-m-d H:i:s');
				$rs = $this->tableGateWay->update($dataPersona, array('id_user' => $id));
			}
		}

		return $rs;
	}
	
	public function delete($id)
	{
		return $this->tableGateWay->delete(array('id_user' => (int)$id));
	}

}
