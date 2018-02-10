'use strict'

xy.controller('ReceiptFillController', [ '$scope', '$log', 'ReceiptFillModle', 'PageService', 'NetworkService',
	function($scope, $log, model, PageService, NetworkService) {
		$scope.model = model;
		$scope.orderInfo = model.orderInfo;
		$scope.balance = 0;
		// 初始化开单显示时间
        var time = new Date();
        if (model.timeModeTime){
        	 time = new Date(model.timeModeTime)
        	 $scope.orderInfo.reg_time = time.getTime() / 1000 + 16 * 3600 - 60; //初始化时间到选中日期的23:59
        }
        var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
        var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        $scope.reg_time = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
        $scope.orderInfo.reg_time = time.getTime() / 1000;



		$scope.quit = function (){
			PageService.closeDialog(model.Dialog)
		}

		$scope.changeCost = function (){
			if ($scope.orderInfo.income == '') {
				$scope.orderInfo.income = 0;
			}
			if($scope.orderInfo.income < 0){
				$scope.orderInfo.income = 0 - $scope.orderInfo.income
			}
			if ($scope.orderInfo.income > model.money_pit) {
				$scope.orderInfo.income = model.money_pit
			}
			$scope.balance = model.money_pit - $scope.orderInfo.income;
		}
		/**
		 * 	创建单据
		 */
		$scope.createOrder = function (){
			NetworkService.request('createFinanceOrder',$scope.orderInfo,function (data){
				PageService.showSharedToast('创建成功！');
				PageService.closeDialog(model.Dialog)
			})
		}
		$(function (){
			$("#datepicker1").datepicker({
				showAnim : 'slide',
                onSelect: function (timeStr) {
                    $scope.orderInfo.reg_time = new Date(timeStr).getTime() /1000 ;
                    $log.debug('datepicker1',new Date(timeStr).getTime() /1000 );

                    //当前小时 分 秒 判断选择日期是否为今天？返回当前时间 ：返回选择日期的23:59
                    var Hours=new Date().getHours(),
                    Minutes=new Date().getMinutes(),
                    Seconds=new Date().getSeconds();
                	if($scope.orderInfo.reg_time-8*3600 == parseInt(new Date()/1000)-Hours*3600-Minutes*60-Seconds){
                    	$scope.orderInfo.reg_time = new Date().getTime() /1000;
                    	$log.debug("testnow:",$scope.orderInfo.reg_time,new Date($scope.orderInfo.reg_time*1000));
                    }else{
                    	$scope.orderInfo.reg_time = $scope.orderInfo.reg_time + 16 * 3600 - 60 ;
                    	$log.debug("testnow:",$scope.orderInfo.reg_time,new Date($scope.orderInfo.reg_time*1000));
                    }
                },
                showButtonPanel: true,
            })
		})
	}
])