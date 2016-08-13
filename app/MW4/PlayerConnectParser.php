<?php

namespace App\MW4;

use DB;

// Example :
//0.0000	Player_Connect	277	Ferret	mechs\thor\thor.data	70.00	IS_A_BOT:Content\ABLScripts\Bots\UberBot.abl
class PlayerConnectParser implements iParser
{
	public function parse($gameId, $args) {		
		$data = [];
		$data['gameId'] = $gameId;
		$data['playerId'] = $args[2];
		$data['playerName'] = $args[3];
		$data['playerTeam'] = null;
		$data['playerMech'] = $this->getMechName($args[4]);
		$data['playerWeight'] = $args[5];
		$data['playerBot'] = strpos($args[6], 'IS_A_BOT') !== FALSE;
		
		DB::insert('INSERT INTO game_scores (game_id, player_game_id, player_name, player_team, player_is_bot, player_mech, player_weight, player_score, player_kills, player_deaths)
					VALUES (:gameId, :playerId, :playerName, :playerTeam, :playerBot, :playerMech, :playerWeight, 0, 0, 0)', $data);		
	}
	
	private function getMechName($mechCode) {
		$mechs = DB::select("SELECT * FROM mechs WHERE code = :code LIMIT 1", ['code' => $mechCode]);
		
		$mechName = $mechCode;
		if (count($mechs) > 0) {
			$mechName = $mechs[0]->name;
		}
		
		return $mechName;
	}
}
