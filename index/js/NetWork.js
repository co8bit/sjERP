/**
 * Created by Raytine on 2017/6/28.
 */
var domainData, domainSkin;
var absUrl = window.location.host;
var ErrorCode = {//错误代码提示
    '-12001': '操作失败',
    '-12002': '手机格式不正确',
    '-12003': '验证码不合法',
    '-12004': '用户不存在',
    '-12501': '短信发送失败，请稍后再试',
    '-12502': '短信验证码不存在',
    '-12503': '短信验证码已过时',
    '-12504': '短信验证码错误',
    '-12506': '操作失败',
    '-12507': '操作失败',
    '-12508': '注册出现问题，请重试',
    '-12509': '手机号已被注册',
    '-8403': '用户名不存在',
    "-8027": '账号未被注册',
    '-8402': "密码错误",
    '-8001': '手机格式不正确',
    '-8002': '手机长度不合法',
    '-8003': '创建者手机号必须存在',
    '-8004': '手机号已被注册',
    '-8005': '手机号长度不合法',
    '-8006': '公司名已被注册',
    '-8007': '公司名长度不合法',
    '-8008': '用户名长度不合法'
};
var isServer = function () {
    //domain = domain.replace('localhost','121.42.174.105');//若为本地环境，修改domain至测试服务器，测试服务器开放跨域请求
    if (absUrl === "localhost") {//
        domainData = 'http://' + absUrl + '/xy/';
        domainSkin = 'http://' + absUrl + '/xyweb';
    } else {
        domainData = 'http://' + absUrl + '/server/';
        domainSkin = 'http://' + absUrl + '/app'
    }
};
isServer();
var apiToUrl = {
    getVerificationCode:domainData +"index.php?m=Home&c=Util&a=get_xy_verify_code" ,
    getAgency: domainData + "index.php?m=Home&c=Login&a=create_proxy",//
    requestSMSSendVerifyCode: domainData + "index.php?m=Home&c=Util&a=requestSMSSendVerifyCode", // 获取验证码
    admin_register: domainData + 'index.php?m=Home&c=Login&a=admin_register',//创建者注册
    setShopInfo: domainData + 'index.php?m=Home&c=User&a=editShopInfo',//编辑商铺
    login: domainData + 'index.php?m=Home&c=Login&a=login',//登陆
    logout: domainData + 'index.php?m=Home&c=User&a=logout',//登出
    editUserPasswd: domainData + 'index.php?m=Home&c=Login&a=editUserPasswd',//找回密码
    changeRPG: domainData + 'index.php?m=Home&c=User&a=changeRpg',//找回密码
    skinApp: domainSkin + "/index.html?xyvs=2.0.1"//跳转到APP
};
var requesting = {
    apiNameArray: [],
    apiStartTime: [],
    start: function (apiName) {
        var startTime = new Date().getTime();
        requesting.apiNameArray.push(apiName);
        requesting.apiStartTime.push(startTime);
        setTimeout(function () {
            var index = $.inArray(apiName, requesting.apiNameArray);
            if (index > -1) {
              requesting.apiNameArray.splice(index, 1);
            }
        }, 5000);
    },
    end: function (apiName) {
        function removeApi() {
          requesting.apiNameArray.splice(index, 1);
          requesting.apiStartTime.splice(index, 1);
        }
        var index = $.inArray(apiName, requesting.apiNameArray);
        if (index > -1) {
            var endTime = new Date().getTime();
            var startTime = requesting.apiStartTime[index]
            var time = endTime - startTime;
            // console.log('startTime:' + startTime + '---' + 'endTime:' + endTime);
            var minTime = 1000;
            if (time < minTime) {
                startTime(removeApi(), minTime - time);
            } else {
                removeApi();
            }

        }
    },
};
function request(apiName, dataToSend, success, error, errorDom) {
    var index = $.inArray(apiName, requesting.apiNameArray);
    // $log.log(apiName + '---$.inArray:' + index);
    if ($.inArray(apiName,requesting.apiNameArray) > -1) {
        return;
    }
    requesting.start(apiName)
    var url = apiToUrl[apiName];
    if (url === undefined) {
        return;
    }
    $.ajax({
            url: url,
            data: dataToSend,
            dataType: 'json',
            success: function (data) {
                if (data.EC > 0) {
                    if (errorDom) {
                        errorDom.css("display", "none");
                    }
                    if (success) {
                        success(data)
                    }
                } else {
                    if (errorDom) {
                        if (ErrorCode[data.EC]) {
                            errorDom.text(ErrorCode[data.EC]).css("display", "block")
                        } else {
                            errorDom.text(data.MSG).css("display", "block")
                        }


                    }
                }
                console.log('  ' + apiName + ' send: ', dataToSend);
                console.log('  ' + apiName + ' response: ', data);
            },
            error: function () {
                console.error("fail");
                if (error) {
                    error()
                }
            }

        }
    )
}