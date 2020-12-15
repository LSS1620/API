<?php

use Illuminate\Http\Request;
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
Route::get('/', function () {
    return 'Api Funcionando ';
});
Route::group(['prefix'=>'v1'],function(){
    Route::group(['prefix'=>'prontuario'],function(){
        Route::get('/', 'ProntuarioController@index')->name('prontuario');
        Route::get('/{id}', 'ProntuarioController@findId')->name('prontuario.findId');
        Route::post('/create', 'ProntuarioController@store')->name('prontuario.store');
        Route::put('/update/{id}', 'ProntuarioController@update')->name('prontuario.update');
        Route::delete('/delete/{id}', 'ProntuarioController@delete')->name('prontuario.delete');
    });
    Route::group(['prefix'=>'itensprontuario'],function(){
        Route::get('/', 'ItensProntuarioController@index')->name('itensprontuario');
        Route::get('/{id}', 'ItensProntuarioController@findId')->name('itensprontuario.findId');
        Route::post('/create', 'ItensProntuarioController@store')->name('itensprontuario.store');
        Route::put('/update/{id}', 'ItensProntuarioController@update')->name('itensprontuario.update');
        Route::delete('/delete/{id}', 'ItensProntuarioController@delete')->name('itensprontuario.delete');
    });
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
