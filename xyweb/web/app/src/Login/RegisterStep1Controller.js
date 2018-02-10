'use strict';

angular.module('XY').controller('RegisterStep1Controller', ['$scope', 'NetworkService', '$log', '$timeout', 'PageService', 'LoginModel', 'EventService', 'ForgetModel',
    function($scope, NetworkService, $log, $timeout, PageService, LoginModel, EventService, ForgetModel) {

        //recovery 为true时为恢复密码状态
        $scope.recovery = ForgetModel.recovery;
        $scope.regModuleType = '1';
		$scope.type = ForgetModel.recovery ? 3 : 1;
		$scope.agreeService = false;
        //PageService.setCurrentPage('signUp');
        var handlerPopup = function(captchaObj) {
            // 成功的回调
            captchaObj.onSuccess(function() {
                var validate = captchaObj.getValidate();
                $.ajax({
                    url: NetworkService.getDomain() + "index.php?m=Home&c=Util&a=requestSMSSendVerifyCode", // 进行二次验证
                    type: "post",
                    dataType: "json",
                    data: {
                        type: $scope.type,
                        geetest_challenge: validate.geetest_challenge,
                        geetest_validate: validate.geetest_validate,
                        geetest_seccode: validate.geetest_seccode,
                        mobile: LoginModel.phoneNum,
                    },
                    success: function(data) {
                        $log.debug('requestSMSSendVerifyCode', data)
                        if (data.EC > 0) {
                            $scope.$apply(function () {
                                LoginModel.status = 5;
                            });
                            LoginModel.setResendDisabled();
                        } else {
                            $scope.$apply(function () {
                                PageService.showSharedToast(data.MSG);
                            })
                        }
                    }
                });
            });
            $("#popup-submit").click(function() {
                captchaObj.show();
            });
            $scope.keydown = function(event) {
                if (event.keyCode == 13) {
                    captchaObj.show();
                }
            }
            // 将验证码加到id为captcha的元素里
            captchaObj.appendTo("#popup-captcha");
            // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
        };
        // 验证开始需要向网站主后台获取id，challenge，success（是否启用failback）
        $scope.gotoOfficialSite = function() {
                var absUrl = window.location.host;
                if (absUrl === "localhost"){
                    window.open('/index/web/serviceContent.html?xyvs=2.0.1');
                }else {
                    window.open("/web/serviceContent.html?xyvs=2.0.1");
                }

        };
        $.ajax({
            url: NetworkService.getDomain() + "index.php?m=Home&c=Util&a=getJiyanToken&t=" + (new Date()).getTime(), // 加随机数防止缓存
            type: "get",
            dataType: "json",
            success: function(data) {
                // 使用initGeetest接口
                // 参数1：配置参数
                // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
                $log.debug('1',data);
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
]);
