<?php

use \Illuminate\Support\Facades\Route;

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
//Route::get('/sistema/{modulo}/{objeto}/{idRecurso}', 'Perfil\AccessbyUrlController@url')->name('url');

//Rutas Despachos ------------------

Route::get('/sistema/despacho/{objeto}/{modulo}', 'EgresoController@index2')->name('despacho');
Route::get('/sistema/despacho/{objeto}/{modulo}/registro', 'EgresoController@form')->name('despacho.registro');
Route::get('/sistema/despacho/data/{empleado}/{semana}/{hacienda}/{axios}', 'EgresoController@getdespacho');
Route::post('/sistema/despacho/save', 'EgresoController@save')->name('despacho.save');
Route::put('/sistema/despacho/edit', 'EgresoController@editDetalle')->name('despacho.edit');
Route::delete('/sistema/despacho/delete/{id}', 'EgresoController@deleteDetalle');

//Rutas Enfunde -----------------------------------------------------------------------------------------------------------------------------------------------

Route::get('/sistema/enfunde/{objeto}/{modulo}', 'EnfundeController@index')->name('enfunde');
Route::get('/sistema/enfunde/{objeto}/{modulo}/registro', 'EnfundeController@form')->name('enfunde.form');
Route::get('/sistema/enfunde/registro/search', 'EnfundeController@search')->name('enfunde.search');
Route::get('/sistema/enfunde/registro/{idlotero}/{semana}', 'EnfundeController@getLotero')->name('lotero');
Route::post('/sistema/enfunde/registro/save', 'EnfundeController@save')->name('enfunde.save');
Route::post('/sistema/enfunde/registro/cierre_semana/{lotero}/{semana}', 'EnfundeController@cerrar_enfunde')->name('enfunde.close');
Route::post('/sistema/enfunde/registro/cierre_semana/all/{idHacienda}', 'EnfundeController@cerrar_enfundeAll')->name('enfunde.closeAll');
Route::delete('/sistema/enfunde/registro/pre/delete/{lotero}/{semana}', 'EnfundeController@delete_presente')->name('enfunde.delete_presente');
Route::delete('/sistema/enfunde/registro/fut/delete/{lotero}/{semana}', 'EnfundeController@delete_futuro')->name('enfunde.delete_futuro');

Route::get('/sistema/enfunde/reporte/{objeto}/{modulo}', 'RepEnfundeController@index')->name('enfunde.reporte.semana');
Route::get('/sistema/enfunde/reporte/lotero/{objeto}/{modulo}', 'RepEnfundeSemController@index')->name('enfunde.reporte.lotero');
Route::post('/sistema/enfunde/reporte/semanal', 'RepEnfundeController@getEnfunde')->name('enfunde.reporte.semanal');
Route::post('/sistema/enfunde/reporte/semanal/lotero', 'RepEnfundeSemController@repEnfundeSemanal')->name('enfunde.rep_semanal_pdf');
Route::post('/sistema/enfunde/reporte/semanal/data', 'RepEnfundeSemController@repIndexEnfundeSemanal')->name('enfunde.rep_semanal_data');

//-------------------------------------------------------------------------------------------------------------------------------------------------------------

Route::post('/sistema/produccion/liquidacion/semana/upload', 'LiquidacionController@uploadFile')->name('produccion.liquid.upload');
Route::post('/sistema/produccion/liquidacion/cajas/semana', 'LiquidacionController@getCajasSemana')->name('produccion.liquid.bansis');

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
Route::get('/api/bansis/enfunde/saldo_empleado/{idempleado}/{idmaterial}/{semana}', 'EgresoController@saldopendiente');
Route::get('/api/bansis/nominas/rrhh/empleados/{criterio}', 'EmpleadoController@Empleados')->name('empleados');

Route::get('/api/bansis/loteros/{criterio}', 'EnfundeController@Loteros')->name('loteros');
Route::get('/api/bansis/calendario/{fecha?}', 'Sistema\UtilidadesController@getSemana')->name('calendario');
Route::get('/api/xass/productos/{bodega}/{criterio}', 'Sistema\UtilidadesController@getProductos')->name('productos');

Route::get('/empacadora/cajas', 'PruebasController@cajas')->name('empacadora.cajas');
Route::get('/empacadora/cajas/api-allweitghts/{hacienda}/{datefrom}/{dateuntil}/{access_token?}',
    'PruebasController@getDataApi')
    ->middleware('checkhacienda')
    ->name('empacadora.balanza');


