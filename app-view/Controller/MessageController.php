<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;
use \Psr\Container\ContainerInterface;

use AppView\Model\MessageTable;
use AppView\Controller\BaseController;

class MessageController extends BaseController
{
	private $adapter;
	private $tableGateway;

	public function __construct(ContainerInterface $container)
	{
		$this->adapter = $container->get('adapter');
		$this->tableGateway = new MessageTable(new TableGateway(TABLE_MESSAGES, $this->adapter));
	}

	/**
	 * Get all uses
	 */
	public function getAll($request, $response, $args)
	{
		// $params = $request->getParams();
		// $params = $this->getParamGET($request, array('id_message', 'id_group', 'id_emisor', 'id_receptor', 'at_created'));

		$id_group = $request->getParam('id_group', false);

		if ($id_group !== false) {
			$data = $this->tableGateway->fetchAllGroup($id_group);
		} else {
			$params['id_emisor'] = $request->getParam('emisor');
			$params['id_receptor'] = $request->getParam('receptor');
			$data = $this->tableGateway->fetchAll($params);
		}

		return $response->withJson($data);
	}

	/**
	 * @return json
	 **/
	public function post($request, $response, $args)
	{
		$rs = $this->_postPut($request, $response, $args);

		return $response->withJson($rs);
	}

	/**
	 * @return int record affected
	 **/
	private function _postPut($request, $response, $args)
	{
		$rs = false;
		// $data = $this->getParamGET($request, array('id_group', 'id_emisor', 'id_receptor')); // params allowed
		$data = $request->getParams();
		// if exist param id (REST PUT) 
		// if (isset($args['id']) === true) {
		//	$data['id_message'] = $args['id'];
		// }

		$formatData['id_emisor'] = $data['emisor'];
		$formatData['id_receptor'] = $data['receptor'];
		$formatData['id_group'] = $data['id_group'];
		$formatData['text'] = $data['message'];

		// format data for group
		if ($data['chatType'] === 'group') {
			unset($formatData['id_receptor']);
		} else if ($data['chatType'] === 'user') {
			unset($formatData['id_group']);
		}

		$rs = $this->tableGateway->save($formatData);

		return $rs;
	}

	// post
	/**
	 * @return json
	 **/
	public function uploadFile($request, $response, $args)
	{
		$rs = false;
		$uploadedFiles = $request->getUploadedFiles();

		if ( !isset($_FILES['uploads']) ) {
			// echo "No files uploaded!!";
			return $response->withJson($rs);
		}

		$imgs = array();

		$files = $_FILES['uploads'];
		$cnt = count($files['name']);

		for($i = 0 ; $i < $cnt ; $i++) {
			if ($files['error'][$i] === 0) {
				$name = uniqid('img-'.date('Ymd').'-');
				if (move_uploaded_file($files['tmp_name'][$i], 'uploads/' . $name) === true) {
					$imgs[] = array('url' => '/uploads/' . $name, 'name' => $files['name'][$i]);
				}

			}
		}

		// $imageCount = count($imgs);

		// if ($imageCount == 0) {
		// 	echo 'No files uploaded!!  <p><a href="/">Try again</a>';
		// 	return;
		// }

		// $plural = ($imageCount == 1) ? '' : 's';

		// foreach($imgs as $img) {
		// 	printf('%s <img src="%s" width="50" height="50" /><br/>', $img['name'], $img['url']);
		// }

		return $response->withJson($imgs);
	}

}
