// AccountPayModel
'use strict';

angular.module('XY').factory('AccountPayModel', ['EventService', '$log', 'PageService', 'UserService',
    function (EventService, $log, PageService, UserService) {

        var model = {};

        EventService.on(EventService.ev.START_ACCOUNT_PAY, function (event, memberInfo) {
            model.memberInfo = memberInfo;
            UserService.getUserAccountInfo(function () {
                model.dialogId = PageService.showDialog('AccountPay');
            });
        });

        return model;
    }
]);