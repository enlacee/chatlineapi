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
		
		if (is_array($inputsAllowed) && count($request) > 0) {
			foreach ($inputsAllowed as $key => $value) {
				if ($request->getParam($value)) {
					$data[$value] = $request->getParam($value);
				}
			}
		}

		return $data;
	}

}