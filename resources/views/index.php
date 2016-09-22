<!DOCTYPE html>
<html ng-app="mw4" class="no-js" ng-controller="mainController as mainCtrl">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">		
		
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		
		<title>MechWarrior 4 : Mercs &ndash; Game Statistics</title>				
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">			
		
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" />
		<link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/angular-loading-bar/0.9.0/loading-bar.min.css' type='text/css' media='all' />
		<link rel='stylesheet' href='/css/app.css' type='text/css' media='all' />
 
		<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-resource.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-route.js"></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/angular-loading-bar/0.9.0/loading-bar.min.js'></script>
		<script src="/js/app.js"></script>			
	</head>

	<body>				
		<div class="container-fluid">				
			<div class="row">
				<div class="col-xs-12 col-md-6">
					<h1>Yet Another MechWarrior Server</h1>		
					<h2>{{ mainCtrl.selectedLadder.name }}</h2>
				</div>
				<div class="col-md-offset-2 col-md-4 col-xs-12">
					<form style="margin-top:1.5em;">
						<select class="form-control" ng-options="ladder as ladder.name for ladder in mainCtrl.ladders | orderBy:'-id'" 
								ng-model="mainCtrl.selectedLadder" ng-change="mainCtrl.pushNewLadderToFactory()">							
						</select>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<ul class="nav nav-tabs">
					  <li role="presentation" ng-class="{'active' : mainCtrl.isTabActive('players') }"><a href="#/players">Player Rankings</a></li>						
					  <li role="presentation" ng-class="{'active' : mainCtrl.isTabActive('games') }"><a href="#/games">Recent Games</a></li>
					  <li role="presentation" ng-class="{'active' : mainCtrl.isTabActive('logs') }"><a href="#/logs">Change Log</a></li>
					</ul>						
				</div>
			</div>
			<ng-view />
			
		</div>
	</body>

</html>