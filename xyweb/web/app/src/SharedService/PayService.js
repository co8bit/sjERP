'use strict'

/**
 * 支付服务
 */
xy.factory('PayService', ['$log', 'UserService', 'EventService', 'NetworkService',
    function ($log, UserService, EventService, NetworkService) {

        var PayService = {};

        var bcPayParam = {};

        function setBcPayParam(param) {
            bcPayParam.title = param.bill_title;
            bcPayParam.amount = param.bill_money;
            bcPayParam.out_trade_no = param.sn; //唯一订单号
            bcPayParam.sign = param.sign;
            // bcPayParam.debug = true;

            var url = window.location.href;
            $log.debug('url:',url);
            if (url.search('xyweb') > -1) {
                var index = url.search('xyweb');
                bcPayParam.return_url = url.substring(0, index) + 'xyweb/';
            } else if (url.search('app') > -1) {
                var index = url.search('app');
                bcPayParam.return_url = url.substring(0, index) + 'app/';
            }
            $log.debug('return_url:',bcPayParam.return_url);
        }

        function bcPay() {
            BC.click(bcPayParam);
        }
        // 这里不直接调用BC.click的原因是防止用户点击过快，BC的JS还没加载完成就点击了支付按钮。
        // 实际使用过程中，如果用户不可能在页面加载过程中立刻点击支付按钮，就没有必要利用asyncPay的方式，而是可以直接调用BC.click。
        function asyncPay() {
            if (typeof BC == "undefined") {
                if (document.addEventListener) { // 大部分浏览器
                    document.addEventListener('beecloud:onready', bcPay, false);
                } else if (document.attachEvent) { // 兼容IE 11之前的版本
                    document.attachEvent('beecloud:onready', bcPay);
                }
            } else {
                bcPay();
            }
        }

        PayService.pay = function (memberInfo, callback) {
            if (!memberInfo) {
                return;
            }
            if (memberInfo.type < 7) {
                var dataToSend = {
                    bill_class : memberInfo.type,
                    member_count : memberInfo.count,
                    update_time:Date.parse(new Date())/1000
            }
            } else if (memberInfo.type == 7) {
                var dataToSend = {
                    bill_class : memberInfo.type,
                    bill_money : memberInfo.money,
                    update_time:Date.parse(new Date())/1000
                }
            }

            NetworkService.request('Paybill_getPayBillParam', dataToSend, function (data) {
                var payBillParam = data.data;
                setBcPayParam(payBillParam);
                asyncPay();
                if (callback) {
                    callback();
                }
            },function () {

            });
        }

        return PayService;

    }
]);