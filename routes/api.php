<?php

use App\Http\Controllers\API\v1\AuthController;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


#Routes for Admin users






Route::group([
    'prefix' => 'admin',
    'middleware' => ['is_admin', 'auth:api']
], function ($router) {
    Route::prefix('/product')->group(function () {
        Route::middleware('auth:api')->post('/add', 'API\v1\ProductsController@create');
        Route::middleware('auth:api')->get('', 'API\v1\ProductsController@index');
        Route::middleware('auth:api')->get('/{id}', 'API\v1\ProductsController@show');
        Route::middleware('auth:api')->put('/{id}/update', 'API\v1\ProductsController@update');
        Route::middleware('auth:api')->delete('/{id}/delete', 'API\v1\ProductsController@delete');
    });
});




# Routes for normal users
Route::prefix('/product')->group(function () {
    Route::get('', 'API\v1\ProductsController@index');
    Route::get('/{id}', 'API\v1\ProductsController@show');
});


# User  Registration Routes
Route::prefix('/user')->group(function () {
    Route::post('/register', 'API\v1\AuthController@register');
    Route::post('/login', 'API\v1\AuthController@login');
    Route::middleware('auth:api')->get('/logout', 'API\v1\AuthController@logout');
});





#Testing
Route::group([


    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});
