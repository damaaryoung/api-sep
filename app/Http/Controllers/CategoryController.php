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
use App\Models\TblCategories;

class CategoryController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected ResponseLibrary $response;
    protected RequestLibrary $request_param;
    protected TblCategories $tbl_categories;

    public function __construct()
    {
        $this->response = new ResponseLibrary();
        $this->request_param = new RequestLibrary();
		$this->tbl_categories = new TblCategories();
    }

    public function insertCategory(Request $request)
    {
        $rules = [
            "name" => "required",
            "description" => "required",
            "f1" => "nullable",
            "f2" => "nullable",
            "f3" => "nullable",
            "f4" => "nullable",
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "insert_category");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA));
        $username = $request->header('X-Username');

        $insert_data = $this->tbl_categories->insertData($param, $username);
        if (!$insert_data) {
			return $this->response->format_response(Constant::RC_DB_ERROR, "Gagal Insert Data", "Store Categories");
		}
        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Store Categories");
    }

    public function showAll(Request $request){
        $rules = [
            "search_data" => "nullable"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "show_category");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        $allData = $this->tbl_categories->getAllData($param);
        if($allData == false){
            return $this->response->format_response(Constant::RC_DATA_NOT_FOUND, Constant::DESC_DATA_NOT_FOUND, "Search Category");
        }
        // dd($allData);

        $mappedData = $allData->map(function ($item) {
            return [
                'id'          => $item->id,
                'name'        => $item->name,
                'description' => $item->description,
                'extra'       => [
                    'f1' => $item->f1,
                    'f2' => $item->f2,
                    'f3' => $item->f3,
                    'f4' => $item->f4,
                ]
            ];
        });
        $response = [
            'data' => $mappedData
        ];
        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Search Category", $response);
    }

    public function show(Request $request){
        $rules = [
            "search_data" => "required"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "show_category");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        $search_data = $this->tbl_categories->getDataSearch($param);
        if($search_data == false){
            return $this->response->format_response(Constant::RC_DATA_NOT_FOUND, Constant::DESC_DATA_NOT_FOUND, "Search Category");
        }
        $mappedData = collect($search_data->items())->map(function ($item) {
            return [
                'id'          => $item->id,
                'name'        => $item->name,
                'description' => $item->description,
                'extra'       => [
                    'f1' => $item->f1,
                    'f2' => $item->f2,
                    'f3' => $item->f3,
                    'f4' => $item->f4,
                ]
            ];
        });
        $response = [
            'data' => $mappedData,
            'pagination' => [
                'total'        => $search_data->total(),
                'per_page'     => $search_data->perPage(),
                'current_page' => $search_data->currentPage(),
                'last_page'    => $search_data->lastPage(),
                'from'         => $search_data->firstItem(),
                'to'           => $search_data->lastItem()
            ]
        ];
        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Search Category", $response);
        
    }

    public function update(Request $request){
        $rules = [
            "id" => "required",
            "name" => "nullable",
            "description" => "nullable",
            "f1" => "nullable",
            "f2" => "nullable",
            "f3" => "nullable",
            "f4" => "nullable"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "update_category");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        $updateData = $this->tbl_categories->updateData($param, $username);
        if (!$updateData) {
			return $this->response->format_response(Constant::RC_DB_ERROR, "Gagal Update Category", "Update Categories");
		}
        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Update Categories");
    }

    public function delete(Request $request){
        $rules = [
            "id" => "required"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "delete_category");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        $id = (int) $param->id;
        $result = $this->tbl_categories->deleteData($id);

        if ($result) {
            return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Delete Categories");
        } else {
			return $this->response->format_response(Constant::RC_DB_ERROR, "Gagal Delete Category", "Delete Categories");
        }
    }

    public function getId($id){
        if (empty($id)) {
            return false;
        }

        $getId = $this->tbl_categories->getById($id);

        if(!$getId){
            return false;
        } else {
            return $getId;
        }
    }

}
