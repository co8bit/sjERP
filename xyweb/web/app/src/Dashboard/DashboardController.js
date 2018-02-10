'use strict';

/* Controllers */

// var salesOrder = angular.module('SalesOrder', []);

xy.controller('DashboardController', ['$scope', '$http', '$log', 'PageService', 'DashboardModel', 'EventService', 'UserService', 'AuthGroupService',
    function($scope, $http, $log, PageService, model, EventService, UserService, AuthGroupService) {

        // $log.debug('DashboardController');

        $scope.model = model;
        $scope.pageState = model.pageState;
		$scope.IsYqjShow =  model.IsYqjShow;
        $scope.isShow = AuthGroupService.getIsShow(AuthGroupService.module.Dashboard);
        if (!$scope.isShow) {
            EventService.emit(EventService.ev.START_VIEW_ALL_ORDER);
            return;
        }
        model.draw();
        model.dealViewDashboard();


        // document.ontouchmove = function(e){ e.preventDefault(); }; //文档禁止 touchmove事件

        // $log.log('model.date model.data.saleTop', model.date, model.data.saleTop);

        $scope.PageService = PageService;


        // $scope.$on('$routeChangeSuccess', function() {
        //  $log.debug('route Success');
            //更新model对dom的认知
            // model.flag_domIsReady = 1;
            // 检查model是否有绘制任务等待完成
            // if (model.flag_controllerDraw) {
            //  $log.debug('Calling draw');
            //  model.draw();
            // }
        // });

        $scope.$on('$destroy',function() {
        	//更新model对dom的认知
        	// model.flag_domIsReady = 0;
        });

        $scope.createBuyOrder = function (item){
            EventService.emit(EventService.ev.START_CREATE_Buy_ORDER,item);
        }
        $scope.createReceipt = function (item){

        }

        $scope.checkRecord = function (cid) {
            var args = {
                cid : cid,
                cstmrPage : 1,
            }
            // $log.log('args.now_page',args.nowPage);
            EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,args);
        }
        $scope.skuDetail = function(item){
            EventService.emit(EventService.ev.START_CREATE_SkuDetail,item);
        }

        //////////////////

        $scope.goReceipt=function(item){
            EventService.emit(EventService.ev.START_CREATE_RECEIPT,item);
        }

    }
]);
