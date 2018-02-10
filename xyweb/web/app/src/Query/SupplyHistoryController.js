//controller
'use strict';

xy.controller('SupplyHistoryController', ['$rootScope', '$scope', '$log', 'PageService', 'StockService',
    function($rootScope, $scope, $log, PageService, StockService) {

        $log.debug('SupplyHistoryController');

        $scope.pageTitle = '采购历史'

    }
]);