<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;

class GroupTable 
{
	protected $tableGateWay;

	public function __construct(TableGateway $tableGateWay) {

		$this->tableGateWay = $tableGateWay;
	}

	public function fetchAll()
	{
		$resultSet = $this->tableGateWay->select();
		return $resultSet->toArray();
	}

}