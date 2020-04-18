<?php
use App\Http\Controllers\API\v1\AuthController;
use Illuminate\Support\Facades\Route;




Route::group([
    'prefix' => 'admin',
    'middleware' => ['is_admin', 'auth:api']
], function ($router) {
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

        #Route for Viewing purchases
        Route::get('/purchase', 'API\v1\ProductPurchasedController@index');
        Route::get('/purchase/{id}', 'API\v1\ProductPurchasedController@show');
        Route::put('/purchase/{id}/top_up', 'API\v1\ProductPurchasedController@update');
        Route::put('/purchase/buy', 'API\v1\ProductPurchasedController@create');

    });




    Route::prefix('/purchase')->group(function () {
        #Route for Viewing purchases
        Route::get('', 'API\v1\ProductPurchasedController@index');
        Route::get('/{id}', 'API\v1\ProductPurchasedController@show');
        Route::put('/{id}/update', 'API\v1\ProductPurchasedController@update');
        Route::put('/buy', 'API\v1\ProductPurchasedController@create');
    });



});




#Routes for making purchase for institutions, viewing and to top_up

Route::prefix('/purchase')->group(function () {
    #Route for Viewing purchases
    Route::get('/{id}', 'API\v1\ProductPurchasedController@show');
    Route::put('/{id}/top_up', 'API\v1\ProductPurchasedController@update');
    Route::put('/buy', 'API\v1\ProductPurchasedController@create');
});






#Routes for operating in istitutions by institutions managers
Route::prefix('/institution')->group(function () {
    Route::middleware('auth:api')->post('/register', 'API\v1\InstitutionController@create');
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
});

