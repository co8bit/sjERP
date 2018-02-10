//xxxxController
'use strict';

angular.module('XY').controller('SelectLoginModeController', ['EventService', '$scope', '$log','PageService','LoginModel',
    function(EventService, $scope, $log,PageService,LoginModel) {
        $scope.PageService = PageService;
        $scope.EventService = EventService;
        $scope.LoginModel = LoginModel
        //页面打开发送事件告知其他窗口不是回复密码状态
        EventService.emit(EventService.ev.IF_RECOVERY)

        $scope.gotoAdminLogin = function() {
        	LoginModel.status = 2
        }

        $scope.gotoStaffLogin = function() {
        	LoginModel.status = 3
        }

        $scope.gotoRegister = function() {
            //送事件告知其他窗口不是恢复密码状态
            EventService.emit(EventService.ev.IF_RECOVERY)
        	LoginModel.status = 4
        }

    }
]);
