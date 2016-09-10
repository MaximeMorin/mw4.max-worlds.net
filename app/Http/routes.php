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
$app->get('/api/games/reparse', ['uses' => 'APIController@getReParse']);


$app->get('/api/ladders', ['uses' => 'APIController@getLadders']);
$app->get('/api/games/{ladderId}', ['uses' => 'APIController@getGames']);
$app->get('/api/players/{ladderId}', ['uses' => 'APIController@getPlayers']);