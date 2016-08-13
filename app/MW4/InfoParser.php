<?php

namespace App\MW4;

use \DateTime;
use DB;

// Examples of info lines :
// 0.0000	info	map	0	Alpine - Alpine
// 0.0000	info	gametype	Team Battle
// 0.0000	info	timelimit	30
class InfoParser implements iParser
{
	public function parse($gameId, $args) {		
		$infoType = $args[2];

		
		switch ($infoType) {
			case "timelimit":
				$timeLimit = $args[3];
				DB::update('UPDATE games SET time_limit = :timeLimit WHERE id = :gameId' , ['timeLimit' => $timeLimit, 'gameId' => $gameId]);
				break;
			case "map":
				DB::update('UPDATE games SET map = :map WHERE id = :gameId' , ['map' => GameParser::sanitizeValue($args[4]), 'gameId' => $gameId]);
				break;
			case "gametype" :
				DB::update('UPDATE games SET game_type = :gameType WHERE id = :gameId' , ['gameType' => GameParser::sanitizeValue($args[3]), 'gameId' => $gameId]);
				break;
		}				
	}
}
