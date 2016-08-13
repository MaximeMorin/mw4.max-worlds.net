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
	return view('index', []);
});

$app->post('/api/game/files/', ['uses' => 'APIController@postGameFile']);

$app->get('/api/games/', ['uses' => 'APIController@getGames']);
$app->get('/api/players/', ['uses' => 'APIController@getPlayers']);

$app->get('/api/games/reparse', ['uses' => 'APIController@getReParse']);
