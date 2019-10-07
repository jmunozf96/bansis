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

/*Route::get('/', function () {
    return view('welcome');
});

Route::get('/empacadora/{hacienda}/{datefrom}/{dateuntil}/{access_token?}',
    [
        'middleware' => 'checkhacienda',
        'uses' => "PruebasController@getDataApi",
        'as' => "empacadora.cajas"
    ]);*/

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/empacadora/cajas','PruebasController@cajas')->name('empacadora.cajas');
Route::get('/empacadora/cajas/api-allweitghts/{hacienda}/{datefrom}/{dateuntil}/{access_token?}',
    'PruebasController@getDataApi')
    ->middleware('checkhacienda')
    ->name('empacadora.balanza');
