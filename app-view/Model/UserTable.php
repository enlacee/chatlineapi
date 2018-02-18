<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Sql\Select;

class UserTable 
{
	protected $tableGateWay;

	protected $fields = array(
		'id_user', 'firstname', 'lastname', 'username', 'area', 'cargo', 'status',
		'chat_plus', 'at_created', 'at_updated', 'id_rol'
	);

	public function __construct(TableGateway $tableGateWay) {

		$this->tableGateWay = $tableGateWay;
	}

	public function fetchAll()
	{
		$resultSet = $this->tableGateWay->select(array('status' => 1));

		return $resultSet->toArray();
	}

	public function login($username, $password, $isAdmin = false)
	{
		// $rs = $this->tableGateWay->select(array(
		// 	'username' => $username,
		// 	'password' => $password
		// ));
		
		$fields = $this->fields;
		$rs = $this->tableGateWay->select(function (Select $select) use ($username, $password, $fields, $isAdmin) {
			$select->columns($fields);
			$select->where(array('username' => $username));
			$select->where(array('password' => $password));

			if ($isAdmin === true) {
				$select->where(array('id_rol' => array(1, 2)));
			}

			$select->limit(1);
			// echo $select->getSqlString();exit;
		});

		return $rs->current();
	}

	public function getUser($id)
	{	
		$rs = false;
		$id = (int) $id;
		$fields = $this->fields;

		$row = $this->tableGateWay->select(function (Select $select) use ($id, $fields) {
			$select->columns($fields);
			$select->where(array('id_user' => $id));
			$select->limit(1);
		});

		if ($row) {
			$rs = $row->toArray();
		}

		return $rs;
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
			if ($this->getUser($id)) {
				$dataPersona['at_updated'] = date('Y-m-d H:i:s');
				$rs = $this->tableGateWay->update($dataPersona, array('id_user' => $id));
			}
		}

		return $rs;
	}
	
	public function delete($id)
	{
		return $this->tableGateWay->delete(array('id_user' => (int) $id));
	}

}
