<?php

use App\Http\Controllers\API\v1\AuthController;
use Illuminate\Support\Facades\Route;




Route::group([
    'prefix' => 'admin',
    'middleware' => ['is_admin', 'auth:api']
], function ($router) {
    Route::prefix('/product')->group(function () {
        Route::post('/add', 'API\v1\ProductsController@create');
        Route::get('', 'API\v1\ProductsController@index');
        Route::get('/{id}', 'API\v1\ProductsController@show');
        Route::put('/{id}/update', 'API\v1\ProductsController@update');
        Route::delete('/{id}/delete', 'API\v1\ProductsController@delete');
    });

    #Routes for Admins Operations on institutions
    Route::post('', 'API\v1\InstitutionController@index');
    Route::prefix('/institution')->group(function () {
        Route::get('/{id}', 'API\v1\InstitutionController@show');
    });


});


#Routes for operating in istitutions by institutions managers

Route::prefix('/institution')->group(function () {
    Route::middleware('auth:api')->post('/register', 'API\v1\InstitutionController@create');
    Route::middleware('auth:api')->put('/{id}/update', 'API\v1\InstitutionController@update');
    Route::middleware('auth:api')->get('/{id}', 'API\v1\InstitutionController@show');
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
