// 订单信息类
'use strict';

xy.factory('OrderInfoClassService', ['$rootScope', '$log', 'CartClassService',
    function($rootScope, $log, CartClassService) {

        // $log.debug('OrderInfoClassService init')

        var OrderInfoClassService = {};

        // 单据类别 _class
        //  * 1.销售单
        //  * 2.销售退货单
        //  * 3.采购单
        //  * 4.采购退货单

        // 订单信息原型
        function OrderProto() {
            this.OrderProtoCtor = function() {
                // 本订单中本店是作为买家还是卖家
                this.isSeller = true;
                //单据类别
                this._class = 0;
                //订单状态
                this.status = 4;

                // 创建时间
                this.reg_time = undefined;

                //购物车
                this.cartAgent = CartClassService.newCartAgent()


                // 往来单位-----------------------------------------------
                this.company = {};


                // 联系人信息---------------------------------------------
                this.contact_name;
                this.car_license = "";
                this.mobile = "";
                this.parkAddress = "";
                this.park_address = "";


                //交易信息------------------------------------------------
                //货物价值(放在购物车里了)
                // this.cartAgent.totPrice

                // 优惠金额
                this.off = 0;
                // 本次应收
                this.receivableThisTime = 0;
                // 本次应付
                this.payableThisTime = 0;
                // 本次实收
                this.actualPayment = 0;
                // 本次实付
                this.actualReceipt = 0;
                //本次结余
                this.balanceThisTime = 0;
                //总结余
                this.balanceAfterPaying = 0;
                this.cash = 0;
                this.bank = 0;
                this.online_pay = 0;
                this.remark = "";

                // 异常编号
                this.exceptionNo = 0;
                this.exception = "";
            };

            // 设定客户为某客户, 如果传入是undefined就当是取消选定了
            this.setCompany = function(company) {
                angular.copy(company, this.company);
            };

            // 设定联系人
            this.setContact = function(contact) {
                this.contact_name = contact.contact_name;
                this.mobile = contact.phonenum == undefined ? '' : contact.phonenum[0].mobile;
                this.car_license = contact.car_license == undefined ? '' : contact.car_license[0].car_license;
            };
            this.setWarehouse=function (warehouse) {
                this.sto_id = warehouse.sto_id;
                this.warehouse_remark = warehouse.warehouse_remark;
                this.sto_name  = warehouse.sto_name;
            };

            this.addContact = function() {
                this.contact_name = '';
                this.car_license = '';
                this.mobile = '';
            };

            this.setPhonenum = function(mobile) {
                this.mobile = mobile;
            };

            this.setCarLicense = function(car_license) {
                this.car_license = car_license;
            }

            this.setParkAddress = function(park_address) {
                this.park_address = park_address;
            }

            // 本店作为卖家时(销售，采购退货)使用的计算金额方法,区别在应收(应付)和本次结余的计算
            this.calcNumberSeller = function() {
                // 应收金额 = 货物总价 - 优惠金额
                this.receivableThisTime = Number(this.cartAgent.totPrice) - Number(this.off);
                // 实收 = 现金 + 银行 + 网络
                this.actualReceipt = Number(this.cash) + Number(this.bank) + Number(this.online_pay);
                // 本次结余 = 实收 - 应收
                this.balanceThisTime = Number(this.actualReceipt) - Number(this.receivableThisTime);
                // 如果是已有客户, 总结余 = 此前结余 + 本次结余
                if (this.company.cid && (this.company.cid > 0)) {
                    this.balanceAfterPaying = (Number(this.company.balance) + Number(this.balanceThisTime)).toFixed(2)
                }
            }

            // 本店作为买家时(采购，销售退货)使用的计算金额方法
            this.calcNumberBuyer = function() {
                // 应付金额 = 货物总价 - 优惠金额
                this.payableThisTime = Number(this.cartAgent.totPrice) - Number(this.off);
                // 实付款 = 现金 + 银行 + 网络
                this.actualPayment = Number(this.cash) + Number(this.bank) + Number(this.online_pay);
                // 本次结余 =  应付 - 实付
                this.balanceThisTime = Number(this.payableThisTime) - Number(this.actualPayment);
                // 如果是已有客户, 总结余 = 此前结余 + 本次结余
                if (this.company.cid && (this.company.cid > 0)) {
                    this.balanceAfterPaying = (Number(this.company.balance) + Number(this.balanceThisTime)).toFixed(2)
                }
            }
        }

        // 销售订单
        function SalesOrder() {
            this.SalesOrderCtor = function() {
                this.OrderProtoCtor();
                this._class = 1;
                this.calcNumber = this.calcNumberSeller;
                this.isSeller = true;
            }
        }
        // 继承订单基类
        SalesOrder.prototype = new OrderProto();

        // 销售退货单
        function SalesReturnOrder() {
            this.SalesReturnOrderCtor = function() {
                this.OrderProtoCtor();
                this._class = 2;
                this.calcNumber = this.calcNumberBuyer;
                this.isSeller = false;
            }
        }
        SalesReturnOrder.prototype = new OrderProto();

        // 采购订单
        function SupplyOrder() {
            this.SupplyOrderCtor = function() {
                this.OrderProtoCtor();
                this._class = 3;
                this.calcNumber = this.calcNumberBuyer;
                this.isSeller = false;
            }
        }
        SupplyOrder.prototype = new OrderProto();

        // 采购退货单
        function SupplyReturnOrder() {
            this.SupplyReturnOrderCtor = function() {
                this.OrderProtoCtor();
                this._class = 4;
                this.calcNumber = this.calcNumberSeller;
                this.isSeller = true;
            }
        }
        SupplyReturnOrder.prototype = new OrderProto();

        //-------------------------------------------------------------------------------------------------------------------------------

        // new一个销售订单
        OrderInfoClassService.newSalesOrder = function() {
            var out = new SalesOrder();
            out.SalesOrderCtor();
            return out;
        }

        // new一个销售退货单
        OrderInfoClassService.newSalesReturnOrder = function() {
            var out = new SalesReturnOrder();
            out.SalesReturnOrderCtor();
            return out;
        }

        // new一个采购单
        OrderInfoClassService.newSupplyOrder = function() {
            var out = new SupplyOrder();
            out.SupplyOrderCtor();
            return out;
        }

        // new一个采购退货单
        OrderInfoClassService.newSupplyReturnOrder = function() {
            var out = new SupplyReturnOrder();
            out.SupplyReturnOrderCtor();
            return out;
        };

        // 把上面4个新建订单接口封装到一起，根据参数new订单,方便Model调用
        // param _class int 订单类型
        OrderInfoClassService.newOrder = function(_class) {
            // $log.log('OrderInfoClassService.newOrder _class: ',_class);
            var out;
            _class = Number(_class);
            switch (_class) {
                case 1: out = OrderInfoClassService.newSalesOrder();
                    break;
                case 2: out = OrderInfoClassService.newSalesReturnOrder();
                    break;
                case 3: out = OrderInfoClassService.newSupplyOrder();;
                    break;
                case 4: out = OrderInfoClassService.newSupplyReturnOrder();
                    break;
                case 5: out = OrderInfoClassService.newSalesOrder();
                    break;
                case 6: out = OrderInfoClassService.newSalesOrder();
                    break;
                default:;
            }
            // $log.log('OrderInfoClassService.newOrder out: ',out);
            return out;
        };

        // 用Api OrderQueryOne的返回信息生成一个order对象
        OrderInfoClassService.newOrderFromApiOrderQueryOne = function(data) {
            var out;
            $log.log('newOrderFromApiOrderQueryOne data: ',data );
            out = OrderInfoClassService.newOrder(data.class);
            // $log.log('newOrderFromApiOrderQueryOne out: ',out);
            out.status = data.status;

            out.sn = data.sn;
            out.class = data.class;
            out.oid = data.oid;
            out.operator_name = data.operator_name;
            out.sto_name = data.sto_name;
            out.sto_id = data.sto_id;
            //这个是给订单详情用的?
            out.cid = data.cid;
            out.company_name = data.cid_name;

            out.contact_name = data.contact_name;
            out.car_license = data.car_license;
            out.mobile = data.mobile;
            out.park_address = data.park_address;
            out.off = data.off;
            out.receivableThisTime = data.receivable;
            out.payableThisTime = data.receivable;// 应收应付服务器都用receivable了 ?
            out.receivable = data.receivable;
            out.actualPayment = data.income;
            out.actualReceipt = data.income// 实收实付api都用income了 ?
            out.income = data.income;
            out.balanceThisTime = data.balance;
            out.history_balance = data.history_balance;
            out.history = data.history;// 订单操作日志
            out.total_balance = data.total_balance;
            // out.balanceThisTime = ???
            out.value = data.value// 这是货物总价？
            out.cash = data.cash;
            out.bank = data.bank;
            out.online_pay = data.online_pay;
            out.remark = data.remark;
            out.remain = data.remain;
            // 创建时间
            out.reg_time = data.reg_time;
            out.update_time = data.update_time;
            out.leave_time = data.leave_time;
            out.exception = data.exception;
            out.exceptionNo = data.exceptionNo;
            out.freight = data.freight;
            out.warehouse_remark = data.warehouse_remark;
            // 装填购物车信息
            out.cartAgent = CartClassService.newCartAgent();
            out.cartAgent.fillWithApiData(data.cart);
            // 把运费装填进购物车
            out.cartAgent.freight = parseFloat(data.freight);
            
            out.is_calculated = parseFloat(data.is_calculated);
            //给继续开单用的往来单位信息
            out.company = {
                cid:Number(data.cid),
                name:data.cid_name,
            }

            // $log.error('out:',out);
            // $log.log('out.company: ',out.company);

            // $log.log('newOrderFromApiOrderQueryOne out..: ', out);
            return out;
        }

        //盘点单类
        function StockTakingOrder() {
            this.StockTakingOrderCtor = function() {
                this.check_uid = undefined;
                this.cartAgent = CartClassService.newStockTakingCartAgent();
                this.remark = '';
                this.class = 53
            };

            // 装填从服务器获取的数据
            this.copyFromApiData = function(data){
                this.cartAgent.fillWithApiData(data.cart);
                // $log.debug() // 盘点单装填出来的购物车
                this.admin_uid = data.admin_uid;
                this.check_name = data.check_name;
                this.check_uid = data.check_uid;
                this.class = data.class;
                this.history = data.history;
                this.operator_name = data.operator_name;
                this.operator_uid = data.operator_uid;
                this.reg_time = data.reg_time;
                this.remark = data.remark;
                this.update_time = data.update_time;
                this.sto_name = data.sto_name;
                this.sto_id = data.sto_id;
                this.value = data.value;
                this.num = data.num;
                this.wid = data.wid;
                this.sn = data.sn;
                this.cart = data.cart;
            }
        }
        // 新建一个盘点单对象
        OrderInfoClassService.newStockTakingOrder = function() {
            var out;
            out = new StockTakingOrder();
            out.StockTakingOrderCtor();
            return out;
        }

        OrderInfoClassService.newStockTakingOrderFromApiData = function(data) {
            var out;
            out = OrderInfoClassService.newStockTakingOrder();
            out.copyFromApiData(data);
            return out;
        }
        OrderInfoClassService.newRequisitionOrderFromApiData = function(data) {
            var out;
            out = OrderInfoClassService.newRequisitionOrder();
            out.copyFromApiData(data);
            return out;
        }

        //调拨单类
        function RequisitionOrder(){
            this.RequisitionOrderCtor = function(){
                this.class = 54;
                this.check_uid = undefined;
                this.cartAgent = CartClassService.newRequisitionCartAgent();
                this.remark = '';
            };
            this.copyFromApiData = function(data){
                this.cartAgent.fillWithApiData(data.cart);
                this.admin_uid = data.admin_uid;
                this.check_name = data.check_name;
                this.check_uid = data.check_uid;
                this.class = data.class;
                this.history = data.history;
                this.new_sto_id = data.new_sto_id;
                this.sto_name = data.sto_name;
                this.new_sto_name = data.new_sto_name;
                this.operator_name = data.operator_name;
                this.operator_uid = data.operator_uid;
                this.reg_time = data.reg_time;
                this.remark = data.remark;
                this.update_time = data.update_time;
                this.value = data.value;
                this.num = data.num;
                this.wid = data.wid;
                this.sn = data.sn;
                this.cart = data.cart;
                this.history = data.history;
            };
            this.setOldSto=function (item) {
                this.sto_id = item.sto_id
            };
            this.setNewSto = function (item) {
                this.new_sto_id = item.sto_id
            };
        }
        //创建一个调拨单对象
        OrderInfoClassService.newRequisitionOrder = function(){
            var out;
            out = new RequisitionOrder();
            out.RequisitionOrderCtor();
            return  out;
        };

        return OrderInfoClassService; // or return xxxxService

    }
]);
