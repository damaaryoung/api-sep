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
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Libraries\FileUploadsLibrary;
use App\Models\TblProducts;
use Illuminate\Support\Facades\File;


class ProductsController extends BaseController {

    use AuthorizesRequests, ValidatesRequests;
    protected ResponseLibrary $response;
    protected RequestLibrary $request_param;
    protected FileUploadsLibrary $uploads;
    protected CategoryController $categoryController;
    protected SubCategoryController $subCategoryController;
    protected TblProducts $tbl_products;

    public function __construct()
    {
        $this->response = new ResponseLibrary();
        $this->request_param = new RequestLibrary();
        $this->categoryController = new CategoryController();
        $this->subCategoryController = new SubCategoryController();
        $this->uploads = new FileUploadsLibrary();
        $this->tbl_products = new TblProducts();
    }

    public function insertProduct(Request $request){
        $rules = [
            "product_name" => "required",
            "description" => "required",
            "product_img" => "required",
            "specification" => "required",
            "categories_id" => "required",
            "sub_category_id" => "required",
            "specification_details" => "required",
            "f1" => "nullable",
            "f2" => "nullable",
            "f3" => "nullable",
            "f4" => "nullable"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "insertCategory");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA));
        $username = $request->header('X-Username');
        
        // cek categori nya dulu
        $category = $this->categoryController->getId($param->categories_id);
        if(!$category){
			return $this->response->format_response(Constant::RC_DB_ERROR, "Gagal Insert Data : Category tidak ada atau sudah dihapus", "Store Products Categories");
        }
        
        //cek sub category nya dulu bos
        $subCategory = $this->subCategoryController->getId($param->sub_category_id);
        if(!$subCategory){
			return $this->response->format_response(Constant::RC_DB_ERROR, "Gagal Insert Data : Sub-Category tidak ada atau sudah dihapus", "Store Products Categories");
        }
        
        $tryUploadImg = $this->uploads->saveBase64ImageToPublicImg($param->product_img);
        if (!$tryUploadImg) {
            return $this->response->format_response(
                Constant::RC_PARAM_NOT_VALID,
                "Gagal upload gambar: " . ($this->uploads->getLastError() ?? 'unknown'),
                "insertProduct"
            );
        } else {
            $imageName = $tryUploadImg;
        }
        
        $tyUploadPDF = $this->uploads->saveBase64PdfToPublicDocuments($param->specification_details);
        if (!$tyUploadPDF) {
            return $this->response->format_response(
                Constant::RC_PARAM_NOT_VALID,
                "Gagal upload gambar: " . ($this->uploads->getLastError() ?? 'unknown'),
                "insertProduct"
            );
        } else {
            $docsName = $tyUploadPDF;
        }
        
        $insert_data = $this->tbl_products->insertData($param, $username, $imageName, $docsName);
        if (!$insert_data) {
			return $this->response->format_response(Constant::RC_DB_ERROR, "Gagal Insert Data", "Store Products");
		}
        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Store Products");
        
    }

    public function show(Request $request){
        $rules = [
            "search_data" => "nullable"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "show_products");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        $allDataProducts = $this->tbl_products->getAllData($param);
        if($allDataProducts == false){
            return $this->response->format_response(Constant::RC_DATA_NOT_FOUND, Constant::DESC_DATA_NOT_FOUND, "Search Products");
        }

        $mappedData = collect($allDataProducts->items())->map(function ($item) {
            return [
                'id'           => $item->id,
                'product_name' => $item->product_name,
                'description'  => $item->description,
                'product_img'  => env('IMG_BASE_URL') . '/' . $item->product_img, //$imgUrl = asset('img/' . $item->product_img)
                'product_docs' => env('DOCS_BASE_URL') . '/' .$item->specification_details
            ];
        });
        $response = [
            'data' => $mappedData,
            'pagination' => [
                'total'        => $allDataProducts->total(),
                'per_page'     => $allDataProducts->perPage(),
                'current_page' => $allDataProducts->currentPage(),
                'last_page'    => $allDataProducts->lastPage(),
                'from'         => $allDataProducts->firstItem(),
                'to'           => $allDataProducts->lastItem()
            ]
        ];

        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Search Category", $response);
    }

    public function searchProduct(Request $request){
        $rules = [
            "search_data" => "nullable",
            "by_categories" => "nullable",
            "by_subcategories" => "nullable"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "show_products");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        $allDataProducts = $this->tbl_products->getDataSearch($param);
        if($allDataProducts == false){
            return $this->response->format_response(Constant::RC_DATA_NOT_FOUND, Constant::DESC_DATA_NOT_FOUND, "Search Products");
        }

        $mappedData = collect($allDataProducts->items())->map(function ($item) {
            return [
                'id'           => $item->id,
                'product_name' => $item->product_name,
                'description'  => $item->description,
                'product_img'  => env('IMG_BASE_URL') . '/' . $item->product_img, //$imgUrl = asset('img/' . $item->product_img)
                'product_docs' => env('DOCS_BASE_URL') . '/' .$item->specification_details
            ];
        });
        $response = [
            'data' => $mappedData,
            'pagination' => [
                'total'        => $allDataProducts->total(),
                'per_page'     => $allDataProducts->perPage(),
                'current_page' => $allDataProducts->currentPage(),
                'last_page'    => $allDataProducts->lastPage(),
                'from'         => $allDataProducts->firstItem(),
                'to'           => $allDataProducts->lastItem()
            ]
        ];

        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Search Category", $response);
    }

    public function deleteProducts(Request $request){
        $rules = [
            "id_data" => "required"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "show_products");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');

        $getId = $this->tbl_products->getDetailProduct($param);
        if(!$getId){
            return $this->response->format_response(Constant::RC_DATA_NOT_FOUND, Constant::DESC_DATA_NOT_FOUND, "Search Products");
        } 

        $product_image = $getId->product_img;
        $spec_details = $getId->specification_details;

        $result = $this->tbl_products->deleteData($param->id_data);
        if ($result) {
            
            $img = public_path('img' . '/' . $product_image);
            if (File::exists($img)) {
                File::delete($img);
            }
            $documents = public_path('documents' . '/' . $spec_details);
            if (File::exists($documents)) {
                File::delete($documents);
            }

            return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Delete Categories");
        } else {
			return $this->response->format_response(Constant::RC_DB_ERROR, "Gagal Delete Category", "Delete Categories");
        }
    }

    public function detailProducts(Request $request){
        $rules = [
            "id_data" => "required"
        ];
        $validator = Validator::make($request->input(Constant::REQUEST_DATA), $rules);
        if ($validator->fails()) {
            return $this->response->format_response(Constant::RC_PARAM_NOT_VALID, $validator->errors()->first(), "show_products");
        }
        $param = $this->request_param->get_param($request->input(Constant::REQUEST_DATA)); 
        $username = $request->header('X-Username');
        
        $allDataProducts = $this->tbl_products->getDetailProduct($param);
        dd($allDataProducts);
        if(!$allDataProducts){
            return $this->response->format_response(Constant::RC_DATA_NOT_FOUND, Constant::DESC_DATA_NOT_FOUND, "Search Products");
        } 
        $mappedData = 
        [
                'id'              => $item->id,
                'product_name'    => $item->product_name,
                'description'     => $item->description,
                'product_img'     => env('IMG_BASE_URL') . '/' . $item->product_img,
                'product_docs'    => env('DOCS_BASE_URL') . '/' .$item->specification_details,
                'specification'   => $item->specification,  
                'categories_id'   => $item->categories_id,    
                'sub_category_id' => $item->sub_category_id,    
                'f1'          	  => $item->f1,	
                'f2'          	  => $item->f2,	
                'f3'          	  => $item->f3,	
                'f4'    		  => $item->f4
        ];
        $response = [
            'data' => $mappedData
        ];
        return $this->response->format_response(Constant::RC_SUCCESS, Constant::DESC_SUCCESS, "Detail Products", $response);
    }
}