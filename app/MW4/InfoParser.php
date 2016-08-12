<?php

namespace App\MW4;

use DB;

// Examples of info lines :
// 0.0000	info	map	0	Alpine - Alpine
// 0.0000	info	gametype	Team Battle
class InfoParser implements iParser
{
	public function parse($gameId, $args) {		
		$infoType = $args[2];
		
		switch ($infoType) {
			case "map":
				DB::update('UPDATE games SET map = :map WHERE id = :gameId' , ['map' => $args[4], 'gameId' => $gameId]);
				break;
			case "gametype" :
				DB::update('UPDATE games SET game_type = :gameType' , ['gameType' => $args[3], 'gameId' => $gameId]);
				break;
		}				
	}
}
