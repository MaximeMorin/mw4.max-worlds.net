<?php

namespace App\MW4;

use DB;

// Example :
//1105.9030	Player_Disconnect	6	{CYT} ARCHER
class PlayerDisconnectParser implements iParser
{
	public function parse($gameId, $args) {		
		$data = [];
		$data['gameId'] = $gameId;
		$data['playerId'] = $args[2];
		
		DB::update('UPDATE game_scores
					SET player_disconnected = 1
					WHERE game_id = :gameId 
						AND player_game_id = :playerId
						AND player_disconnected = 0', $data);			
	}
}
