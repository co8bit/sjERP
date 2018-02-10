//订单详情页Model
'use strict';

angular.module('XY').factory('OrderDetailsModel', ['EventService', '$log', 'PageService','OrderService','CstmrService',
    function(EventService, $log, PageService, OrderService,CstmrService) {

        var model = {};
        // 订单详情对话框id
        model.dialogId = undefined;
        // 标记异常弹出对话框id
        model.exceptionDialogId = undefined;

        model.orderInfo = {};

        model.selectedContact = {};

        // 编辑之前的备份
        model.orderInfoBak = {};

        model.refreshOrderInfo = function () {
            OrderService.queryOneOrder(model.oid,function(orderInfo){
                angular.copy(orderInfo, model.orderInfo);
                angular.copy(orderInfo, model.orderInfoBak);
            });
        };

        // 响应要求查看订单详情事件
        EventService.on(EventService.ev.ORDER_VIEW_DETAILS,function(evnet,args){

            // PageService.closeDialog();
            model.oid = args;
        	// 先查询要显示详情的订单，在回调中装填数据并显示页面
        	OrderService.queryOneOrder(args,function(orderInfo){
        		model.orderInfo = orderInfo;
        		// $log.error(model.orderInfo);
                angular.copy(orderInfo,model.orderInfoBak);
                $log.debug('orderInfo',orderInfo);
                // 显示界面
                if ((orderInfo.class == 5)||(orderInfo.class == 6)) {
                    model.dialogId = PageService.showDialog('ModifyIncomeOrExpenseDetails');
                }
                else
                    model.dialogId = PageService.showDialog('OrderDetails')
        	})
        });

        // 比较编辑后的orderInfo和编辑前的orderInfo，如果有变化则装填变化属性并请求编辑订单
        model.compareAndEditOrder = function(orderInfo) {

            var dataToSend = {
                oid:orderInfo.oid,
                remark: orderInfo.remark,
            };

            var flag_changed = false;

            if (model.orderInfoBak.contact_name.toString() != orderInfo.contact_name.toString()) {
                dataToSend.contact_name = orderInfo.contact_name;
                flag_changed = true;
                $log.log('contact_name')
            }

            if (model.orderInfoBak.mobile.toString() != orderInfo.mobile.toString()) {
                dataToSend.mobile = orderInfo.mobile;
                flag_changed = true;
                $log.log('mobile')
            }

            if (model.orderInfoBak.park_address.toString() != orderInfo.park_address.toString()) {
                dataToSend.park_address = orderInfo.park_address;
                flag_changed = true;
                $log.log('park_address')
            }

            if (model.orderInfoBak.car_license.toString() != orderInfo.car_license.toString()) {
                dataToSend.car_license = orderInfo.car_license;
                flag_changed = true;
                $log.log('car_license')
            }

            if (model.orderInfoBak.remark.toString() != orderInfo.remark.toString()) {
                dataToSend.remark = orderInfo.remark;
                flag_changed = true;
                $log.log('remark')
            }

            if (model.orderInfoBak.status.toString() != orderInfo.status.toString()) {
                dataToSend.status = orderInfo.status;
                flag_changed = true;
                $log.log('status')
                // if (orderInfo.status == 2) {
                //     dataToSend.exceptionNo = orderInfo.exceptionNo
                //     if (orderInfo.exceptionNo == 3) {
                //         dataToSend.exception = orderInfo.exception
                //     }
                // }
            }

            model.orderInfoBak = {};//保存修改后修改备份
            angular.copy(orderInfo, model.orderInfoBak);
            
            if (flag_changed) {
                $log.log('dataToSend: ',dataToSend);
                OrderService.editOrder(dataToSend, function () {
                    model.refreshOrderInfo();
                })
            } else {
                PageService.showSharedToast('没有变化')
            }

        };

        EventService.on(EventService.ev.editOrder,function(event,arg){
            /*if (arg.ec > 0) {
                PageService.showSharedToast('编辑订单成功')
                // EventService.emit(EventService.ev.ORDER_VIEW_DETAILS,model.orderInfo.oid)
            } else {
                PageService.showSharedToast('编辑订单失败')
            }*/

        });

        // 改变订单状态
        EventService.on(EventService.ev.setOrderStatus,function(event,arg){
            if (arg.ec > 0){
                PageService.showSharedToast('编辑订单成功')
                // EventService.emit(EventService.ev.ORDER_VIEW_DETAILS,model.orderInfo.oid)
            } else {
                PageService.showSharedToast('编辑订单失败')
            }
        });

        EventService.on(EventService.ev.ORDER_ARGS,function(evnet,args) {
            model.orderArgs = args;
        });

        return model // or return model

    }
])
