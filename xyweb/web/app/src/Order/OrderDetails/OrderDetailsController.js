//OrderDetailsController
'use strict'

angular.module('XY').controller('OrderDetailsController', ['EventService', '$scope', '$log', 'OrderDetailsModel','OrderService','CstmrService','$timeout','PageService','UserService','PrintService',
    function(EventService, $scope, $log, model, OrderService,CstmrService,$timeout,PageService,UserService,PrintService) {

    	$scope.model = model;
    	$scope.orderInfo = model.orderInfo;
        $scope.rpg = UserService.getLoginStatus().rpg;
    	$scope.isHistoryShow = false;
        $scope.isShowMore = false; //更多按钮
        // $log.error('order',$scope.orderInfo)
		$(function(){
			//页面加载改变div大小
			windowInit();
		});
		//窗口改变改变div大小
		window.onresize = function(){
            windowInit();
        };
		//改变大小函数
		function windowInit(){
			var width = $(window).width() + 20;
			var height = $(window).height() + 20;
			$("#order-details-page").css({'width': width,'height': height});
		};

        // 建立对联系人信息的映射
        $scope.contactList = CstmrService.contact.getContactList();
        // 映射停车位置
        $scope.parkAddressList = CstmrService.parkAddress.getParkAddressList();

        $scope.isTestShow = true;

    	// 0:不可修改状态
    	// 1:可修改备注
    	$scope.pageState = {
            isEditing : false,
            isContactListShow :false,
            isContactPhonenumListShow :false,
            isCarLisenseListShow : false,
            isParkAddressListShow : false,
       };

        $scope.clickContactNameInput = function() {
            if ($scope.pageState.isEditing) {
                $scope.pageState.isContactListShow = true;
            }
        };
        $scope.clickContactPhoneNumInput = function() {
            if ($scope.pageState.isEditing) {
                $scope.pageState.isContactPhonenumListShow = true;
            }
        };
        $scope.clickCarLisenseInput = function() {
            if ($scope.pageState.isEditing) {
                $scope.pageState.isCarLisenseListShow = true;
            }
        };
        $scope.clickParkAddressInput = function() {
            if ($scope.pageState.isEditing) {
                $scope.pageState.isParkAddressListShow = true;
            }
        };


        // 点击列表中的联系人
        $scope.clickContact = function(contact) {
            angular.copy(contact,model.selectedContact)
        };

        //当发生点击body事件，判断是否点发生在客户姓名输入框
        var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function(angularEvent, domEvent) {

            $timeout(function() {
                if ( !($(domEvent.target).hasClass('contact-list')) ) {
                    $scope.pageState.isContactListShow = false
                }

                // 关闭联系人电话自动完成列表
                if ( !($(domEvent.target).hasClass('contact-phonenum-list')) ) {
                    $scope.pageState.isContactPhonenumListShow = false
                }

                // 关闭停车位置自动完成列表
                if ( !($(domEvent.target).hasClass('contact-license-list')) ) {
                    $scope.pageState.isCarLisenseListShow = false
                }

                // 关闭停车位置自动完成列表
                if ( !($(domEvent.target).hasClass('parkaddress-list')) ) {
                    $scope.pageState.isParkAddressListShow = false
                }
            })

        });

        /**
        *   备注失去焦点自动保存
        */
    	$scope.clickModify = function() {
			$scope.pageState.isEditing = false;//取消备注高亮显示
            //发送编辑参数
            var dataToSend = {
                oid:$scope.orderInfo.oid,
                remark: $scope.orderInfo.remark,
            }
            //发送编辑请求
            OrderService.editOrder(dataToSend, function () {
                model.refreshOrderInfo();
                //成功后执行 重置orderinfo到最新状态
            })
    	}

        // 标记为异常,弹出标记异常对话框
        $scope.clickMarkException = function() {
            model.exceptionDialogId = PageService.showDialog('OrderDetailsExceptionPrompt')
        };
        // 标记为完成
        $scope.clickFinish = function() {
            PageService.showConfirmDialog('确认标记为已完成吗？',[],function(){
                $scope.orderInfo.status = 1;
                OrderService.setOrderStatus($scope.orderInfo, function () {
                model.refreshOrderInfo();
            })
            });

        }
        // 通知库管
        $scope.clickNotify = function() {
            $scope.orderInfo.status = 6;
            OrderService.setOrderStatus($scope.orderInfo, function () {
                model.refreshOrderInfo();
            })
        }
        // 操作日志的隐藏和显示
    	$scope.clickHistory = function() {
    		$scope.isHistoryShow = !$scope.isHistoryShow;
            PageService.showHistoryDialog($scope.orderInfo.history,['确认'])
    	};


        //点击退出按钮
        $scope.quit = function() {
            //关闭窗口前检查是否修改
            if (model.orderInfoBak.remark != $scope.orderInfo.remark){
                //修改弹窗提示
                PageService.showConfirmDialog('订单修改未保存，确认关闭么？',[],function(){
                    //确认后不保存关闭
                    PageService.closeDialog(model.dialogId);
                    //库存详情进入的没有orderargs
                    if(model.orderArgs == undefined){
                        return;
                    }
                    if (model.orderArgs.orderPage != undefined) {
                        switch (model.orderArgs.orderPage) {
                            case 1:
                                PageService.closeDialog();
                                EventService.emit(EventService.ev.VIEW_BACK_TO_ORDER);
                                // EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
                                break;
                            case 2:
                                PageService.closeDialog();
                                EventService.emit(EventService.ev.VIEW_BACK_TO_ORDER);
                                // EventService.emit(EventService.ev.START_VIEW_ALL_ORDER);
                                break;
                            case 3:
                                EventService.emit(EventService.ev.VIEW_BACK_TO_COMPANY);
                                PageService.closeDialog();
                                // EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,model.orderArgs);
                                break;
                            case 4:
                                PageService.closeDialog();
                                EventService.emit(EventService.ev.VIEW_BACK_TO_DRAFT,model.orderArgs.draftTab);
                                // EventService.emit(EventService.ev.START_VIEW_DRAFT,model.orderArgs.draftTab);
                                break;
                            default:
                                break;
                        }
                    }
                })
            }else{
                PageService.closeDialog(model.dialogId);
                //库存详情进入的没有orderargs
                if(model.orderArgs == undefined){
                    return;
                }
                if (model.orderArgs.orderPage) {
                    switch (model.orderArgs.orderPage) {
                        case 1:
                            PageService.closeDialog();
                            EventService.emit(EventService.ev.VIEW_BACK_TO_ORDER);
                            // EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
                            break;
                        case 2:
                            PageService.closeDialog();
                            EventService.emit(EventService.ev.VIEW_BACK_TO_ORDER);
                            // EventService.emit(EventService.ev.START_VIEW_ALL_ORDER);
                            break;
                        case 3:
                            EventService.emit(EventService.ev.VIEW_BACK_TO_COMPANY);
                            PageService.closeDialog();
                            // EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,model.orderArgs);
                            break;
                        case 4:
                            PageService.closeDialog();
                            EventService.emit(EventService.ev.VIEW_BACK_TO_DRAFT,model.orderArgs.draftTab);
                            // EventService.emit(EventService.ev.START_VIEW_DRAFT,model.orderArgs.draftTab);
                            break;
                        default:
                            break;
                    }
                }
            }

        }

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
                $scope.$apply(function () {
                    $scope.quit();
                })
            }
        });

        $scope.$on('$destroy', function () {
            keydownHandle();
        });

        //打印单据
        $scope.print = function (pagetype) {
            EventService.emit(EventService.ev.PRINT_ORDER,{class:$scope.orderInfo.class,oid:$scope.orderInfo.oid,isPreview:true,pageType:pagetype});
        };

        //删除单据
        $scope.clickDelete = function() {
            PageService.showConfirmDialog('确定作废吗？',['确定','取消'],function(){
                //点击确定（第一个按钮）时调用的回调函数
                OrderService.setOrderStatus(
                    {oid:$scope.orderInfo.oid,status:3},
                    function () {
                        $scope.orderInfo.status = 3;
                })

            });

        };

        //单据重开或者复制
		$scope.reopen = function (isTrue){
			if (isTrue) {
				var tip = '重开一张单据, 作废原先单据，确认继续？';
                $scope.orderInfo.importWay = "reOpen";//重开单据设置 ，为了重开成功时作废原单据
			}else{
				var tip = '复制一张单据，原先单据不会改变，确认继续？'
			}
			PageService.showConfirmDialog(tip,['确定','取消'],function(){
			    $log.error($scope.orderInfo);
				EventService.emit(EventService.ev.START_REOPEN_ORDER,$scope.orderInfo);
                PageService.closeDialog(model.dialogId);
			});
		};

        //打开库存详情
        $scope.skuDetail = function(item){
            PageService.closeDialog(model.dialogId);
            EventService.emit(EventService.ev.START_CREATE_SkuDetail,item);
        }
    }
]);

