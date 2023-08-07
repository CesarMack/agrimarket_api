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
Route::middleware(['auth:api', 'App\Http\Middleware\CheckAdmin'])->group(function () {
    Route::prefix('/admins')->group(function(){
        Route::get('/dashboard', 'App\Http\Controllers\AdminsController@dashboard');
        Route::get('/suggestions', 'App\Http\Controllers\SuggestedProductsController@index');
        Route::post('/suggestions/{id}', 'App\Http\Controllers\SuggestedProductsController@update_status');
        Route::get('/users', 'App\Http\Controllers\UsersController@index');
        Route::post('/find_user', 'App\Http\Controllers\UsersController@find_user');
        Route::post('/register', 'App\Http\Controllers\AdminsController@register');
        Route::post('/find_product_type', 'App\Http\Controllers\ProductTypesController@find_product_type');
        Route::prefix('/categories')->group(function(){
            Route::post('/', 'App\Http\Controllers\CategoriesController@store');
            Route::get('/{id}', 'App\Http\Controllers\CategoriesController@show');
            Route::post('/{id}', 'App\Http\Controllers\CategoriesController@update');
            Route::post('/{id}/active', 'App\Http\Controllers\CategoriesController@destroy');
        });
        Route::prefix('/units_of_measurements')->group(function(){
            Route::post('/', 'App\Http\Controllers\UnitOfMeasurementsController@store');
            Route::get('/{id}', 'App\Http\Controllers\UnitOfMeasurementsController@show');
            Route::post('/{id}', 'App\Http\Controllers\UnitOfMeasurementsController@update');
            Route::post('/{id}/active', 'App\Http\Controllers\UnitOfMeasurementsController@destroy');
        });
        Route::prefix('/product_types')->group(function(){
            Route::post('/', 'App\Http\Controllers\ProductTypesController@store');
            Route::get('/{id}', 'App\Http\Controllers\ProductTypesController@show');
            Route::post('/{id}', 'App\Http\Controllers\ProductTypesController@update');
            Route::post('/{id}/active', 'App\Http\Controllers\ProductTypesController@destroy');
        });
    });
});
Route::middleware(['auth:api', 'App\Http\Middleware\CheckFarmer'])->group(function () {
    Route::prefix('/farmers')->group(function(){
        Route::get('/dashboard', 'App\Http\Controllers\FarmersController@dashboard');
        Route::get('/top_sales', 'App\Http\Controllers\FarmersController@top_sales');
        Route::get('/last_orders', 'App\Http\Controllers\FarmersController@last_orders');
        Route::get('/orders', 'App\Http\Controllers\FarmersController@index');
        Route::post('/update_order_status/{id}', 'App\Http\Controllers\OrdersController@update_order_status');
        //Route::post('/orders/{id}', 'App\Http\Controllers\FarmersController@update_order_status');
    });
});
Route::middleware(['auth:api', 'App\Http\Middleware\CheckClient'])->group(function () {
    Route::prefix('/clients')->group(function(){
        Route::get('/me', 'App\Http\Controllers\ClientsController@me');
        Route::get('/dashboard', 'App\Http\Controllers\ClientsController@dashboard');
        Route::get('/orders', 'App\Http\Controllers\ClientsController@orders');
        Route::post('/update_order_status/{id}', 'App\Http\Controllers\ClientsController@update_order_status');
        Route::post('/me/update', 'App\Http\Controllers\ClientsController@update_me');
    });
});
Route::middleware('auth:api')->group(function () {
    Route::prefix('/users')->group(function(){
        Route::get('/me', 'App\Http\Controllers\UsersController@me');
        Route::post('/me/update', 'App\Http\Controllers\UsersController@update_me');
        Route::get('/{id}', 'App\Http\Controllers\UsersController@show');
        Route::delete('/{id}', 'App\Http\Controllers\UsersController@destroy');
    });
    Route::prefix('/categories')->group(function(){
        Route::get('/', 'App\Http\Controllers\CategoriesController@index');
    });
    Route::prefix('/product_types')->group(function(){
        Route::get('/', 'App\Http\Controllers\ProductTypesController@index');
    });
    Route::prefix('/estates')->group(function(){
        Route::get('/', 'App\Http\Controllers\EstatesController@index');
        Route::post('/', 'App\Http\Controllers\EstatesController@store');
        Route::get('/{id}', 'App\Http\Controllers\EstatesController@show');
        Route::post('/{id}', 'App\Http\Controllers\EstatesController@update');
        Route::delete('/{id}', 'App\Http\Controllers\EstatesController@destroy');
    });
    Route::prefix('/suggested_products')->group(function(){
        //Route::get('/', 'App\Http\Controllers\SuggestedProductsController@index');
        Route::post('/', 'App\Http\Controllers\SuggestedProductsController@store');
        Route::get('/{id}', 'App\Http\Controllers\SuggestedProductsController@show');
        Route::post('/{id}', 'App\Http\Controllers\SuggestedProductsController@update');
        Route::post('/{id}/update_status', 'App\Http\Controllers\SuggestedProductsController@update_status');
        Route::delete('/{id}', 'App\Http\Controllers\SuggestedProductsController@destroy');
    });
    Route::prefix('/units_of_measurements')->group(function(){
        Route::get('/', 'App\Http\Controllers\UnitOfMeasurementsController@index');
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
