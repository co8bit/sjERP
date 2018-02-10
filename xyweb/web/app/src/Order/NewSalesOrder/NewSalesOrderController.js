'use strict';

xy.controller('NewSalesOrderController',['$rootScope', '$scope', '$log', '$timeout', 'StockService', 'NewSalesOrderModel', '$cacheFactory', 'PageService', 'ConfigService', 'CstmrService',
    'GenService', 'OrderService', 'EventService',
    function($rootScope, $scope, $log, $timeout, StockService, model, $cacheFactory, PageService, ConfigService, CstmrService,
        GenService, OrderService, EventService){

    	$scope.model = model;
    	$scope.text = 'model.dialogId : ' + model.dialogId;

    	$scope.quit = function() {
            // if (angular.equals(orderInfo, $scope.orderInfo)) {
            //     PageService.closeDialog();
            // } else {
            //     PageService.showConfirmDialog('确定退出吗？',['直接退出','保存为草稿单据并退出','取消'],function () {
            //         PageService.closeDialog();
            //     },function () {
            //         OrderService.createDraft($scope.orderInfo);
            //     });
            // }
            PageService.closeDialog();
        }
    }


]);