<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class RequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        $validator = Validator::make($request->all(), ['request_data' => 'required']);
        if ($validator->fails()) {
                $response =  response()->json([
                    "response_code" => "PNC",
                    "response_desc" => 'Format Request salah',
                    "action" => ''
                ]);
                Log::info('[REQUEST]' . json_encode($request->all()));
                return $response;
        }
        return $next($request);
    }

}