<?php

namespace App\MW4;

use DB;

// Example :
// 0.0000	Player_Team	264	1  <--- 0 based
class PlayerTeamParser implements iParser
{
	public function parse($gameId, $args) {		
		$data = [];
		$data['gameId'] = $gameId;
		$data['playerId'] = $args[2];
		$data['teamId'] = $args[3] + 1;
		
		DB::update('UPDATE game_scores
					SET player_team = :teamId
					WHERE game_id = :gameId 
						AND player_game_id = :playerId
						AND player_disconnected = 0', $data);	
	}
}
