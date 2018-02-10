'use strict'

angular.module('XY').controller('TodaySummarySheetController', ['EventService', '$scope', '$log','PageService','SummarySheetModel','ViewOrderModel', 'UserService',
    function(EventService, $scope, $log,PageService, model,model2,UserService) {
        $scope.PageService = PageService
        $scope.EventService = EventService
        $scope.$log = $log

        $scope.model = model
        $scope.data = model.todaySummarySheet;

        var reg_date = new Date();
        reg_date.setTime($scope.data.reg_time * 1000);
        model.reg_date2 = reg_date.toLocaleDateString();


        // $scope.sheetData = model.query.getData();

        // $log.log('wjw',$scope.sheetData);

        $scope.total = {quantity:0,price:0,profit:0};

        $scope.goBack = function() {
            var reg_date = new Date();
            reg_date.setTime($scope.data.reg_time * 1000);
            model.reg_date2 = reg_date.toLocaleDateString();
        	PageService.setNgViewPage('EverydaySummarySheet')
        }

        $scope.viewOrder = function (type) {
            var arg = {};
            arg.type = type;
            arg.time = $scope.data.reg_time;
            EventService.emit(EventService.ev.START_VIEW_ALL_ORDER,arg);
        }

        $scope.showLabel=[false,false,false,false];
        $scope.toggleSelectList = function (id) {
            $scope.showLabel[id] = $scope.showLabel[id]?false:true;
        }

        $scope.IsSalesperson = false;
        if(UserService.getLoginStatus().rpg == 2){
            $scope.IsSalesperson = true;
        }

        //打开库存详情
        $scope.skuDetail = function(item){
            EventService.emit(EventService.ev.START_CREATE_SkuDetail,item);
        }
    }
])