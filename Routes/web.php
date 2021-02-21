<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('companyhudson')->group(function() {
/*    Route::get('api', 'ApiController@index')->name('companycasabonita.api');

    Route::post('api/all', 'ApiController@storeAll')->name('companycasabonita.api.store.all');
    Route::post('api/{order}', 'ApiController@store')->name('companycasabonita.api.store');
*/

    Route::get('hudson', 'HudsonController@index')->name('hudson');

	Route::post('hudson/pedidos/{pedido}', 'HudsonController@store')->name('hudson.pedidos.store');
	Route::post('hudson/pedidos/{pedido}/retry', 'HudsonController@store_retry')->name('hudson.pedidos.store.retry');

	Route::post('hudson/pedidos', 'HudsonController@store')->name('hudson.pedidos.store.all');
});