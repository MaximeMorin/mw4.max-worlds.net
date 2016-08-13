<?php

namespace App\MW4;

use DB;

// Example :
// 1801.5399	PS	273	11627	0	0	0
class PlayerScoreParser implements iParser
{
	public function parse($gameId, $args) {		
		$data = [];
		$data['gameId'] = $gameId;
		$data['playerId'] = $args[2];
		$data['score'] = $args[3];
		
		DB::update('UPDATE game_scores
					SET player_score = :score
					WHERE game_id = :gameId 
						AND player_game_id = :playerId
						AND player_disconnected = 0', $data);	
	}
}
