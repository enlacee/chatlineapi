<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Sql\Select;

class UserTable 
{
	protected $tableGateWay;

	public function __construct(TableGateway $tableGateWay) {

		$this->tableGateWay = $tableGateWay;
	}

	public function fetchAll()
	{
		$resultSet = $this->tableGateWay->select(array('status' => 1));
		return $resultSet->toArray();
	}

	public function login($username, $password)
	{

		// $rs = $this->tableGateWay->select(array(
		// 	'username' => $username,
		// 	'password' => $password
		// ));

		$rs = $this->tableGateWay->select(function (Select $select) use ($username, $password) {
			$select->columns(array('id_user', 'firstname', 'lastname', 'username', 'at_created', 'at_updated', 'id_rol'));
			$select->where(array('username' => $username));
			$select->where(array('password' => $password));
			$select->limit(1);
			// echo $select->getSqlString();
		});

		return $rs->toArray();
	}

	public function getUser($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateWay->select(array('id_user' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}

	public function saveUser(array $dataPersona)
	{
		$id = (int) $dataPersona['id_user']; 
		unset($dataPersona['id_user']);
		
		if ($id == 0) {
			$this->tableGateway->insert($dataPersona);
		} else {
		
			if ($this->getUser($id)) {
				$this->tableGateway->update($dataPersona, array('id_user' => $id));
			} else {
				throw new \Exception('Persona id does not exist');
			}
		}
	}
	
	public function deleteUser($id)
	{
		$this->tableGateway->delete(array('id_user' => (int) $id));
	}

}
