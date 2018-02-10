'use strict';

xy.factory('BootStrapService', ['$rootScope', '$log', 'StockService', 'CstmrService','EventService',
    function($rootScope, $log, StockService, CstmrService,EventService) {

      
        //$log.debug('BootStrapService init');

        var service = {}; 

        // 登录成功或者刷新网页的时候会执行的加载数据
        service.loadData = function() {

        	//获取sku
        	//StockService.querySKU(1)

        	//获取类别
        	//StockService.queryCat(1)

        	//获取客户
        	// CstmrService.queryCompany(1)

        }


        


        return service;

    }
]);



