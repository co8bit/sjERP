'use strict'

xy.factory('CorporateSectorModel', [ '$log', 'EventService', 'PageService', 'NetworkService',
	function($log, EventService, PageService, NetworkService) {
		var model = {};
		model.departmentList = [];
		EventService.on(EventService.ev.START_CORPORATE_SECTORY, function(event,arg){
			model.getDepartment(function (){
				PageService.setNgViewPage('CorporateSector');
			})
		});

		model.getDepartment = function(callback){
			NetworkService.request('getDepartment',{},function (data){
				angular.copy(data.data,model.departmentList);
				if (callback) {
					callback();
				}
			})
		}
		return model;
	}
])