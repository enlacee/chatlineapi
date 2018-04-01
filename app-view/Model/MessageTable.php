<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Sql\Select;

class MessageTable 
{
	protected $tableGateWay;

	protected $fields = array(
		'id_message', 'id_group', 'id_emisor', 'id_receptor', 'text', 'is_read', 'at_created', 'at_updated'
	);

	public function __construct(TableGateway $tableGateWay){

		$this->tableGateWay = $tableGateWay;
	}

	public function fetchAll($params = array())
	{
		$where = array_merge(array(), $params);
		$fields = $this->fields;
		$date = new \DateTime(); // current date
		$date->modify('-1 day');
		$newDate = $date->format('Y-m-d H:i:s');

		$rs = $this->tableGateWay->select(function (Select $select) use ($fields, $where, $newDate) {

			$select->columns($fields);
			$select->where("at_created > '$newDate'");
			$select->where
				->nest
				->equalTo('id_emisor', $where['id_emisor'])
				->or
				->equalTo('id_emisor', $where['id_receptor'])
				->unnest
				->nest
				->equalTo('id_receptor', $where['id_receptor'])
				->or
				->equalTo('id_receptor', $where['id_emisor']);
			// echo $select->getSqlString();exit;
		});

		return $rs->toArray();
	}

	/**
	 * Update or Insert user
	 * @return bool | int
	 */
	public function save(array $data)
	{	
		$rs = false;
		$id = isset($data['id_message']) ? $data['id_message'] : false; 
		unset($data['id_message']);
		
		if ($id === false) {
			$data['at_created'] = date('Y-m-d H:i:s');
			$rs = $this->tableGateWay->insert($data); //$this->tableGateWay->getLastInsertValue();
		}

		return $rs;
	}
}