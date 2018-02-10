//controller
'use strict';

xy.controller('CartController', ['$rootScope', '$scope', '$log', 'PageService', 'StockService',
    function($rootScope, $scope, $log, PageService, StockService) {

        $log.debug('CartController');

        $scope.cartItemList = $scope.cartAgent.cartItemList;

        // 从购物车中删除条目
        $scope.deleteFromCart = function (item) {
            $log.debug('click delete')
            $scope.cartAgent.deleteItem(item);
        }

        // 
        $scope.$watchCollection('cartAgent.cartItemList',function() {
            $scope.cartAgent.calcTotalPrice()
        })




    }
]);