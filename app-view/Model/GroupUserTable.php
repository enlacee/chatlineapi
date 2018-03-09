<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Sql\Select;

class GroupUserTable 
{
	protected $tableGateWay;
	protected $id = 'id_group_user';

	public function __construct(TableGateway $tableGateWay) {

		$this->tableGateWay = $tableGateWay;
	}

	public function fetchAll($params)
	{
		$where = array_merge(array(), $params);
		$rs = $this->tableGateWay->select(function (Select $select) use ($where) {
			$select->where($where);
			$select->join(TABLE_USERS, 'users.id_user = groups_users.id_user', array('firstname', 'lastname'), 'left');
		});

		return $rs->toArray();
	}

	// lista de groups por idUSER 
	public function fetchAllv2($params)
	{
		$where = array_merge(array(), $params);
		$rs = $this->tableGateWay->select(function (Select $select) use ($where) {
			// $select->where($where);
			$select->join(TABLE_USERS, 'users.id_user = groups_users.id_user', array('firstname', 'lastname'), 'left');
			$select->join(TABLE_GROUPS, 'groups_users.id_group = groups.id_group', array('name'), 'left');
			if (isset($where['id_user']) === true) {
				$select->where(array('users.id_user = ?' => $where['id_user'])); 
			}

			// echo $select->getSqlString();exit;
		});

		return $rs->toArray();
	}

	public function getById($id)
	{
		$rs = $this->tableGateWay->select(array($this->id => $id));

		return $rs->current();
	}

	/**
	 * Update or Insert user
	 * @return bool | int
	 */
	public function save(array $data)
	{	
		$rs = false;
		$id = isset($data[$this->id]) ? $data[$this->id] : false; 
		unset($data[$this->id]);
		
		if ($id === false) {
			$data['at_created'] = date('Y-m-d H:i:s');
			$rs = $this->tableGateWay->insert($data);

		} else {
			if ($this->getById($id)) {
				$data['at_updated'] = date('Y-m-d H:i:s');
				$rs = $this->tableGateWay->update($data, array($this->id => $id));
			}
		}

		return $rs;
	}

	public function delete($id)
	{	$rs = false;
		$rs = $this->tableGateWay->delete(array($this->id => (int) $id));
		
		return $rs;
	}

}