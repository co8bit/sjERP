//controller
'use strict';

xy.controller('StockHistoryController', ['$rootScope', '$scope', '$log', 'PageService', 'StockService','OrderService','FilterClassService',
    function($rootScope, $scope, $log, PageService, StockService,OrderService,FilterClassService) {

        $log.debug('StockHistoryController');

        $scope.pageTitle = '库存流水查询'

        $scope.orderList = OrderService.getOrderList()

        // 向过滤器类要一个新的过滤器对象
        $scope.filter = FilterClassService.newDocFilter()

        $scope.filterfunc = function(value) {
        	$log.debug('filter func value: ',value)
        	return $scope.filter.check(value)
        }
    }
]);