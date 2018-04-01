<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Sql\Select;

use \Zend\ServiceManager\ServiceLocatorInterface;
use \Zend\Db\TableGateway\AbstractTableGateway;
use \Zend\Db\ResultSet\ResultSet;
use \Zend\Db\Adapter\Adapter;

class GroupUserTable extends AbstractTableGateway
{
	protected $tableGateWay;
	protected $adapter;

	protected $id = 'id_group_user';
	protected $fields = array(
		'id_group_user', 'id_group', 'id_user', 'at_created', 'at_updated');

	public function __construct(TableGateway $tableGateWay, $adapter) {

		$this->tableGateWay = $tableGateWay;
		$this->adapter = $adapter;
	}

	/**
	 * listar usuarios por grupo para administrador
	 */
	public function fetchAll($params)
	{
		$where = array_merge(array(), $params);
		$rs = $this->tableGateWay->select(function (Select $select) use ($where) {
			$select->join(TABLE_USERS, 'users.id_user = groups_users.id_user', array('firstname', 'lastname'), 'left');
			$select->where($where);
		});

		return $rs->toArray();
	}

	/**
	 * listar los usuarios con el contador de mensajes de las ultimas 24 horas
	 * @param array $params
	 * @param int $idUserExcept idUser
	 *
	 * @return array
	 */
	public function fetchAllv3($params, $idUserExcept)
	{
		$_id_user = $idUserExcept;
		$_id_group = $params['id_group'];
		$resultSet = $this->adapter->query(
			"
			SELECT 
				groups_users.id_group_user AS id_group_user,
				groups_users.id_group AS id_group,
				groups_users.id_user AS id_user,
				groups_users.at_created AS at_created,
				groups_users.at_updated AS at_updated,
				users.firstname AS firstname,
				users.lastname AS lastname,
				(
					SELECT count(messages.text) FROM messages
						WHERE
							-- at_created > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
							at_created > NOW() - INTERVAL 1 DAY
							AND (id_emisor = users.id_user AND id_receptor = $_id_user)
							OR id_emisor = $_id_user AND id_receptor = users.id_user
							limit 1
				) as counter_messages
			FROM
				groups_users
					LEFT JOIN
				users ON users.id_user = groups_users.id_user
			WHERE
				id_group = ? AND users.id_user != $_id_user;
			",
			array($_id_group)
		)->toArray();

		return $resultSet;
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