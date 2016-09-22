'use strict';

angular.module('mw4', [
	'ngRoute',
	'ngResource',
	'angular-loading-bar'
]);

angular.module('mw4').config(['$routeProvider', 'cfpLoadingBarProvider', function ($routeProvider, cfpLoadingBarProvider) {
	$routeProvider.when('/games', {
        templateUrl: 'views/games.html',
        controller: 'gamesController',
        controllerAs: 'gamesCtrl'
      }).when('/players', {
        templateUrl: 'views/players.html',
        controller: 'playersController',
        controllerAs: 'playersCtrl'
      }).when('/logs', {
        templateUrl: 'views/logs.html'
      }).otherwise({redirectTo: '/players'});
	  
	  cfpLoadingBarProvider.includeSpinner = false;	  
}]);

angular.module('mw4').factory('statsService', ['$resource', function ($resource) {
	return $resource('/api/:stats/:ladderId', null, {
		'allLadders': { method: 'GET', params: {'stats' : 'ladders', 'ladderId' : null}, isArray: true },
		'allGames': { method: 'GET', params: {'stats' : 'games', 'ladderId' : '@ladderId'}, isArray: true },
		'allPlayers': { method: 'GET', params: {'stats' : 'players', 'ladderId' : '@ladderId'}, isArray: true }
	});
}]);

angular.module('mw4').factory('statsFactory', ['statsService', function(statsService) {
	var factory = this;
	factory.utils = {};
	factory.selectedLadder = {};
		
	factory.allLadders = function() {	
		return statsService.allLadders().$promise;		
	};
		
	factory.allGames = function(ladderId) {	
		return statsService.allGames({'ladderId' : ladderId}).$promise;		
	};
		
	factory.allPlayers = function(ladderId) {	
		return statsService.allPlayers({'ladderId' : ladderId}).$promise;		
	};
		
	factory.utils.getRatio = function(numerator, denominator) {
		return numerator / Math.max(denominator, 1);
	};	
	
	factory.utils.getTotalTimePlayed = function(timePlayed) {
		return moment.duration(parseFloat(timePlayed), "seconds").humanize();
	};		
	
	return factory;
}]);

angular.module('mw4').controller('mainController', ['$location', 'statsFactory', function($location, statsFactory) {
	var ctrl = this;
	ctrl.ladders = [];
	ctrl.selectedLadder = null;
	
	ctrl.init = function() {
		statsFactory.allLadders().then(function(results) {
			ctrl.ladders = results;

			if (ctrl.ladders.length > 0) {
				ctrl.selectedLadder = ctrl.ladders[ctrl.ladders.length - 1];
				ctrl.pushNewLadderToFactory();
			}
		});			
	};	
	
	ctrl.pushNewLadderToFactory = function() {
		statsFactory.selectedLadder = ctrl.selectedLadder;
	};
	
	ctrl.isTabActive = function(tabRoute) {
		return $location.path().indexOf(tabRoute) >= 0;
	};
	
	ctrl.getLadderById = function(id) {
		var ladder = null;
		
		ctrl.ladders.forEach(function(l) {
			if (l.id === id) {
				ladder = l;
			}
		});
		
		return ladder;
	};
	
	ctrl.init();
	
	return ctrl;
}]);

angular.module('mw4').controller('gamesController', ['$scope', '$filter', 'statsFactory', function($scope, $filter, statsFactory) {
	var ctrl = this;
	ctrl.statsFactory = statsFactory;
	
	ctrl.allGames = [];
	ctrl.games = [];
	
	ctrl.init = function() {
		if (statsFactory.selectedLadder.hasOwnProperty('id')) {
			statsFactory.allGames(statsFactory.selectedLadder.id).then(function(results) {
				ctrl.allGames = $filter('orderBy')(results, 'id', true);
				ctrl.games = $filter('limitTo')(ctrl.allGames, 10);
				ctrl.games.forEach(function(g) {
					g.teams = $filter('orderBy')(g.teams, 'score', true);
					g.teams.forEach(function(t) {
						t.scores = $filter('orderBy')(t.scores, 'player_score', true);
					});
				});
			});
		}
	};
	
	ctrl.getTimePlayed = function(score) {
		var start = parseFloat(score.time_start);
		var end = parseFloat(score.time_end);
		return moment.duration(end - start, "seconds").humanize();
	};		
	
	ctrl.getKDRatio = function(kills, deaths) {
		return statsFactory.utils.getRatio(kills, deaths);
	};
	
	$scope.$watch('gamesCtrl.statsFactory.selectedLadder', function(newValue, oldValue) {
		if (newValue !== oldValue) {
			ctrl.init();
		}
	});
	
	ctrl.init();
	
	return ctrl;
}]);

angular.module('mw4').controller('playersController', ['$scope', '$filter', 'statsFactory', function($scope, $filter, statsFactory) {
	var ctrl = this;
	ctrl.statsFactory = statsFactory;
	
	ctrl.players = [];
		
	ctrl.init = function() {
		ctrl.maximumGamePlayed = 0;
		ctrl.maximumKillDeathRatio = 0;
		ctrl.maximumWinLoseRatio = 0;
		ctrl.maximumEfficiency = 0;		
		
		if (statsFactory.selectedLadder.hasOwnProperty('id')) {		
			statsFactory.allPlayers(statsFactory.selectedLadder.id).then(function(results) {			
				ctrl.players = results;
				ctrl.initComputedStats();
				ctrl.bindPlayerTrophiesToLadders();
				ctrl.players = $filter('orderBy')(ctrl.players, 'totalScore', true);
			});		
		}
	};
	
	ctrl.initComputedStats = function() {
		ctrl.players.forEach(function(p) {
			p.kdRatio = statsFactory.utils.getRatio(p.sum_kills, p.sum_deaths);
			p.game_won = p.game_played - p.game_lost;
			p.wlRatio = statsFactory.utils.getRatio(p.game_won, p.game_lost);
			p.efficiency = p.sum_score / Math.max(p.time_played, 1) / Math.max(p.average_weight, 1);
			
			if (p.game_played > ctrl.maximumGamePlayed) {
				ctrl.maximumGamePlayed = p.game_played;
			}
			
			if (p.kdRatio > ctrl.maximumKillDeathRatio) {
				ctrl.maximumKillDeathRatio = p.kdRatio;
			}
			
			if (p.wlRatio > ctrl.maximumWinLoseRatio) {
				ctrl.maximumWinLoseRatio = p.wlRatio;
			}
			
			if (p.efficiency > ctrl.maximumEfficiency) {
				ctrl.maximumEfficiency = p.efficiency;
			}
		});
		
		ctrl.players.forEach(function(p) {
			p.gamePlayedScore = statsFactory.utils.getRatio(p.game_played, ctrl.maximumGamePlayed) * 25;
			p.kdRatioScore = statsFactory.utils.getRatio(p.kdRatio, ctrl.maximumKillDeathRatio) * 25;
			p.wlRatioScore = statsFactory.utils.getRatio(p.wlRatio, ctrl.maximumWinLoseRatio) * 25;
			p.efficiencyScore = p.efficiency / ctrl.maximumEfficiency * 25;		
			p.totalScore = p.gamePlayedScore + p.kdRatioScore + p.wlRatioScore + p.efficiencyScore;
		});
	};
	
	ctrl.bindPlayerTrophiesToLadders = function() {
		
	};
	
	ctrl.getTotalTimePlayed = function(timePlayed) {
		return statsFactory.utils.getTotalTimePlayed(timePlayed);
	};
	
	ctrl.getTrophyTextualPosition = function(position) {
		var text = "1st";
		
		if (position == 2) {
			text = "2nd";
		} else if (position == 3) {
			text = "3rd";
		}
		
		return text;
	};
	
	$scope.$watch('playersCtrl.statsFactory.selectedLadder', function(newValue, oldValue) {
		if (newValue !== oldValue) {
			ctrl.init();
		}
	});		
	
	ctrl.init();
	
	return ctrl;
}]);
