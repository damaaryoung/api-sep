<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TblSubCategory extends Model
{
    protected $table = 'sub_category';

    protected $columns = [
		'name',
        'categories_id',
		'description',
		'f1',
		'f2',
		'f3',
		'f4',
		'created_by',
		'updated_by',
		'created_at',
		'updated_at'
	];

    public function insertData($data, $username){
        $dataCategories = [
            'name'        => $data->name,
            'categories_id' => $data->categories_id,
            'description'   => $data->description,
            'f1'            => $data->f1,
            'f2'            => $data->f2,
            'f3'            => $data->f3,
            'f4'            => $data->f4,
            'created_by'    => $username,
            'updated_by'    => "NULL",
            'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'    => NULL
        ];

        $save = DB::table($this->table)->insert($dataCategories);
        if ($save) {
			return true;
		}
		return false;
    } 

    public function getDataSearch($data){
        $search_param = $data->search_data;
            
        $dataList = \DB::table($this->table)
            ->where(function ($q) use ($search_param) {
                $q->where('name', 'LIKE' , "%{$search_param}%")
                ->orWhere('description', 'LIKE', "%{$search_param}%");
            })
            ->paginate(10);
            
        if ($dataList->isEmpty()) {
		    return false;
        }
	    return $dataList;
    }

    public function updateData($data, string $username): bool {
        if (empty($data->id)) {
            return false;
        }
        $id = (int) $data->id;
        $payload = [
            'name'          => $data->name,
            'categories_id' => $data->categories_id,
            'description'   => $data->description,
            'f1'            => $data->f1,
            'f2'            => $data->f2,
            'f3'            => $data->f3,
            'f4'            => $data->f4,
            'updated_by'    => $username,
            'updated_at'    => Carbon::now()->format('Y-m-d H:i:s')
        ];
        try {
            $affected = DB::table($this->table)
                ->where('id', $id)
                ->update($payload);
            return $affected > 0;
        } catch (\Throwable $e) {
            return false;
        }
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
    
    public function getById(int $id)
    {
        $getData = DB::table($this->table)
            ->where('id', $id)
            ->first();

        if (!$getData) {
            return false;
        }
        return $getData;
    }
}