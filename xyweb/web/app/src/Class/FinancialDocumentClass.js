// * @param string $class 单据类别
// * @param unsigned_int $cid 单位cid
// * @param double $cash 现金
// * @param double $bank 银行
// * @param string $remark 备注
// FinanceInvoiceClass

// * 71.收款单
// * 72.付款单
// * 73.其他收入单
// * 74.费用单

'use strict';

angular.module('XY').factory('FinancialDocumentClass', ['EventService', '$log', 'PageService',
    function(EventService, $log, PageService) {

        var FinancialDocumentClass = {};

        function FinancialDocument() {
            this.FinancialDocumentCtor = function() {
                // api 使用了 class作为参数名,我们应该避开js保留字，所以加个下划线 http://www.w3schools.com/js/js_reserved.asp
                this._class = "";
                this._name = "";

                this.company = {};

                this.cash = '';
                this.bank = '';
                this.online_pay = '';

                // 实收金额
                this.actual = '';
                this.remark = '';

            }

            // 设定name参数 在不同单据中有不同意义
            this.setName = function(str) {
                this._name = str;
            }

            this.updateActual = function() {
                // this.cash = Number(this.cash.replace(/[^0-9 | \.]+/, ''));
                // this.bank = Number(this.bank.replace(/[^0-9 | \.]+/, ''));
                // this.online_pay = Number(this.online_pay.replace(/[^0-9 | \.]+/, ''));
                this.actual = (Number(this.cash) + Number(this.bank) + Number(this.online_pay)).toFixed(2);
            }

            // 设定往来单位
            this.setCompany = function(company) {
                angular.copy(company, this.company);
            }

            //从查询财务单返回值拷贝
            this.copyFromApiData = function(data) {

                this.admin_uid = data.admin_uid;
                this.bank = data.bank;
                this.cash = data.cash;
                this.cid = data.cid;
                this.cid_name = data.cid_name;
                this.class = data.class;
                this.fid = data.fid;
                this.history = data.history;
                this.income = data.income;
                this.name = data.name;
                this._name = data.name;
                this.online_pay = data.online_pay;
                this.operator_name = data.operator_name;
                this.reg_time = data.reg_time;
                this.remark = data.remark;
                this.update_time = data.update_time;
                this.sn = data.sn;

            }

        }


        // 收款单/付款单
        function ReceiptPayment() {
            this.ReceiptPaymentCtor = function() {
                this.FinancialDocumentCtor();
                //待付款/收款订单
                this.orderList = [];
                // 总代收代付金额
                this.totRemain = 0;

            }

            // 设定待付款/收款订单
            this.setOrderList = function(orderList) {
                //这里必须有个空行才可以，不然会错。
                angular.copy(orderList,this.orderList);
                this.updateTotRemain();
            }

            // 更新总代收代付金额
            this.updateTotRemain = function() {
                var tot = 0;
                for (var key in this.orderList) {
                    tot+=Number(this.orderList[key].remain);
                }
                // 如果是待付API返回的remain都是负的所以得取绝对值
                tot = Math.abs(tot);
                this.totRemain = tot;
            }

            // 重写了updateActual,相比费用单和收入单增加了自动分配每个代收代付订单金额的功能
            this.updateActual = function() {
                // this.cash = this.cash.replace(/[^0-9 | \.]+/, '');
                // this.bank = this.bank.replace(/[^0-9 | \.]+/, '');
                // this.online_pay = this.online_pay.replace(/[^0-9 | \.]+/, '');
                this.actual = (Number(this.cash) + Number(this.bank) + Number(this.online_pay)).toFixed(2);
                var toAllocate = this.actual;
                var remain = 0;
                for (var key in this.orderList) {
                    // 这个订单还差多少钱
                    remain = Math.abs(this.orderList[key].remain);
                    // 能付得起
                    if (toAllocate >= remain) {
                        this.orderList[key].money = remain;
                        toAllocate -= remain;
                    // 付不起，有多少付多少
                    } else {
                        this.orderList[key].money = toAllocate;
                        toAllocate -= toAllocate;
                    }
                }
            }

            // 重写基类方法，从查询财务单返回值拷贝
            this.copyFromApiData = function(data) {
                this.admin_uid = data.admin_uid;
                this.bank = data.bank;
                this.cash = data.cash;
                this.cid = data.cid;
                this.cid_name = data.cid_name;
                this.class = data.class;
                this.fid = data.fid;
                this.history = data.history;
                this.income = data.income;
                this.name = data.name;
                this._name = data.name;
                this.online_pay = data.online_pay;
                this.operator_name = data.operator_name;
                this.reg_time = data.reg_time;
                this.remark = data.remark;
                this.update_time = data.update_time;
                this.company = {
                    name:data.cid_name,
                    cid:data.cid,
                }
                //付款收款订单列表
                this.orderList = data.cart;
                this.sn = data.sn;
            }

        }
        ReceiptPayment.prototype = new FinancialDocument();


        // 新建一个其他收入单
        FinancialDocumentClass.newIncome = function() {
            var out = new FinancialDocument();
            out.FinancialDocumentCtor();
            // 设定单据类别
            out._class = 73;
            return out;
        }

        // 新建一个费用单
        FinancialDocumentClass.newExpense = function() {
            var out = new FinancialDocument();
            out.FinancialDocumentCtor();
            // 设定单据类别
            out._class = 74;
            return out;
        }

        // 新建收款单
        FinancialDocumentClass.newReceipt = function() {
            var out = new ReceiptPayment();
            out.ReceiptPaymentCtor();
            out._class = 71;
            return out;
        }



        // 新建付款单
        FinancialDocumentClass.newPayment = function() {
            var out = new ReceiptPayment();
            out.ReceiptPaymentCtor();
            out._class = 72;
            return out;
        }

        // 根据class新建财务单
        FinancialDocumentClass.newDoc = function(_class) {
            var out;
            _class = Number(_class);
            switch (_class) {
                case 73: out = FinancialDocumentClass.newIncome();
                    break;
                case 74: out = FinancialDocumentClass.newExpense();
                    break;
                case 71: out = FinancialDocumentClass.newReceipt();;
                    break;
                case 72: out = FinancialDocumentClass.newPayment();
                    break;                
                default:;
            }
            return out;
        }

        //继续草稿单
        FinancialDocumentClass.newDocFromApiOrderQueryOne = function(data) {
            var out;
            //新建单据
            out = FinancialDocumentClass.newDoc(data.class);
            //把传进来的信息添加入新单据内
            out.copyFromApiData(data);
            return out;

        }

        return FinancialDocumentClass; // or return model

    }
]);
