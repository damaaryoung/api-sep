<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Log;

class RequestLibrary {
    private $request_data = "";

    public function get_param($request){
        $this->request_data = json_encode($request);
        $param = json_decode($this->request_data);
        return $param;
    }
}