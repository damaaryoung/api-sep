<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TblProducts extends Model
{
    protected $table = 'products';

    protected $columns = [
		'product_name',
		'description',
        'product_img',
        'specification',
        'specification_details',
        'categories_id',
        'sub_category_id',
		'f1',
		'f2',
		'f3',
		'f4',
		'created_by',
		'updated_by',
		'created_at',
		'updated_at'
	];

	public function insertData($data, $username, $productImg, $productDetails){
		$dataProducts = [
            'product_name'          => $data->product_name,
            'description'           => $data->description,
			'product_img'           => $productImg,
			'specification'         => $data->specification,
			'specification_details' => $productDetails,
			'categories_id'         => $data->categories_id,
			'sub_category_id'       =>  $data->sub_category_id,
            'f1'          			=> $data->f1,
            'f2'          			=> $data->f2,
            'f3'          			=> $data->f3,
            'f4'          			=> $data->f4,
            'created_by'  			=> $username,
            'updated_by'  			=> "NULL",
            'created_at'  			=> Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'  			=> NULL
        ];

		$save = DB::table($this->table)->insert($dataProducts);
        if ($save) {
			return true;
		}
		return false;
	}
}