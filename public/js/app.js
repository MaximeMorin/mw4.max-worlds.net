'use strict';

angular.module('mw4', [
	'ngRoute',
	'ngResource'
]);

angular.module('mw4').config(['$routeProvider', function ($routeProvider) {
	$routeProvider.otherwise({redirectTo: '/'});
}]);

angular.module('mw4').factory('gamesService', ['$resource', function ($resource) {
	return $resource('/api/games', null, {
		'all': { method: 'GET', params: null, isArray: true }
	});
}]);

angular.module('mw4').factory('gamesFactory', ['gamesService', function(gamesService) {
	var factory = this;
	factory.utils = {};
		
	factory.all = function() {	
		return gamesService.all().$promise;		
	};
	
	return factory;
}]);

angular.module('mw4').controller('mainController', ['gamesFactory', function(gamesFactory) {
	var ctrl = this;
	ctrl.games = [];
		
	ctrl.init = function() {
		gamesFactory.all().then(function(results) {
			ctrl.games = results;
		});
	};
	
	ctrl.getKDRatio = function(kills, deaths) {
		return kills / Math.max(deaths, 1);
	};
	
	ctrl.init();
	
	return ctrl;
}]);