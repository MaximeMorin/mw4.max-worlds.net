<?php

namespace App\MW4;

use DB;

class GameParser
{	
	public static function parse($gameId) {
		$gameFile = env('MW4_GAME_STATS_FOLDER') . $gameId . '.log';
		
		DB::beginTransaction();
		
		$contentArray = file($gameFile);
		$contentLength = count($contentArray);
		for ($i = 0; $i < $contentLength; $i++) {
			$parser = null;
			$splitLine = self::splitLine($contentArray, $i);	
			
			$eventType = self::sanitizeValue($splitLine[1]);
			switch ($eventType) {
				case "info":
					$parser = new InfoParser();
					break;
				case "Game_Start":
					$args = [];
					$args[0] = "Game_Start";
					
					$splitLine = self::splitLine($contentArray, ++$i);
					$args[1] = $splitLine[1];
										
					$splitLine = self::splitLine($contentArray, ++$i);
					$args[2] = $splitLine[1];
					
					$splitLine = $args;
						
					$parser = new GameStartParser();
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
			
			if ($parser != null) {
				$parser->parse($gameId, $splitLine);
			}			
		}			
		
		DB::commit();
	}
	
	private static function splitLine($contentArray, $index) {
		$line = $contentArray[$index];
		return explode("\t", $line);
	}
	
	public static function sanitizeValue($value) {
		return str_replace(array("\n", "\r"), '', $value);
	}
}
