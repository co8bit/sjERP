'use strict';


xy.controller('ManageGoodAndCatController', ['EventService', '$rootScope', '$scope', '$log', '$timeout', 'PageService', 'StockService', 'ConfigService',
    function(EventService, $rootScope, $scope, $log, $timeout, PageService, StockService, ConfigService) {

        //默认激活标签号
        $scope.activeTabNum = 1;

        
        //映射
        $scope.StockService = StockService;

        // $scope.confirmDeleteSKU = function (sku_id) {
        //     if (confirm("确定删除吗？")) {
        //         StockService.deleteSKU(sku_id);
        //     }
        //     else {
        //     }
        // }
        
        $scope.confirmDeleteCat = function (item) {
            if (confirm("确定删除吗？")) {
                StockService.deleteCat(item);
            }
            else {
            }
        }
        
        
    }
]);
