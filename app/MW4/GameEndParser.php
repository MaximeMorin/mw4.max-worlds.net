<?php

namespace App\MW4;

use DB;

// Example :
// 1801.5452	Game_End	RecycleState
class GameEndParser implements iParser
{
	public function parse($gameId, $args) {		
		$data = [];
		$data['gameId1'] = $gameId;
		$data['gameId2'] = $gameId;
		DB::update('UPDATE games 
					SET winning_team = (
						SELECT player_team
						FROM game_scores
						WHERE game_id = :gameId1
						GROUP BY player_team
						ORDER BY SUM(player_score) DESC
						LIMIT 1
					)
					WHERE id = :gameId2', $data);	
	}
}
