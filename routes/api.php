<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('game', 'GameController@createGame');
Route::get('play-game/{id}', 'GameController@getGame');
Route::post('set-mine/{id}', 'GameController@setMine');
Route::get('get-mine/{id}', 'GameController@getMine');
Route::post('revealed/{id}', 'GameController@revealedCell');
Route::get('reset-revealed/{id}', 'GameController@resetRevealed');
