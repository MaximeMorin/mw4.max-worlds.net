<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () {	
	return response()->json();
});

$app->post('/api/game/files/', ['uses' => 'APIController@postGameFile']);

$app->get('/api/players/', ['uses' => 'APIController@getPlayers']);
$app->get('/api/players/types/', ['uses' => 'APIController@getPlayerTypes']);
