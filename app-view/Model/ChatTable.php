<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;
// use \Zend\Db\TableGateway\TableGatewayInterface;
use \Zend\Db\Sql\Select;

class ChatTable
{

	private $tableGateway;

	public function __construct(TableGateway $tableGateWay) {
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll($params)
	{
		// return $this->tableGateway->select($params);
		$where = array_merge(array(), $params);
		$rs = $this->tableGateWay->select(function (Select $select) use ($where) {
			$select->where($where);
		});

		return $rs->toArray();
	}

	// public function getAlbum($id)
	// {
	// 	$id = (int) $id;
	// 	$rowset = $this->tableGateway->select(['id' => $id]);
	// 	$row = $rowset->current();
	// 	if (! $row) {
	// 		throw new RuntimeException(sprintf(
	// 			'Could not find row with identifier %d',
	// 			$id
	// 		));
	// 	}

	// 	return $row;
	// }
}
