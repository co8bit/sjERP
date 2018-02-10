// StatementModel
'use strict';

angular.module('XY').factory('StatementModel', ['EventService', '$log', '$filter', 'PageService', 'NetworkService', '$rootScope', '$q',
    function (EventService, $log, $filter, PageService, NetworkService, $rootScope, $q) {

        var model = {};
        model.phone = "";
        /**
         * 发送短信对账单
         */
        model.sendStatementWithSMS = function (phoneStr, callback) {
            // $log.log('phoneStr:', phoneStr);
            var phoneArray = phoneStr.split(/[,，]/);
            for (var i = 0; i < phoneArray.length; i++) {
                phoneArray[i] = phoneArray[i].replace(/\s+/g, '');
                if(phoneArray[i].length<11) phoneArray[i]="";
            }
            // $log.log('phoneArray:', phoneArray);

            var dataToSend = {
                phoneArray: JSON.stringify(phoneArray),
                dateSt: model.data.dateSt,
                dateEnd: model.data.dateEnd,
                money: model.data.money,
                content: model.data.content,
                token: model.data.token,
                sign: model.data.sign
            }

            NetworkService.request('SmsDetails_sendSMSStatementOfAccount', dataToSend, function (data) {
                PageService.showConfirmDialog('发送成功!');
            }, function (data) {

            });
        }

        /**
         * 获取要发送的短信信息
         *
         * @param {Object} dataToSend
         */
        function requestStatement(dataToSend) {
            var defered = $q.defer();
            NetworkService.request('Company_requestStatementOfAccount', dataToSend, function (data) {
                model.data = data.data;
                var dateSt = $filter('date')(model.data.dateSt * 1000, 'yyyy-MM-dd');
                var dateEnd = $filter('date')(model.data.dateEnd * 1000, 'yyyy-MM-dd');
                model.data.sms_text_last = '【星云进销存对账单】您好，从' + dateSt + '到' + dateEnd + '期间，' + model.data.sms_text_last;
                defered.resolve();
            }, function (data) {
                defered.reject();
            });
            return defered.promise;
        }

        /**
         * 获取联系人信息
         *
         * @param {Object} dataToSend
         */
        function queryContactList(dataToSend) {
            var defered = $q.defer();
            NetworkService.request('contact_queryList', {
                cid: dataToSend.cid
            }, function (data) {
                model.contactList = data.data;
                $log.debug(model.contactList);
                defered.resolve();
            }, function (data) {
                defered.reject();
            });
            return defered.promise;
        }

        function refreshPhoneList(fn) {

        }


        EventService.on(EventService.ev.START_STATEMENT, function (event, dataToSend) {
            var initPromise = $q.all([requestStatement(dataToSend), queryContactList(dataToSend)]);
            initPromise.then(function () {
                model.dialogId = PageService.showDialog('Statement');
            });
        });

        return model;
    }
]);

// {
//     "dateSt": "1480416360",
//     "dateEnd": "1480416360",
//     "money": -587380.52,
//     "content": "第1张单据：2016-11-29日销售单，那瓦霍白43只装15件X32.61=489.15元。该单总计489.15元，优惠90.99元，该单还差-587380.52元结清",
//     "sign": "bbad195c3524a0caa8afbe9cb7bd7638",
//     "token": "db5ef15e5091423193f2818939d63047",
//     "num": 3,
//     "expense": 0
// }