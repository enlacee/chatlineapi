<?php

namespace AppView\Model;

class UserTable implements \Zend\Db\TableGateway\TableGatewayInterface
{
	protected $db;

	public function __contruct($db)
	{
		$this->db = $db;
	}

	public function get($id)
	{
		$rs = $this->db->prepare('sql', $id);

		return $rs;
	}

	public function getTable(){

	}
	public function select($where = null){

	}
	public function insert($set){

	}
	public function update($set, $where = null){

	}
	public function delete($where){

	}

}
