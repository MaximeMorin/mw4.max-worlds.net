<?php

namespace App\Http\Controllers;

use Log;
use DB;
use Illuminate\Http\Request;

class APIController extends Controller
{
	public function postGameFile(Request $request) {		
		$uploadedFile = $request->file()['file'];
		Log::info('File:' . print_r($uploadedFile, true));
		if ($uploadedFile->isValid()) {
			DB::beginTransaction();
			DB::insert('INSERT INTO games (server_id) VALUES (?);', array(1));
			$gameId = DB::getPdo()->lastInsertId();
			DB::commit();
						
			$uploadedFile->move(env('MW4_GAME_STATS_FOLDER'), $gameId . '.log');
		}				
	}
	
	public function getPlayers() {
		return response()->json(DB::select("SELECT * FROM players"));
	}
	
	public function getPlayerTypes() {
		return response()->json(DB::select("SELECT * FROM player_types"));
	}

}
