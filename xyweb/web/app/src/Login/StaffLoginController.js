'use strict';

xy.controller('StaffLoginController', ['$scope', 'NetworkService', '$log', '$timeout', 'PageService', 'LoginModel', 'MiscService', 'UserService',
    function ($scope, NetworkService, $log, $timeout, PageService, LoginModel, MiscService, UserService) {

        $log.debug('StaffLoginController');

        $scope.LoginModel = LoginModel
        $scope.model = LoginModel
        var model = LoginModel
        model.loadLast()

        // 初始化的时候就检查一下是不是直接启动极验
        if (model.loginFailureCount >= 3) {
            turnOnGeetest()
        }
        $scope.login = function () {
            // mode = 2 管理员登录,type = 1 无意义
            // $log.log('click login');

            //如果失败次数<3，直接请求登录,如果大于等于3，则已经开启极验验证，login请求改从极验的回调里发
            if (model.loginFailureCount < 3) {
                // 使用创建者手机号还是店名
                var isAdminMobile
                if (MiscService.testElevenNum(model.staffLogin.admin)) {
                    isAdminMobile = 1
                } else {
                    isAdminMobile = 2
                }
                UserService.login(model.staffLogin.admin, model.staffLogin.username, model.staffLogin.password, 1, isAdminMobile, '', '', '', '',
                    //登录失败的回调,记录失败次数+1，如果到达3次开启极验验证
                    function () {
                        PageService.showConfirmDialog('密码错误', null, function () {
                            document.getElementById('password').focus();
                        });
                        model.loginFailureCount += 1
                        if (model.loginFailureCount >= 3) {
                            turnOnGeetest()
                        }
                    });
            }
        }

        //PageService.setCurrentPage('signUp');
        var handlerPopup = function (captchaObj) {
            // 极验本地验证成功的回调
            captchaObj.onSuccess(function () {
                var isAdminMobile
                if (MiscService.testElevenNum(model.staffLogin.admin)) {
                    isAdminMobile = 1
                } else {
                    isAdminMobile = 2
                }

                var validate = captchaObj.getValidate();
                UserService.login(model.staffLogin.admin, model.staffLogin.username, model.staffLogin.password, 1, isAdminMobile, validate.geetest_challenge, validate.geetest_validate, validate.geetest_seccode,
                    function () {
                        model.loginFailureCount = 0;
                    }, function () {
                        PageService.showConfirmDialog('密码错误', null, function () {
                            document.getElementById('password').focus();
                        });
                    });
            });
            $("#popup-submit").click(function () {
                captchaObj.show();
            });
            $scope.keydown = function (event) {
                if (event.keyCode == 13) {
                    captchaObj.show();
                }
            }
            // 将验证码加到id为captcha的元素里
            captchaObj.appendTo("#popup-captcha");
            // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
        };

        // 开启极验验证，在登录失败3次时被调用
        function turnOnGeetest() {
            // 验证开始需要向网站主后台获取id，challenge，success（是否启用failback）
            $.ajax({
                url: NetworkService.getDomain() + "index.php?m=Home&c=Util&a=getJiyanToken&t=" + (new Date()).getTime(), // 加随机数防止缓存
                type: "get",
                dataType: "json",
                success: function (data) {
                    // 使用initGeetest接口
                    // 参数1：配置参数
                    // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
                    initGeetest({
                        gt: data.data.gt,
                        challenge: data.data.challenge,
                        product: "popup", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                        offline: !data.data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                        // 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
                    }, handlerPopup);
                }
            });
        }

        $scope.keydown = function (event) {
            if (event.keyCode == 13) {
                $scope.login();
            }
        }
    }
]);
