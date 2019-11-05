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

//Auth::routes();

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/', 'HomeController@index')->name('home');

//Acceder a modulos del sistema


//URL Enfunde
Route::get('/sistema/{modulo}/{objeto}/{idRecurso}', 'Perfil\AccessbyUrlController@url')->name('url');

Route::get('/sistema/{modulo}/{objeto}/{idRecurso}/registro_despacho', 'EgresoController@form')->name('despacho');
Route::post('/sistema/enfunde/despacho/save', 'EgresoController@save');
Route::put('/sistema/enfunde/despacho/edit', 'EgresoController@editDetalle');
Route::delete('/sistema/enfunde/despacho/delete/{empleado}/{semana}/{hacienda}/{id}', 'EgresoController@deleteDetalle');
Route::get('/sistema/enfunde/despacho/{empleado}/{semana}/{hacienda}/{axios}', 'EgresoController@getdespacho');

Route::get('/sistema/{modulo}/{objeto}/{idRecurso}/registro_enfunde', 'EnfundeController@form')->name('enfunde');
Route::post('/sistema/enfunde/registro/save', 'EnfundeController@save');
Route::get('/sistema/axios/enfunde/lotero/{idlotero}/{semana}', 'EnfundeController@getLotero')->name('lotero');

Route::get('/api/enfunde/saldo_empleado/{idempleado}/{idmaterial}', 'EgresoController@saldopendiente');
Route::get('/api/empleados/{criterio}', 'EmpleadoController@Empleados')->name('empleados');
Route::get('/api/loteros/{criterio}', 'EnfundeController@Loteros')->name('loteros');
Route::get('/api/calendario/{fecha?}', 'Sistema\UtilidadesController@getSemana')->name('calendario');
Route::get('/api/productos/{bodega}/{criterio}', 'Sistema\UtilidadesController@getProductos')->name('productos');

Route::get('/empacadora/cajas', 'PruebasController@cajas')->name('empacadora.cajas');
Route::get('/empacadora/cajas/api-allweitghts/{hacienda}/{datefrom}/{dateuntil}/{access_token?}',
    'PruebasController@getDataApi')
    ->middleware('checkhacienda')
    ->name('empacadora.balanza');


