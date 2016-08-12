<?php

namespace App\MW4;

use DB;

// Example :
// 1801.5399	PS	273	11627	0	0	0
class PlayerScoreParser implements iParser
{
	public function parse($gameId, $args) {		
		$playerId = $args[2];
		$playerName = $args[3];
		$playerMech = $args[4];
		$playerWeight = $args[5];
		$playerBot = strpos($args[6], 'IS_A_BOT') !== FALSE;
		
		
		DB::update('UPDATE games SET map = :map WHERE id = :gameId' , ['map' => $splittedLine[4], 'gameId' => $gameId]);			
	}
}
