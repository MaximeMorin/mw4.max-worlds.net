<?php

namespace App\MW4;

use DB;

// Example :
// 0.0000	Game_Start
// 0.0000	8/8/2016
// 0.0000	11:49:19 PM
// Args : Game_Start 8/8/2016 11:49:19 PM
class GameStartParser implements iParser
{
	public function parse($gameId, $args) {		
		$data = [];
		$data['gameId'] = $gameId;
		$data['date'] = GameParser::sanitizeValue($args[1]) . ' ' . GameParser::sanitizeValue($args[2]);
		
		/*echo "UPDATE games
					SET `date` = STR_TO_DATE(:date, \'%m/%d/%Y %h:%i:%s %p\')
					WHERE id = :gameId";
		print_r($data);*/
		DB::update('UPDATE games
					SET `date` = STR_TO_DATE(:date, \'%m/%d/%Y %h:%i:%s %p\')
					WHERE id = :gameId', $data);		
	}
}
