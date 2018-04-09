<?php

namespace AppView\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\TableGateway\TableGateway;
use \Psr\Container\ContainerInterface;

use AppView\Model\UserTable;
use AppView\Controller\BaseController;

class UserController extends BaseController
{
	private $adapter;
	private $pdo;
	private $table;

	public function __construct(ContainerInterface $container)
	{
		$this->pdo = $container->get('pdo');
		$this->adapter = $container->get('adapter');
		$this->table = new UserTable(new TableGateway(TABLE_USERS, $this->adapter));
	}

	/**
	 * Get all uses
	 */
	public function getAll($request, $response, $args)
	{
		$params = $this->getParamGET($request, array('firstname', 'lastname', 'username', 'dni'));

		$data = $this->table->fetchAll($params);

		return $response->withJson($data);
	}

	/**
	 * Get all uses diccionary
	 */
	public function getUserDiccionary($request, $response, $args)
	{
		$data = array();
		$users = $this->table->getUserDiccionary();

		foreach ($users as $key => $user) {
			$data["{$user['id_user']}"] = array(
				"firstname" => $user['firstname'],
				"lastname" => $user['lastname'],
			);
		}

		return $response->withJson($data);
	}

	public function getById($request, $response, $args)
	{
		$rs = false;
		$id = $args['id'];
		
		if ($id) {
			$rs = $this->table->getById($id);
		}

		return $response->withJson($rs);
	}

	public function login($request, $response, $args)
	{
		$rs = false;

		$adapter = $this->adapter;

		$username = $request->getParam('username');
		$password = $request->getParam('password');

		if (empty($username) && empty($password)) {
			return $rs;
		}

		// Check crypted password
		$passwordCrypt = $this->loginCryptValidation($username, $password);
		if ($passwordCrypt !== false && is_string($passwordCrypt) === true) {
			if ($request->getParam('is-admin', false) !== false) {
				$rs = $this->table->login($username, $passwordCrypt, true);
			} else {
				$rs = $this->table->login($username, $passwordCrypt);
			}
		}

		return $response->withJson($rs);
	}

	/**
	 * @ref http://php.net/manual/en/function.password-hash.php
	 * crypt validation
	 *
	 * @return string password encrypted
	 */
	private function loginCryptValidation($username, $password) {
		$rs = false;
		$user = $this->table->getByUserName($username);

		if (!empty($user)) {
			if (password_verify($password, $user->password)) {
				$rs = $user->password; // echo 'Password is valid!';
			}
		}

		return $rs;
	}

	public function post($request, $response, $args)
	{
		$rs = $this->_postPut($request, $response, $args);

		return $response->withJson($rs);
	}

	public function put($request, $response, $args)
	{
		$rs = $this->_postPut($request, $response, $args);

		return $response->withJson($rs);
	}

	public function delete($request, $response, $args)
	{
		$rs = false;

		if (isset($args['id']) === true) {
			
			// no allowed delete superadministrator
			if ($args['id'] == 1) {
				$rs = false;
			} else {
				$rs = $this->table->delete($args['id']);
			}
		}

		return $response->withJson($rs);
	}

	private function _postPut($request, $response, $args)
	{
		$rs = false;
		$inputsAllowed = array(
			'id_user', 'firstname', 'lastname', 'username', 'password', 'dni',
			'area', 'cargo', 'status', 'chat_plus', 'id_rol'
		);
		$data = $this->getParamGET($request, $inputsAllowed);

		// exeption POST (auto fill data)
		if ($request->isPost() === true) {
			if (empty($data['id_rol'])) {
				$data['id_rol'] = 3;
			}

			if (empty($data['status'])) {
				$data['status'] = 0;
			}

			// reset hash password (no exists user)
			if (!empty($data['password'])) {
				$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
			}
		}

		// if exist param id (REST PUT) 
		if (isset($args['id']) === true) {
			$data['id_user'] = $args['id'];

			// Check crypted password
			if (isset($data['username']) && isset($data['password'])) {
				$passwordCrypt = $this->loginCryptValidation($data['username'], $data['password']);
				if ($passwordCrypt !== false) {
					unset($data['password']); // no save password (curent is iqual)
				} else {
					$data['password'] = $this->_encryptPassword($data['password']);
				}
			}
		}

		$rs = $this->table->save($data);

		return $rs;
	}

	/**
	 * encriptacion de clave segura 
	 * @ return string
	 */
	private function _encryptPassword($stringPassword)
	{
		return password_hash($stringPassword, PASSWORD_BCRYPT);
	}

	/**
	 * Return your current password
	 * @ return void
	 */
	public function forgotPassword($request, $response, $args)
	{

		// vars allowed
		$inputsAllowed = array('email');
		$paramRequest = $this->getParamGET($request, $inputsAllowed);
		$userData = false;
		$rs = false;

		if (!empty($paramRequest['email']) && filter_var($paramRequest['email'], FILTER_VALIDATE_EMAIL)) {
			$resultData = $this->table->fetchAll(array('username' => $paramRequest['email']));

			// new password
			if (is_array($resultData) && count($resultData) === 1) {
				$userData = $resultData[0];
				$newPasswordReadble = $this->generateRandomString();
				$newPassword =  $this->_encryptPassword($newPasswordReadble);
				$rsUser = $this->table->save(array(
					'password'	=> $newPassword,
					'id_user'	=> $userData['id_user']
				));

				// send mail message
				if ($rsUser !== false) {
					$rs = $this->_sendMailTo($paramRequest['email'], $userData, $newPasswordReadble);
				}
				
			}
			
		}

		return $response->withJson($rs);
	}

	/**
	 * Sen mail to user by email
	 * @ return void
	 */
	private function _sendMailTo($email, $userData, $newPassword)
	{
		$rs = false;
		$strMessage = '<br/>';
		$strMessage .= 'Hola: ' . mb_strtoupper($userData['firstname']);
		$strMessage .= '<br/>';
		$strMessage .= 'tú nueva clave es : ' . $newPassword;
		$strMessage .= '<br/>';
		$strMessage .= '<br/>';

		$strHTML = <<<EOT
<html>
	<head>
		<title></title>
	</head>
	<body>
		<table>
			<tr>
				<td align="left">
					{$strMessage}
				</td>
			</tr>
		</table>
	</body>
</html>
EOT;

		try {
				$from = 'noreply@noreply.com';
				$to = $email;
				$subject = 'Recuperar clave Chat En Linea';
				$headers = "From: noreply@noreply.com" . "\r\n";
				$headers .= "Reply-To: ". strip_tags($email) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

				$message = $strHTML;
				mail($to, $subject, $message, $headers);

				$rs = true;
			} catch (Exception $e) {
				$rs = false;
			}

		return $rs;
	}

}
