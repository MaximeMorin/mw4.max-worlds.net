<?php

namespace App\Http\Controllers;

use Log;
use DB;
use Illuminate\Http\Request;
use \App\MW4\GameParser;

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
			
			GameParser::parse($gameId);
		}				
	}
	
	public function getLadders() {
		$ladders = DB::select("SELECT * FROM ladders");	
		return response()->json(array_values($ladders));		
	}
	
	public function getGames($ladderId) {
		$gamesFromDB = DB::select(
			"SELECT *
			FROM games 
			WHERE ladder_id = ?"
		, array($ladderId));
		
		$games = [];		
		foreach ($gamesFromDB as $game) {
			$game->teams = [];
			$games[$game->id] = $game;
		}
		
		$gameScores = DB::select(
			"SELECT gs.* 
			FROM game_scores AS gs 
				INNER JOIN games AS g ON g.id = gs.game_id 
			WHERE g.ladder_id = ?"
		, array($ladderId));
		
		foreach ($gameScores as $score) {
			$game = $games[$score->game_id];			
			
			if (array_key_exists($score->player_team, $game->teams)) {
				$team = $game->teams[$score->player_team];
				$team['score'] += $score->player_score;
				$team['kills'] += $score->player_kills;
				$team['deaths'] += $score->player_deaths;
				$team['scores'][] = $score;
				$game->teams[$score->player_team] = $team;
			} else {
				$team = ['id' => $score->player_team, 'score' => $score->player_score, 'kills' => $score->player_kills, 'deaths' => $score->player_deaths, 'scores' => [ $score ] ];				
				$game->teams[$score->player_team] = $team;
			}			
		}
		
		foreach ($games as $game) {
			$game->teams = array_values($game->teams);
		}		
		return response()->json(array_values($games));
	}
	
	public function getPlayers($ladderId) {
		$players = DB::select("
			SELECT gs.player_name, AVG(gs.player_weight) AS average_weight, SUM(gs.player_score) AS sum_score, SUM(gs.player_kills) AS sum_kills,
				SUM(gs.player_deaths) AS sum_deaths, `gamesPlayedByPlayer`(gs.player_name, ?) AS game_played, `gamesLostByPlayer`(gs.player_name, ?) AS game_lost,
				SUM(GREATEST(gs.time_end, gs.time_start) - gs.time_start) AS time_played
			FROM game_scores AS gs
				INNER JOIN games AS g ON g.id = gs.game_id
			WHERE g.ladder_id = ?
				AND gs.player_is_bot = 0
			GROUP BY gs.player_name", array($ladderId, $ladderId, $ladderId));
		
		$playerTrophies = DB::select("SELECT * FROM trophies ORDER BY ladder_id ASC");		
		foreach ($players as $player) {
			$player->trophies = array();
			foreach ($playerTrophies as $trophy) {
				if ($player->player_name == $trophy->player_name) {
					array_push($player->trophies, $trophy);
				}
			}
		}
		
		return response()->json($players);
	}
	
	public function getReParse() {
		DB::statement("TRUNCATE game_scores");
		$gamesFromDB = DB::select("SELECT * FROM games");
		foreach ($gamesFromDB as $game) {
			GameParser::parse($game->id);
		}
	}
}
