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

	public function getAllData($data){
		$dataList = \DB::table($this->table)
            ->paginate(3);
            
        if ($dataList->isEmpty()) {
			return false;
        }
		return $dataList;
	}

	public function getDataSearch($data){
		$search_param     = $data->search_data;
		$by_categories    = $data->by_categories;
		$by_subcategories = $data->by_subcategories;

		$dataList = \DB::table($this->table)
			->when(!empty($search_param), function ($q) use ($search_param) {
				$q->where(function ($q2) use ($search_param) {
					$q2->where('product_name', 'LIKE', "%{$search_param}%")
					->orWhere('description', 'LIKE', "%{$search_param}%");
				});
			})
			->when(!empty($by_categories), function ($q) use ($by_categories) {
				$q->where('categories_id', $by_categories);
			})
			->when(!empty($by_subcategories), function ($q) use ($by_subcategories) {
				$q->where('sub_category_id', $by_subcategories);
			})
			->paginate(3);

		if ($dataList->isEmpty()) {
			return false;
		}

		return $dataList;
	}

	public function getDetailProduct($data){
		$id_data     = $data->id_data;

		$getData = DB::table($this->table)
            ->where('id', $id_data)
            ->first();

        if (!$getData) {
            return false;
        }
        return $getData;
	}

	public function deleteData(int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        try {
            $deleted = DB::table($this->table)
                ->where('id', $id)
                ->delete();

            return $deleted > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}