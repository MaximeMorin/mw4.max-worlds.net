<!DOCTYPE html>
<html ng-app="mw4" class="no-js" ng-controller="mainController as main">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">		
		<title>MechWarrior 4 : Mercs &ndash; Game Statistics</title>				
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">			
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" />
		
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-resource.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-route.js"></script>
		<script src="/js/app.js"></script>			
	</head>

	<body>
		<h1>Recent Games</h1>
		<div ng-repeat="game in main.games | orderBy:'-id'">			
			<h3>{{ game.game_type }} on {{ game.map }} for {{ game.time_limit }} minutes at {{ game.date }}</h3>
			<table class="table table-hover">
				<thead>
					<tr>
						<th style="width:20%">Name</th>
						<th>Mech</th>
						<th style="width:12%" class="text-right">Score</th>
						<th style="width:12%" class="text-right">Kills</th>
						<th style="width:12%" class="text-right">Deaths</th>
						<th style="width:12%" class="text-right">K/D Ratio</th>
					</tr>
				</thead>
				<tbody ng-repeat="team in game.teams | orderBy:'-score'">
					<tr>
						<th>
							Team {{ team.id }} &ndash;
							<span ng-show="$first">Winner</span>
							<span ng-show="!$first">Loser</span>
						</th>
						<th>-</th>
						<th class="text-right">{{ team.score }}</th>
						<th class="text-right">{{ team.kills }}</th>
						<th class="text-right">{{ team.deaths }}</th>
						<th class="text-right">{{ main.getKDRatio(team.kills, team.deaths) | number:2 }}</th>
					</tr>
					<tr ng-repeat="score in team.scores | orderBy:['-player_score']">
						<td style="padding-left:2em;">
							<span ng-show="!score.player_is_bot">
								<i class="fa fa-user fa-lg"></i>
							</span>
							<span ng-show="score.player_is_bot">
								<i class="fa fa-android fa-lg"></i>
							</span>
							<span style="padding-left:0.5em">
								{{ score.player_name }}
							</span>
						</td>
						<td>{{ score.player_mech }} ({{ score.player_weight }} tons)</td>
						<td class="text-right">{{ score.player_score }}</td>
						<td class="text-right">{{ score.player_kills }}</td>
						<td class="text-right">{{ score.player_deaths }}</td>										
						<td class="text-right">{{ main.getKDRatio(score.player_kills, score.player_deaths) | number:2 }}</td>										
					</tr>
				</tbody>		
			</table>			
		</div>
	</body>

</html>