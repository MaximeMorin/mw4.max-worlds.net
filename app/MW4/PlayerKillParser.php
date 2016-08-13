<?php

namespace App\MW4;

use DB;

// Example :
// 1092.9581	Player_Kill	276	255	120	0
class PlayerKillParser implements iParser
{
	public function parse($gameId, $args) {		
		$killerId = $args[2];
		$killedId = $args[3];
		
		if ($killerId !== $killedId) {
			DB::update('UPDATE game_scores
						SET player_kills = player_kills + 1
						WHERE game_id = :gameId 
							AND player_game_id = :killerId
							AND player_disconnected = 0', ['gameId' => $gameId, 'killerId' => $killerId]);	
		}
		DB::update('UPDATE game_scores
					SET player_deaths = player_deaths + 1
					WHERE game_id = :gameId 
						AND player_game_id = :killedId
						AND player_disconnected = 0', ['gameId' => $gameId, 'killedId' => $killedId]);					
	}
}
