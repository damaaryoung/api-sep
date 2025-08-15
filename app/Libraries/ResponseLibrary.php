<?php

/**
 * Format untuk Response Data
 *
 * @package           App
 * @subpackage        Libraries
 * @author            Suluh Damar Grahita
 * @copyright         PT. Sahabat Energi Persada
 * @return            json
 */

namespace App\Libraries;

class ResponseLibrary {
    public function format_response($response_code, $response_desc, $action=null, $response_data=null) {
        $response = [
			"response_code" => $response_code,
            "response_desc" => $response_desc,
            "action" 		=> $action,
			"response_data" => $response_data
			
        ];
		return $response;
	}
}