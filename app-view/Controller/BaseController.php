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

}