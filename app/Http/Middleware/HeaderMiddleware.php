<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use App\Libraries\ResponseLibrary;
use Illuminate\Http\Request;

class HeaderMiddleware {
    private ResponseLibrary $response;

    public function __construct() {
		$this->response = new ResponseLibrary();
    }

	function handle(Request $request, Closure $next){
        if($request->header('X-Username') == null){
            $response = response()->json([
                "response_code" => "HE",
                "response_desc" => 'Header Tidak Lengkap',
                "action" => ''
            ]);
            Log::info('[REQUEST]'.json_encode($request->all()));
            return $response;
        }
        return $next($request);
	}
}