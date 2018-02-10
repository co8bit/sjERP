//controller
'use strict';

xy.controller('SalesHistoryController', ['$rootScope', '$scope', '$log', 'PageService', 'StockService','OrderService','FilterClassService',
    function($rootScope, $scope, $log, PageService, StockService,OrderService,FilterClassService) {

        $log.debug('SalesHistoryController');

        $scope.pageTitle = '销售历史'

        $scope.orderList = OrderService.getOrderList()

        var filterObj = FilterClassService.newDocFilterObj()

        $scope.filterfunc = function(value) {
        	$log.debug('value: ',value)


        	return true

        }
    }
]);