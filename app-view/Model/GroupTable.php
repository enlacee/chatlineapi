<?php

namespace AppView\Model;

use \Zend\Db\TableGateway\TableGateway;

class GroupTable 
{
	protected $tableGateWay;
	protected $id = 'id_group';

	public function __construct(TableGateway $tableGateWay) {

		$this->tableGateWay = $tableGateWay;
	}

	public function fetchAll()
	{
		$rs = $this->tableGateWay->select();

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