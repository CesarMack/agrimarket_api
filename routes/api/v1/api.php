<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/user')->group(function(){
    Route::post('/login', 'App\Http\Controllers\AuthenticationController@login');
    Route::post('/register', 'App\Http\Controllers\AuthenticationController@register');
});
Route::middleware('auth:api')->group(function () {
    Route::prefix('/users')->group(function(){
        Route::get('/', 'App\Http\Controllers\UsersController@index');
        Route::post('/', 'App\Http\Controllers\UsersController@profile');
        Route::get('/{id}', 'App\Http\Controllers\UsersController@show');
        Route::post('/{id}', 'App\Http\Controllers\UsersController@update');
        Route::delete('/{id}', 'App\Http\Controllers\UsersController@destroy');
    });
    Route::prefix('/categories')->group(function(){
        Route::get('/', 'App\Http\Controllers\CategoriesController@index');
        Route::post('/', 'App\Http\Controllers\CategoriesController@store');
        Route::get('/{id}', 'App\Http\Controllers\CategoriesController@show');
        Route::post('/{id}', 'App\Http\Controllers\CategoriesController@update');
        Route::delete('/{id}', 'App\Http\Controllers\CategoriesController@destroy');
    });
    Route::prefix('/product_types')->group(function(){
        Route::get('/', 'App\Http\Controllers\ProductTypesController@index');
        Route::post('/', 'App\Http\Controllers\ProductTypesController@store');
        Route::get('/{id}', 'App\Http\Controllers\ProductTypesController@show');
        Route::post('/{id}', 'App\Http\Controllers\ProductTypesController@update');
        Route::delete('/{id}', 'App\Http\Controllers\ProductTypesController@destroy');
    });
    Route::prefix('/estates')->group(function(){
        Route::get('/', 'App\Http\Controllers\EstatesController@index');
        Route::post('/', 'App\Http\Controllers\EstatesController@store');
        Route::get('/{id}', 'App\Http\Controllers\EstatesController@show');
        Route::post('/{id}', 'App\Http\Controllers\EstatesController@update');
        Route::delete('/{id}', 'App\Http\Controllers\EstatesController@destroy');
    });
    Route::prefix('/suggested_products')->group(function(){
        Route::get('/', 'App\Http\Controllers\SuggestedProductsController@index');
        Route::post('/', 'App\Http\Controllers\SuggestedProductsController@store');
        Route::get('/{id}', 'App\Http\Controllers\SuggestedProductsController@show');
        Route::post('/{id}', 'App\Http\Controllers\SuggestedProductsController@update');
        Route::post('/{id}/update_status', 'App\Http\Controllers\SuggestedProductsController@update_status');
        Route::delete('/{id}', 'App\Http\Controllers\SuggestedProductsController@destroy');
    });
    Route::prefix('/units_of_measurements')->group(function(){
        Route::get('/', 'App\Http\Controllers\UnitOfMeasurementsController@index');
        Route::post('/', 'App\Http\Controllers\UnitOfMeasurementsController@store');
        Route::get('/{id}', 'App\Http\Controllers\UnitOfMeasurementsController@show');
        Route::post('/{id}', 'App\Http\Controllers\UnitOfMeasurementsController@update');
        Route::delete('/{id}', 'App\Http\Controllers\UnitOfMeasurementsController@destroy');
    });
    Route::prefix('/products')->group(function(){
        Route::get('/', 'App\Http\Controllers\ProductsController@index');
        Route::post('/', 'App\Http\Controllers\ProductsController@store');
        Route::get('/{id}', 'App\Http\Controllers\ProductsController@show');
        Route::post('/{id}', 'App\Http\Controllers\ProductsController@update');
        Route::delete('/{id}', 'App\Http\Controllers\ProductsController@destroy');
    });
    Route::prefix('/orders')->group(function(){
        Route::get('/', 'App\Http\Controllers\OrdersController@index');
        Route::post('/', 'App\Http\Controllers\OrdersController@store');
        Route::get('/{id}', 'App\Http\Controllers\OrdersController@show');
        Route::post('/{id}', 'App\Http\Controllers\OrdersController@update');
        Route::delete('/{id}', 'App\Http\Controllers\OrdersController@destroy');
    });
    Route::prefix('/photos')->group(function(){
        Route::get('/', 'App\Http\Controllers\PhotosController@index');
        Route::post('/', 'App\Http\Controllers\PhotosController@store');
        Route::get('/{id}', 'App\Http\Controllers\PhotosController@show');
        Route::post('/{id}', 'App\Http\Controllers\PhotosController@update');
        Route::delete('/{id}', 'App\Http\Controllers\PhotosController@destroy');
    });
});
