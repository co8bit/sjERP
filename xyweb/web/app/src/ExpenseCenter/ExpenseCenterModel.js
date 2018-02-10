// AccountPayModel
'use strict';

angular.module('XY').factory('ExpenseCenterModel', ['EventService', '$log', 'PageService', 'UserService', 'NetworkService', '$rootScope', 'QueryClass', 'MiscService', '$q',
    function (EventService, $log, PageService, UserService, NetworkService, $rootScope, QueryClass, MiscService, $q) {

        var model = {};

        model.query = QueryClass.newQueryForExpense();

        model.changeType = function (num) {
            model.query.setType(num);
            model.query.setPage(1);
            model.query.setPline(8);
            model.query.request();
        }

        function init() {

            model.query.setPage(1);
            model.query.setPline(8);
            model.query.setType(2);

            // var classOptionArr = [{
            //     optionName: '充值',
            //     id: 1,
            // }, {
            //     optionName: '支出',
            //     id: 2,
            // }];

            // model.query.getClassCheckbox().setOption(classOptionArr);
        }

        function calcPline() {
            var bodyHeight = $(".function-body").height();
            var tableHeight = bodyHeight - 250;
            // $log.debug('tableHeight',tableHeight)
            var ajustedPline = Math.floor(tableHeight / 36);
            return ajustedPline;
        }

        function getMoneyTimeAndSave() {
            var defered = $q.defer();
            NetworkService.request('PaymentDetails_getMoneyTimeSave', '', function (data) {
                model.saved = data.data;
                defered.resolve();
            });
            return defered.promise;
        }

        function query() {
            var defered = $q.defer()
                // var ajustedPline = calcPline();
                // model.query.setPline(ajustedPline);
                model.query.cleanTime();

                // 异步任务计数器
                var task = MiscService.newAsyncTask(1);
                // 设定回调
                task.setFinalProc(function () {
                    // 初始查询完成后清除回调
                    model.query.setCustomCallback(undefined);

                    defered.resolve();
                })

                // 新建一个管理查询的对象
                model.query.setCustomCallback(function () {
                    // 查询成功后计数
                    task.finishOneProc();
                });

                model.query.request();

            return defered.promise;
        }

        EventService.on(EventService.ev.START_EXPENSE_CENTER, function (event, memberInfo) {

            model.memberInfo = memberInfo;

            init();

            var queryPromise = query();

            var getMoneyTimeAndSavePromise = getMoneyTimeAndSave();

            var getUserAccountInfoPromise = UserService.getUserAccountInfo(function (data) {
                model.userAccountInfo = data;
            });

            $q.all([getUserAccountInfoPromise, getMoneyTimeAndSavePromise, queryPromise]).then(function () {
                    model.dialogId = PageService.showDialog('ExpenseCenter');
            });

        });

        return model;
    }
]);