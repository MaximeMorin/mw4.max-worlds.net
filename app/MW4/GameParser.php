<?php

namespace App\MW4;

use File;
use DB;

class GameParser
{	
	public static function parse($gameId) {
		$gameFile = env('MW4_GAME_STATS_FOLDER') . $gameId . '.log';
		
		DB::beginTransaction();
		
		$content = File::get($gameFile);		
		foreach ($content as $line) {
			$splitLine = explode("\t", $line);	
			$parser = null;
			$eventType = $splitLine[1];
			switch ($eventType) {
				case "info":
					$parser = new InfoParser();
					break;
				case "Player_Connect" :
					$parser = new PlayerConnectParser();
					break;
				case "Player_Team" :
					$parser = new PlayerTeamParser();
					break;
				case "Player_Kill" :
					$parser = new PlayerKillParser();
					break;
				case "Player_Disconnect" :
					$parser = new PlayerDisconnectParser();
					break;
				case "PS" :
					$parser = new PlayerScoreParser();
					break;
			}
			
			$parser->parse($gameId, $splitLine);
		}	
		
		DB::commit();
	}
}
