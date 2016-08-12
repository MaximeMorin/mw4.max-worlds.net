<?php

namespace App\MW4;

use DB;

// Example :
//1105.9030	Player_Disconnect	6	{CYT} ARCHER
class PlayerConnectParser implements iParser
{
	public function parse($gameId, $args) {		
		
		// WHAT TO DO WITH A DISCONNECT!?!?! (Mech change, etc...)
		
		$playerId = $args[2];
		$playerName = $args[3];
		$playerMech = $args[4];
		$playerWeight = $args[5];
		$playerBot = strpos($args[6], 'IS_A_BOT') !== FALSE;
		
		
		DB::update('UPDATE games SET map = :map WHERE id = :gameId' , ['map' => $splittedLine[4], 'gameId' => $gameId]);			
	}
}
