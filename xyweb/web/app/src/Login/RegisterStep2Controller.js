'use strict';

angular.module('XY').controller('RegisterStep2Controller', ['$scope', 'NetworkService', '$log', '$timeout', 'PageService', 'LoginModel', 'UserService',
    function($scope, NetworkService, $log, $timeout, PageService, LoginModel, UserService) {


        $log.debug('RegisterStep2Controller')
        $scope.PageService = PageService;
        $scope.model = LoginModel;
        $scope.model.verify_code = '';
        $scope.model.password = '';
        $scope.isServiceShow = false;
        $scope.agreeService=true;
        $scope.showService=function () {
            $scope.isServiceShow =true;
        };
        $scope.closeService = function () {
            $scope.isServiceShow =false;
        };
        var handlerPopup = function(captchaObj) {
            // 成功的回调
            captchaObj.onSuccess(function() {
                var validate = captchaObj.getValidate();
                $.ajax({
                    url: NetworkService.getDomain() + "index.php?m=Home&c=Util&a=requestSMSSendVerifyCode", // 进行二次验证
                    type: "post",
                    dataType: "json",
                    data: {
                        type: 1,
                        geetest_challenge: validate.geetest_challenge,
                        geetest_validate: validate.geetest_validate,
                        geetest_seccode: validate.geetest_seccode,
                        mobile: LoginModel.phoneNum,
                    },
                    success: function(data) {
                        $log.debug('requestSMSSendVerifyCode', data)
                        if (data.EC > 0) {
                            LoginModel.setResendDisabled();
                        }
                    }
                });
            });
            $("#popup-submit").click(function() {
                captchaObj.show();
            });
            // 将验证码加到id为captcha的元素里
            captchaObj.appendTo("#popup-captcha");
            // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
        };

        // 重发验证码
        $scope.resendVerifyCode = function() {

        }


        // 验证开始需要向网站主后台获取id，challenge，success（是否启用failback）
        $.ajax({
            url: NetworkService.getDomain() + "index.php?m=Home&c=Util&a=getJiyanToken&t=" + (new Date()).getTime(), // 加随机数防止缓存
            type: "get",
            dataType: "json",
            success: function(data) {
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


        // * @param  string  username;用户名
        // * @param  string  password;密码
        // * @param  string  verify_code; 验证码
        // * @param  string  where_know从何处得知
        // 注册创建者
        var isOneTouch = true; //是否第一次点击
        $scope.register = function() {
            if (isOneTouch){
                if ($scope.model.phoneNum.length < 11) {
                    PageService.showSharedToast('输入手机号有误，请检查！');
                return
                }
                if(!$scope.agreeService){
                    PageService.showSharedToast('请同意服务条款！');
                  return
                }
                isOneTouch = false; //点击后改为不是第一次点击
                if ($scope.selected == 1) { $scope.where_know = '网上搜索' }
                if ($scope.selected == 2) { $scope.where_know = '熟人推荐' }
                if ($scope.selected == 3) { $scope.where_know = '手机应用商店' }
                if ($scope.selected == 4) { $scope.where_know = $scope.other }
                if ($scope.model.password.length >= 6 &&　$scope.model.password.length <= 32) {
                    UserService.admin_register($scope.model.phoneNum, $scope.model.password, $scope.model.verify_code, $scope.where_know, $scope.invitated_code)
                } else {
                    PageService.showConfirmDialog('密码长度应在6-32个字符之间！');
                }
                setTimeout(function (){
                    isOneTouch = true; //两秒后才可以点击按钮
                },2000)
            }else{
                PageService.showConfirmDialog('操作过于频繁，请稍后再试')
            }
        };
        // 忘记密码
        $scope.recovery = function() {
            if (isOneTouch){
                isOneTouch = false; //点击后改为不是第一次点击
                if ($scope.model.phoneNum.length < 11) {
                    PageService.showSharedToast('输入手机号有误，请检查！');
                    return;
                }
                if ($scope.model.password.length >= 6 &&　$scope.model.password.length <= 32) {
                    UserService.admin_recovery($scope.model.phoneNum, $scope.model.password, $scope.model.verify_code)
                } else {
                    PageService.showConfirmDialog('密码长度应在6-32个字符之间！');
                }
                setTimeout(function (){
                    isOneTouch = true; //两秒后才可以点击按钮
                },2000)
            }else{
                PageService.showConfirmDialog('操作过于频繁，请稍后再试')
            }
        }
        $scope.keydown = function(event) {
            if (event.keyCode == 13) {
                if (model.recovery) {
                    $scope.recovery();
                }else{
                    $scope.register();
                }
            }
        }

        $scope.validate = function (callback) {
            if ($scope.model.password.length >= 6 &&　$scope.model.password.length <= 32) {
                callback();
            } else {
                PageService.showConfirmDialog('密码长度应在6-32个字符之间！');
            }
        }

    }
]);
