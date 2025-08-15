<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblCategories extends Model
{
    protected $table = 'categories';
    protected $connection = 'db_sep';

        public function getDataSearch($data){
            $search_param = $data['search_data'];

            $dataList = \DB::connection('db_sep')
                ->table($table)
                ->where(function ($q) use ($search_param) {
                    $q->where('name', 'LIKE' , $search_param)
                    ->orWhere('description', 'LIKE', $search_param);
                })
                ->paginate(10);
        }
}