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
      }).otherwise({redirectTo: '/games'});
	  
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
	
	return factory;
}]);

angular.module('mw4').controller('mainController', ['$location', function($location) {
	var ctrl = this;
	
	ctrl.isTabActive = function(tabRoute) {
		return $location.path().indexOf(tabRoute) >= 0;
	};
	
	ctrl.getKDRatio = function(kills, deaths) {
		return kills / Math.max(deaths, 1);
	};
	
	ctrl.getTimePlayed = function(score) {
		var start = parseFloat(score.time_start);
		var end = parseFloat(score.time_end);
		return moment.duration(end - start, "seconds").humanize();
	};		
	
	ctrl.getTotalTimePlayed = function(timePlayed) {
		return moment.duration(parseFloat(timePlayed), "seconds").humanize();
	};		
	
	return ctrl;
}]);

angular.module('mw4').controller('gamesController', ['statsFactory', function(statsFactory) {
	var ctrl = this;
	ctrl.games = [];
		
	ctrl.init = function() {
		statsFactory.allGames().then(function(results) {
			ctrl.games = results;
		});
	};
	
	ctrl.init();
	
	return ctrl;
}]);

angular.module('mw4').controller('playersController', ['statsFactory', function(statsFactory) {
	var ctrl = this;
	ctrl.players = [];
		
	ctrl.init = function() {
		statsFactory.allPlayers().then(function(results) {
			ctrl.players = results;
		});
	};
	
	ctrl.init();
	
	return ctrl;
}]);
