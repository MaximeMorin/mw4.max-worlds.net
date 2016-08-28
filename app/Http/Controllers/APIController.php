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
	
	public function getGames() {
		$gamesFromDB = DB::select("SELECT * FROM games");
		
		$games = [];		
		foreach ($gamesFromDB as $game) {
			$game->teams = [];
			$games[$game->id] = $game;
		}
		
		$gameScores = DB::select("SELECT * FROM game_scores");
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
	
	public function getPlayers() {
		$players = DB::select("
			SELECT player_name, AVG(player_weight) AS average_weight, SUM(player_score) AS sum_score, SUM(player_kills) AS sum_kills,
				SUM(player_deaths) AS sum_deaths, `gamesPlayedByPlayer`(player_name) AS game_played, `gamesWonByPlayer`(player_name) AS game_won,
				SUM(GREATEST(time_end, time_start) - time_start) AS time_played
			FROM game_scores
			WHERE player_is_bot = 0
			GROUP BY player_name");
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
