//SubmitTabController
'use strict';

angular.module('XY').controller('SubmitTabController', ['EventService', '$scope', '$log', 'OrderService','LockService', 'PageService', 'SalesOrderModel',
    function(EventService, $scope, $log, OrderService, LockService, PageService, SalesOrderModel) {
    	// 初始化开单显示时间
        var time = new Date();
        var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
        var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        $scope.reg_time = time.getFullYear() + '-' + month + '-' + day; 
        // 使用jqUI
        $(function () {
            $("#datepicker1").datepicker({
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

        });
        //初始开单方式
		$scope.isPrint = SalesOrderModel.isPrint;
        $scope.createOrderItem = SalesOrderModel.isPrint == true ? '开单并打印' : '开单';
		$scope.createOrderCheckBoxIsShow = false;
        $scope.reopen = SalesOrderModel.reopen;
        $scope.isDelete = SalesOrderModel.isDelete;
		//选择开单
		$scope.createOrderCheck = function (isTrue) {
			//换选项
			if (isTrue) {
				$scope.createOrderItem = '开单并打印';
				$scope.createOrderCheckBoxIsShow = false;
                $scope.isPrint = true;
				SalesOrderModel.isPrint = true;
            	SalesOrderModel.setOptionArray();
			}else{
				$scope.createOrderItem = '开单';
				$scope.createOrderCheckBoxIsShow = false;
                $scope.isPrint = false;
				SalesOrderModel.isPrint = false;
            	SalesOrderModel.setOptionArray();
			}
		}
		//判断选项框是否弹出
		$scope.CheckBoxIsShow = function () {
			if ($scope.createOrderCheckBoxIsShow) {
				$scope.createOrderCheckBoxIsShow = false;
			}else{
				$scope.createOrderCheckBoxIsShow = true;
			}
		}


    	// 创建订单 flag_Print 为是否打印
    	$scope.createOrder = function (flag_Print) {
            LockService.getLockShopStatus(1, function () {
                // $scope.isCreating = true;
                PageService.showConfirmDialog('开单中...');

                if ($scope.orderInfo.company.cid == undefined) {
                    if ($scope.orderInfo._class == 1 || $scope.orderInfo._class == 2) {
                        var companyClass = '客户';
                    } else {
                        var companyClass = '供货商';
                    }
                    PageService.showConfirmDialog('<div class="title"><a class="red">' + companyClass + '信息错误</a></div>'
                    + '<div><a>可能是您直接在输入框中手动输入了不存在的<span class="red">' + companyClass + '名称</span>所导致。您可以通过以下三种中的任意一种方法解决：</a></div>'
                    + '<div><a>1.选择<span class="red">' + companyClass + '信息</span>下拉列表中已有的' + companyClass + '名称，而不是手动输入。</a></div>'
                    + '<div><a>2.手动输入<span class="red">' + companyClass + '信息</span>下拉列表中已经存在的' + companyClass + '名称。</a></div>'
                    + '<div><a>3.如果该类别确实是新的往来单位，请点击<span class="red">' + companyClass + '信息</span>下拉列表中的<span class="red">增加新单位</span>按钮新建。</a></div>');
                    return;
                }

                if(!$scope.orderInfo.mobile)delete $scope.orderInfo.mobile;
                if(!$scope.orderInfo.car_license)delete $scope.orderInfo.car_license;

                //格式化日期至时间戳
                // $scope.orderInfo.reg_time = new Date($scope.reg_time).getTime() /1000 ;
                // $scope.orderInfo.reg_time.setHours(23,59,59,999);
                $log.debug('$scope.orderInfo2',$scope.orderInfo.reg_time);
                OrderService.createOrder($scope.orderInfo,flag_Print, function () {
                    // $scope.isCreating = false;
                    PageService.closeConfirmDialog();

                    //原单据作废
                    if ($scope.isDelete) {
                        OrderService.setOrderStatus({
                            oid:$scope.orderInfo.oid,
                            status:3
                        })
                    }   
                });
            });
        }


    	$scope.submitIsShow = false;
    	//
    	$scope.submitBtnIsShow = function(){
    		$log.log('点击了开单选项框')
    		if ($scope.submitIsShow) {
    			$scope.submitIsShow = false;
    		}else{
    			$scope.submitIsShow = true;
    		}
    	}

    }
]);

