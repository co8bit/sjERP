'use strict'

xy.factory('QueryCatClass', ['$log', 'EventService', 'PageService', 'NetworkService', '$q', function($log, EventService, PageService, NetworkService, $q) {
	var QueryCatClass = {};

	function CatClass() {
		var catList = [];
		var dataToSend = {};
		var type = 1;
		this.setType = function (__type){
			type = __type;
		}
		this.getCatList = function() {
			return catList;
		}
		this.request = function() {
			var defered = $q.defer();
			var dataToSend = {
				type: type,
			};
			NetworkService.request('queryFinanceCart', dataToSend, function(data) {
				angular.copy(data.data, catList)
				defered.resolve();
			})
			return defered.promise;
		}
	}
	QueryCatClass.newQueryCat = function() {
		var out = new CatClass();
		return out;
	}

	return QueryCatClass;
}])