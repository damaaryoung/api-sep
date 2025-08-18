<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TblCategories extends Model
{
    protected $table = 'categories';
    protected $connection = 'db_sep';

    protected $columns = [
		'name',
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
            'description' => $data->description,
            'f1'          => $data->f1,
            'f2'          => $data->f2,
            'f3'          => $data->f3,
            'f4'          => $data->f4,
            'created_by'  => $username,
            'updated_by'  => "NULL",
            'created_at'  => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'  => NULL
        ];
        
        // dd($dataCategories);

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
            
        if  ($dataList->isEmpty()) {
		    return false;
        }
	    return $dataList;
    }
}