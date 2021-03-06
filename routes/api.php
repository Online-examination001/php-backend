<?php
use Illuminate\Support\Facades\Route;


Route::post('/admin/login', 'API\v1\auth\AdminAuthController@login');
Route::post('admin/register', 'API\v1\auth\AdminAuthController@register');
Route::group([
    'prefix' => 'admin',
    'middleware' => []
], function ($router) {
    Route::prefix('/auth')->group(function () {
        Route::post('/register', 'API\v1\auth\AdminAuthController@register');
        Route::middleware('auth:api')->post('/logout', 'API\v1\auth\AdminAuthController@logout');
        Route::middleware('auth:api')->post('/me', 'API\v1\auth\AdminAuthController@me');
        Route::post('refresh', 'API\v1\auth\AdminAuthController@refresh');
        Route::post('/update_account', 'API\v1\AdminAuthController@update');
        Route::delete('/delete_account', 'API\v1\AdminAuthController@destroy');
    });





    Route::prefix('/products')->group(function () {
        Route::post('/add', 'API\v1\ProductsController@create');
        Route::get('', 'API\v1\ProductsController@index');
        Route::get('/{id}', 'API\v1\ProductsController@show');
        Route::put('/{id}/update', 'API\v1\ProductsController@update');
        Route::delete('/{id}/delete', 'API\v1\ProductsController@delete');

    });

    #Routes for Admins Operations on institutions

    Route::prefix('/institutions')->group(function () {
        Route::get('/{id}', 'API\v1\InstitutionController@adminShow');
        Route::get('', 'API\v1\InstitutionController@index');

    });




    Route::prefix('/purchase')->group(function () {
        #Route for Viewing purchases
        Route::get('', 'API\v1\PurchaseController@index');
        Route::get('/{id}', 'API\v1\PurchaseController@show');
    });



});




#Routes for making purchase for institutions, viewing and to top_up

Route::prefix('/purchase')->group(function () {
    #Route for Viewing purchases
    Route::get('/{id}', 'API\v1\PurchaseController@show');
    Route::put('/{id}/top_up', 'API\v1\PurchaseController@update');
    Route::post('/buy', 'API\v1\PurchaseController@create');
});



Route::post('/buy', 'API\v1\PurchaseController@create');


#Routes for operating in istitutions by institutions managers
Route::prefix('/institution')->group(function () {
    Route::post('/register', 'API\v1\InstitutionController@create');
    Route::middleware('auth:api')->put('/{id}/update', 'API\v1\InstitutionController@update');
    Route::middleware('auth:api')->get('/detail', 'API\v1\InstitutionController@show');
});



# Routes for normal users
Route::prefix('/products')->group(function () {
    Route::get('', 'API\v1\ProductsController@index');
    Route::get('/{id}', 'API\v1\ProductsController@show');
});
# User  Registration Routes
Route::prefix('/auth')->group(function () {
    Route::post('/register', 'API\v1\AuthController@register');
    Route::post('/login', 'API\v1\AuthController@login');
    Route::middleware('auth:api')->post('/logout', 'API\v1\AuthController@logout');
    Route::middleware('auth:api')->get('/me', 'API\v1\AuthController@me');
    Route::post('refresh', 'API\v1\AuthController@refresh');
    Route::middleware('auth:api')->post('/update_account', 'API\v1\AuthController@update');
    Route::delete('/delete_account', 'API\v1\AuthController@destroy');
});

