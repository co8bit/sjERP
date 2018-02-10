'use strict';

//订单服务
xy.factory('OrderService', ['$rootScope', 'EventService', '$log', 'NetworkService', 'OrderInfoClassService', 'MiscService', 'PageService', 'PrintService',
    function ($rootScope, EventService, $log, NetworkService, OrderInfoClassService, MiscService, PageService, PrintService) {

        var OrderService = {};

        // 二维数组
        // 第一维是类别，第二维是订单数组 
        var orderList = [];


        /**
         * 新建订单
         * @param unsigned_int $class 单据类别
         * @param unsigned_int $cid 公司cid
         * @param string $name 单位名称
         * @param string $mobile 手机
         * @param string $park_address 停车位置
         * @param string $car_license 车牌号
         * @param double $off 优惠
         * @param double $cash 现金
         * @param double $bank 银行
         * @param string $remark 备注
         * @param 4||5||6||7||11||12 $status 状态
         * @param json $cart 购物车内容，购物车格式如下：
         * @cart json:
         *     mini:{"data":[{"sku_id":1,"spu_name":"三黄鸡","spec_name":"10只","quantity":10,"unitPrice":123},{"sku_id":2,"spu_name":"三黄鸡","spec_name":"15只","quantity":20,"unitPrice":223}]}
         *     展开:
         *        {
         *            "data":
         *            [
         *                {
         *                    "sku_id":1,
         *                    "spu_name":"三黄鸡",
         *                    "spec_name":"10只",
         *                    "quantity":10,
         *                    "unitPrice":123
         *                },
         *                {
         *                    "sku_id":2,
         *                    "spu_name":"三黄鸡",
         *                    "spec_name":"15只",
         *                    "quantity":20,
         *                    "unitPrice":223
         *                }
         *            ]
         *        }
         *
         * @return unsigned_int 成功返回oid > 0
         * @return <=0 错误编码，查看全局错误编码信息
         */

        // * @param unsigned_int $class 单据类别
        // * @param unsigned_int $cid 公司cid
        // * @param string $name 单位名称
        // * @param string $mobile 手机
        // * @param string $park_address 停车位置
        // * @param string $car_license 车牌号
        // * @param double $off 优惠
        // * @param double $cash 现金
        // * @param double $bank 银行
        // * @param string $remark 备注
        // * @param 4||5||6||7||11||12 $status 状态
        // * @param json $cart 购物车内容，购物车格式如下：
        // * @cart json:

        //未启用
        OrderService.getOrderList = function () {
            return orderList
        }

        function fillOrder(tarArr, srcArr) {
            // 判断这次更新的列表里有没有某类订单
            var classExists = [];
            var orderClass = 0;
            for (var i = 0; i < srcArr.length; i++) {
                orderClass = srcArr[i].class;
                // 遇到本次更新的第一个某类型订单，清空原有的该类型订单
                if (classExists[orderClass]) {

                } else {
                    orderList[orderClass] = [];
                    classExists[orderClass] = 1
                }
                var tmpItem = {};
                angular.copy(srcArr[i], tmpItem);
                orderList[orderClass].push(tmpItem)
            }
        }


        // var salesOrderList = []
        // var salesRefundOrderList = []
        // var supplyOrderList = []1
        // var supplyRefundOrderList = []

        // 生成符合API格式的购物车信息
        // param  {}     orderInfo  订单对象
        // return string            购物车信息,用于装填到 dataToSend.cart
        function genCartStrForAPI(orderInfo) {
            var cart_arr = [];
            var cartItemList = orderInfo.cartAgent.cartItemList;
            var tar_item;
            var src_item;
            for (var v in cartItemList) {
                src_item = cartItemList[v];
                tar_item = {
                    sku_id: src_item.sku_id,
                    spu_name: src_item.spu_name,
                    spec_name: src_item.spec_name,
                    quantity: src_item.quantity,
                    unitPrice: src_item.unit_price,
                    comment: src_item.comment
                };
                cart_arr.push(tar_item)
            }

            var cart_data = {
                data: cart_arr,
            };
            var cart_str = JSON.stringify(cart_data);

            return cart_str
        }

        // 把订单信息装填到用于API调用的对象
        // param  {}  orderInfo 订单对象
        // param  int mark      标记 1=草稿
        // return {}            用于API调用的数据
        function genDataToSend(orderInfo, mark) {
            var dataToSend = {};
            // var now = parseInt(new Date().getTime() / 1000);
            if ((orderInfo._class >= 0) && (orderInfo._class <= 4)) {
                dataToSend = {
                    sto_id:orderInfo.sto_id,
                    oid: orderInfo.oid,
                    class: orderInfo._class,
                    cid: orderInfo.company.cid,
                    company_name: orderInfo.company.name,
                    contact_name: orderInfo.contact_name,
                    warehouse_remark: orderInfo.contact_remark,
                    mobile: orderInfo.mobile,
                    park_address: orderInfo.park_address,
                    car_license: orderInfo.car_license,
                    off: parseFloat(orderInfo.off == '' ? 0 : orderInfo.off),
                    cash: parseFloat(orderInfo.cash == '' ? 0 : orderInfo.cash),
                    bank: parseFloat(orderInfo.bank == '' ? 0 : orderInfo.bank),
                    online_pay: parseFloat(orderInfo.online_pay == '' ? 0 : orderInfo.online_pay),
                    remark: orderInfo.remark,
                    status: orderInfo.status,
                    reg_time: orderInfo.reg_time,
                    freight: orderInfo.freight == '' ? 0 : orderInfo.freight,
                    reopen: orderInfo.reopen,
                    isDelete: orderInfo.isDelete,
                    is_calculated : orderInfo.is_calculated
                    // reg_time: now
                }

                if (orderInfo.freight_cal_method) {
                    dataToSend.freight_cal_method = orderInfo.freight_cal_method;
                }

            }
            // 把购物车数据转成字符串
            var cart_str = genCartStrForAPI(orderInfo);
            // 装填购物车信息
            dataToSend.cart = cart_str;
            return dataToSend;
        }

        // 创建订单
        // param int 单据类别 1,2,4,5 销售，退货，采购，采购退货
        OrderService.createOrder = function (orderInfo, flag_Print, callback) {
            var dataToSend = genDataToSend(orderInfo);
            $log.debug('createOrder dataToSend: ', dataToSend);

            NetworkService.request('createOrder', dataToSend, function (data) {
                    if (flag_Print)
                        EventService.emit(EventService.ev.PRINT_ORDER, {oid: data.data, isPreview: true, pageType: 'A4'});

                    EventService.emit(EventService.ev.CREATE_ORDER_SUCCESS, orderInfo._class);
                    if (orderInfo.isToday === true) {
                        EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
                    }
                    else {
                        EventService.emit(EventService.ev.START_VIEW_ALL_ORDER,orderInfo.reg_time);
                    }
                    if (callback) {
                        callback();
                    }
                }, function (data) {
                    var errorInfo = {};
                    if (data) {
                        errorInfo = {
                            _class: orderInfo._class,
                            EC: data.EC,
                            data: data.data,
                        }
                    }
                    EventService.emit(EventService.ev.CREATE_ORDER_ERROR, errorInfo);
                    if (callback) {
                        callback()
                    }
                }, 1
            );
        };

// 创建订单草稿
        OrderService.createDraft = function (orderInfo) {
            var dataToSend = genDataToSend(orderInfo);
            NetworkService.request('order_createDraft', dataToSend, function (data) {
                EventService.emit(EventService.ev.ORDER_CREATE_DRAFT_SUCCESS);
                EventService.emit(EventService.ev.START_VIEW_DRAFT, 1);
            }, function () {
                EventService.emit(EventService.ev.ORDER_CREATE_DRAFT_ERROR);
            }, 1);
        };

//创建报溢报损
        OrderService.createOverflowOrLoss = function (order) {
            var dataToSend = {
                class: order.orderType, // 报溢还是报损
                remark: order.remark,
                check_uid: order.checker.uid,
            };

            var cart_arr = genCartForAPI(order);
            $log.debug('genCartForAPI ', cart_arr);

            var cart_data = {
                data: cart_arr
            };

            //变成json字符串
            var str_cart = JSON.stringify(cart_data);
            $log.debug('str_cart: ', str_cart);


            dataToSend.cart = str_cart;


            $log.debug('dataToSend', dataToSend);
            NetworkService.request('createOverflowOrLoss', dataToSend, function (data) {
                $log.log(data)
            })
        };


// 查询订单,前四个 1 || 0,然后两个1970秒数
        OrderService.queryOrder = function (salesOrder, salesRefundOrder, supplyOrder, supplyRefundOrder, startTime, endTime) {
            var dataToSend = {
                mode: 0,
                class1: salesOrder,
                class2: salesRefundOrder,
                class3: supplyOrder,
                class4: supplyRefundOrder,
                startTime: startTime,
                endTime: endTime,
            };

            NetworkService.request('queryOrder', dataToSend, function (data) {
                $log.debug('queryOrder response: ', data);
                fillOrder(orderList, data.data);
                $log.debug('orderList: ', orderList)
            }, function () {

            })
        };

// 查询一个订单,如果查询成功,将查询结果作为参数回调
        OrderService.queryOneOrder = function (oid, callback) {
            var dataToSend = {
                oid: oid,
            };
            var orderInfo = OrderInfoClassService.newOrder();
            NetworkService.request('order_get_', dataToSend, function (data) {
                var tmpOrder = OrderInfoClassService.newOrderFromApiOrderQueryOne(data.data);
                $log.log('queryOneOrder tmpOrder: ', tmpOrder);
                MiscService.genShowContent([tmpOrder]);

                tmpOrder = MiscService.formatMoneyMisc(tmpOrder, ['value', 'bank', 'cash', 'off', 'unit_price', 'remain', 'pile_price', 'receivableThisTime', 'payableThisTime', 'actualReceipt', 'actualPayment', 'online_pay', 'balanceThisTime', 'history_balance', 'total_balance']);
                tmpOrder.cartAgent.cartItemList = MiscService.formatMoneyObject(tmpOrder.cartAgent.cartItemList, ['unit_price', 'pile_price', 'value', 'bank', 'cash', 'off']);

                if (callback) {
                    callback(tmpOrder);
                }
            }, function () {

            }, 1);
        };

// 编辑订单 这里直接传入dataToSend
        OrderService.editOrder = function (dataToSend, callback) {
           if(dataToSend.wid){
               NetworkService.request('warehouse_edit_', dataToSend, function (data) {
                   if (callback) {
                       callback();
                   }
               }, function () {

               }, 1);
           }
          if(dataToSend.oid){
              NetworkService.request('editOrder', dataToSend, function (data) {
                  if (callback) {
                      callback();
                  }
              }, function () {

              }, 1);
          }
          if(dataToSend.fid){
              NetworkService.request('editFinanceOrder', dataToSend, function (data) {
                  if (callback) {
                      callback();
                  }
              }, function () {

              }, 1);
          }
        };

// 设置订单状态
        OrderService.setOrderStatus = function (orderInfo, callback_success, callback_error) {
            var dataToSend = {
                oid: orderInfo.oid,
                status: orderInfo.status,
                remark: orderInfo.remark,
                exceptionNo: orderInfo.exceptionNo,
                exception: orderInfo.exception,
            };
            NetworkService.request('setOrderStatus', dataToSend, function () {
                if (callback_success) {
                    callback_success();
                }
            }, callback_error);
        };
        return OrderService

    }
])
;
