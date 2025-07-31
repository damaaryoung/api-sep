<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use DB;

class CategoryController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

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

    public function addCategory() : Returntype {
        
    }
}
