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
}