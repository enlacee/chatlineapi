<?php

namespace AppView\Controller;

class BaseController
{
	/**
	 * @param $request object
	 * @param $inputsAllowed filter by array('id_group', 'name');
	 *
	 * @return array
	 */
	public function getParamGET($request, $inputsAllowed){
		$data = array();

		if (is_array($inputsAllowed) && count($inputsAllowed) > 0) {
			foreach ($inputsAllowed as $key => $value) {
				$theValue = $request->getParam($value, false);
				if ($theValue !== false && $theValue !== '') {
					$data[$value] = $theValue;
				}
			}
		}

		return $data;
	}

	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

}
