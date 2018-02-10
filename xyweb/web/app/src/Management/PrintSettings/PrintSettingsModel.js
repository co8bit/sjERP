'use strict';

angular.module('XY').factory('PrintSettingsModel', ['NetworkService', '$log', '$q', 'EventService', 'PageService',
    function(NetworkService, $log, $q, EventService, PageService) {
		var model = {};

		/**
	     * 向服务器请求模板
	     */
		model.requestPrintTemplate = function () {
			var defered = $q.defer();
			NetworkService.request('getTemplate',{ 'class': 1 },function(data){
					model.print_template = data.data;
					defered.resolve();
			});
			return defered.promise;
		}

		//打开打印设置
        EventService.on(EventService.ev.PRINT_SETTINGS, function () {
            model.requestPrintTemplate().then( function (){
                PageService.setNgViewPage('PrintSettings');
            })
        });
		return model;
    }
]);
