//xxxxModel or Service or Class
'use strict'

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('LoginModel', ['EventService', '$log', 'PageService', '$timeout', 'UserService',
    function(EventService, $log, PageService, $timeout, UserService) {

        // $log.debug("LoginModel init")

        var model = {};
        model.status = 1;
            // 状态表
            // 1  表示，显示登录类型选择界面
            // 2: 管理员登录
            // 3：员工登录
            // 4: 注册step1
            // 5: 注册step2
            // 6:
        model.phoneNum = '';
        model.password = '';
        model.verify_code = '';
        model.where_know = '';

        // 禁用重发验证码按钮
        model.secondLeft = 0;
        model.isResendVerifyCodeDisabled = false;

        model.setResendDisabled = function() {
            model.isResendVerifyCodeDisabled = true;
            model.secondLeft = 60;

            function checkTime() {

                if (model.secondLeft <= 0) {
                    model.isResendVerifyCodeDisabled = false
                } else {
                    model.secondLeft--;
                        $timeout(checkTime, 1000)
                }
            }
            $timeout(checkTime, 1000)
        }

        // 登录失败次数
        model.loginFailureCount = 0;
            // 管理员登录框内容
        model.adminLogin = {};
        model.adminLogin.username = '';
        model.adminLogin.password = '';

        // test
        model.adminLogin.username = '';
        model.adminLogin.password = '';

        // 员工登录框内容
        model.staffLogin = {};
        model.staffLogin.admin = '';
        model.staffLogin.username = '';
        model.staffLogin.password = '';

        // test
        model.staffLogin.admin = '';
        model.staffLogin.username = '';
        model.staffLogin.password = '';

        // 自动填写上次登录成功的管理员用户名
        model.loadLast = function() {
            var xyLastLogin = UserService.getLastLogin();
            // $log.debug('xyLastLogin = ', xyLastLogin)
            if (xyLastLogin) {
                if (xyLastLogin.adminLogin.username) {
                    model.adminLogin.username = xyLastLogin.adminLogin.username
                }
                if (xyLastLogin.staffLogin.admin) {
                    model.staffLogin.admin = xyLastLogin.staffLogin.admin
                }
                if (xyLastLogin.staffLogin.username) {
                    model.staffLogin.username = xyLastLogin.staffLogin.username
                }
            }
        }

        // 接收事件判断是否为恢复密码状态
        EventService.on(EventService.ev.IF_RECOVERY,function(event,arg){
            model.recovery = false;
            if(arg == 1){
                model.recovery = true;
            }
        })
        EventService.on(EventService.ev.ADMIN_RECOVERY_SUCCESS,function(){
            model.status = 1;
        });
        return model // or return model

    }
])
