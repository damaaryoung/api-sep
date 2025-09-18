<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\ProductsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the API PT. Sahabat Energi Persada',
    ]);
});

Route::get('/users', [UserController::class, 'index']);
Route::prefix('categories')->group(function () {
    Route::post('/insert', [CategoryController::class, 'insertCategory']);
    Route::post('/show', [CategoryController::class, 'show']);
    Route::post('/update', [CategoryController::class, 'update']);
    Route::post('/delete', [CategoryController::class, 'delete']);
    Route::post('/showAll', [CategoryController::class, 'showAll']);
});
Route::prefix('sub-category')->group(function () {
    Route::post('/insert', [SubCategoryController::class, 'insertSubCateory']);
    Route::post('/show', [SubCategoryController::class, 'show']);
    Route::post('/update', [SubCategoryController::class, 'update']);
    Route::post('/delete', [SubCategoryController::class, 'delete']);
    Route::post('/showAll', [SubCategoryController::class, 'showAll']);
    Route::post('/showByIdCategory', [SubCategoryController::class, 'showByIdCategory']);
});
Route::prefix('products')->group(function () {
    Route::post('/insert', [ProductsController::class, 'insertProduct']);
    Route::post('/show', [ProductsController::class, 'show']);
    Route::post('/update', [ProductsController::class, 'update']);
    Route::post('/delete', [ProductsController::class, 'deleteProducts']);
});

