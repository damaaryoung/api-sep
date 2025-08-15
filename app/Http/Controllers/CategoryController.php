<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Constant;
use App\Libraries\ResponseLibrary;
use App\Libraries\RequestLibrary;

class CategoryController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected ResponseLibrary $response;
    protected RequestLibrary $request_param;

    public function __construct()
    {
        $this->response = new ResponseLibrary();
        $this->request_param = new RequestLibrary();
    }

    public function index(Request $request)
    {
        // Example of fetching categories from the database
        $categories = DB::table('categories')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function show($id)
    {
        // Example of fetching a single category by ID
        $category = DB::table('categories')->find(1);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Category not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function searchCategory(Request $request){
        $rules = [
            "search_data" => "required"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "get_merchant_existing");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        dd($param);
    }
}
