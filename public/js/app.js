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
      }).otherwise({redirectTo: '/players'});
	  
	  cfpLoadingBarProvider.includeSpinner = false;	  
}]);

angular.module('mw4').factory('statsService', ['$resource', function ($resource) {
	return $resource('/api/:stats', null, {
		'allGames': { method: 'GET', params: {'stats' : 'games'}, isArray: true },
		'allPlayers': { method: 'GET', params: {'stats' : 'players'}, isArray: true }
	});
}]);

angular.module('mw4').factory('statsFactory', ['statsService', function(statsService) {
	var factory = this;
	factory.utils = {};
		
	factory.allGames = function() {	
		return statsService.allGames().$promise;		
	};
		
	factory.allPlayers = function() {	
		return statsService.allPlayers().$promise;		
	};
	
	
	factory.utils.getRatio = function(numerator, denominator) {
		return numerator / Math.max(denominator, 1);
	};	
	
	factory.utils.getTotalTimePlayed = function(timePlayed) {
		return moment.duration(parseFloat(timePlayed), "seconds").humanize();
	};		
	
	return factory;
}]);

angular.module('mw4').controller('mainController', ['$location', function($location) {
	var ctrl = this;
	
	ctrl.isTabActive = function(tabRoute) {
		return $location.path().indexOf(tabRoute) >= 0;
	};	
	
	return ctrl;
}]);

angular.module('mw4').controller('gamesController', ['$filter', 'statsFactory', function($filter, statsFactory) {
	var ctrl = this;
	ctrl.allGames = [];
	ctrl.games = [];
		
	ctrl.init = function() {		
		statsFactory.allGames().then(function(results) {
			ctrl.allGames = $filter('orderBy')(results, 'id', true);
			ctrl.games = $filter('limitTo')(ctrl.allGames, 10);
			ctrl.games.forEach(function(g) {
				g.teams = $filter('orderBy')(g.teams, 'score', true);
				g.teams.forEach(function(t) {
					t.scores = $filter('orderBy')(t.scores, 'player_score', true);
				});
			});
		});
	};
	
	ctrl.getTimePlayed = function(score) {
		var start = parseFloat(score.time_start);
		var end = parseFloat(score.time_end);
		return moment.duration(end - start, "seconds").humanize();
	};		
	
	ctrl.getKDRatio = function(kills, deaths) {
		return statsFactory.utils.getRatio(kills, deaths);
	};
	
	ctrl.init();
	
	return ctrl;
}]);

angular.module('mw4').controller('playersController', ['$filter', 'statsFactory', function($filter, statsFactory) {
	var ctrl = this;
	ctrl.players = [];
	ctrl.maximumGamePlayed = 0;
	ctrl.maximumKillDeathRatio = 0;
	ctrl.maximumWinLoseRatio = 0;
	ctrl.maximumEfficiency = 0;
		
	ctrl.init = function() {
		statsFactory.allPlayers().then(function(results) {			
			ctrl.players = results;
			ctrl.initComputedStats();
			ctrl.players = $filter('orderBy')(ctrl.players, 'totalScore', true);
		});
	};
	
	ctrl.initComputedStats = function() {
		ctrl.players.forEach(function(p) {
			p.kdRatio = statsFactory.utils.getRatio(p.sum_kills, p.sum_deaths);
			p.game_lost = p.game_played - p.game_won;
			p.wlRatio = statsFactory.utils.getRatio(p.game_won, p.game_lost);
			p.efficiency = p.sum_score / p.time_played / p.average_weight;
			
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
	
	ctrl.getTotalTimePlayed = function(timePlayed) {
		return statsFactory.utils.getTotalTimePlayed(timePlayed);
	};
	
	ctrl.init();
	
	return ctrl;
}]);
