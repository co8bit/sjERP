'use strict'

xy.factory('UserService', ['$rootScope', '$q','$log', 'NetworkService', '$cookies', 'EventService', 'PageService','$window', '$location',
    function ($rootScope,$q, $log, NetworkService, $cookies, EventService, PageService, $window, $location) {
        var expires = new Date();
        expires.setDate(expires.getDate() + 30);
        var UserService = {};
        var cookie = {
            set:function(key,val,time){//key设置键 val设置值 time有效时间
                if(cookie.get(val)){
                    cookie.delete(val)
                }
                var date=new Date();
                var expiresDays=time;
                date.setTime(date.getTime()+expiresDays*24*3600*1000); //格式化为cookie识别的时间
                document.cookie=key + "=" + val +";expires="+date.toGMTString()+";path=/";  //设置cookie
            },
            get:function(key){//需要获取cookie的键值
                var getCookie = document.cookie.replace(/[ ]/g,"");
                var arrCookie = getCookie.split(";")
                var tips;  //声明变量tips
                for(var i=0;i<arrCookie.length;i++){
                    var arr=arrCookie[i].split("=");
                    if(key==arr[0]){
                        tips=arr[1];   //将cookie的值赋给变量tips
                        break;
                    }
                }
                return tips;
            },
            delete:function(key){ //删除key 要删除键的值
                var date = new Date(); //获取当前时间
                date.setTime(date.getTime()-10000); //将date设置为过去的时间
                document.cookie = key + "=v; expires =" +date.toGMTString();//设置cookie
            }
        }
        // 登录状态
        var xyjxcLoginStatus = {
            status: 0, // 0:未登录 >0 已登录
            id: 0,
        }
        function turnToLogin(){
            cookie.set("userInfo",JSON.stringify(null),7,1);
            cookie.set('loginInfo',JSON.stringify(null),7,1);
            PageService.showIndexPage("Login")
        }
        // 上次登录成功的adminname
        var xyLastLogin = {
            adminLogin: {
                username: '',
            },
            staffLogin: {
                admin: '',
                username: '',
            },
        };
        // 登录成功时会调用这个
        UserService.loginSuccess = function (data) {
            // 记录从服务器得到的登录信息
            xyjxcLoginStatus.status = 1;
            xyjxcLoginStatus.id = data.uid;
            xyjxcLoginStatus.shop_name = data.shop_name;
            xyjxcLoginStatus.rpg = data.rpg;
            xyjxcLoginStatus.name = data.name;
            if ($window._vds) {
                $window._vds.push(['setCS1', 'uid', data.uid]);
                $window._vds.push(['setCS2', 'admin_uid', data.saas_uid]);
                $window._vds.push(['setCS3', 'user_name', data.name]);
                $window._vds.push(['setCS4', 'saas_name', data.shop_name]);
                $window._vds.push(['setCS7', 'saas_industry', data.industry]);
                // $log.log('_vds',$window._vds);
            }

            // $log.log('loginStatus:', xyjxcLoginStatus);
            // 把登录信息存到cookie里
            $cookies.putObject('xyjxcLoginStatus', xyjxcLoginStatus);
            PageService.setNgViewPage('Dashboard');//进首页
            // EventService.emit(EventService.ev.LOGIN_SUCCESS, {}, 1);
        };
        (()=>{
            var data = decodeURIComponent(cookie.get("userInfo"));
            if(data == undefined || data == 'undefined' || data == '' || data == null || data == 'null'){
                turnToLogin();
                return
            }
            data = JSON.parse(data);
            UserService.loginSuccess(data)
            if (data.mode == 1) {
                xyLastLogin.staffLogin.admin = adminUsername;
                xyLastLogin.staffLogin.username = username;
            }
            // 如果是管理登录，管理员用户名是参数中的username
            if (data.mode == 2) {
                xyLastLogin.adminLogin.username = username;
            }

            $cookies.putObject('xyLastLogin', xyLastLogin, {
                expires: expires
            });
        })();

        UserService.isExperience = Cookies.get('isExperience');

        UserService.isFree = Cookies.get('free');
        // $log.log('isFree',UserService.isFree);
        // $log.log('isExprience',UserService.isExperience);

        UserService.memberInfo = Cookies.getJSON('memberInfo');

        UserService.getMemberInfo = function () {
            UserService.memberInfo = Cookies.getJSON('memberInfo');
        }

        UserService.clearMemberInfo = function () {
            Cookies.remove('memberInfo');
            UserService.memberInfo = {};
        }

        UserService.experience = Cookies.get('experience');

        UserService.getExperience = function () {
            UserService.experience = Cookies.get('experience');
        }

        UserService.clearExperience = function () {
            Cookies.remove('experience');
            UserService.experience = {};
        }

        // 读取上次登录成功的信息
        var tmp_xyLastLogin = $cookies.getObject('xyLastLogin')
            // $log.debug('get tmp_xyLastLogin',xyLastLogin)
        if (tmp_xyLastLogin) {
            if (tmp_xyLastLogin.adminLogin || tmp_xyLastLogin.staffLogin) {
                angular.copy(tmp_xyLastLogin, xyLastLogin)
            }
        }

        // 读取登录状态
        var tmp_xyjxcLoginStatus = $cookies.getObject('xyjxcLoginStatus')
        if (tmp_xyjxcLoginStatus) {
            angular.copy(tmp_xyjxcLoginStatus, xyjxcLoginStatus)
        }

        var loginStatusChecked = false


        // 员工列表
        var userList = []

        //用来存储店铺设置
        var shopInfo = {}

        UserService.Util = {}
        UserService.Util.getJiyanToken = function (callback) {
            var dataToSend = {
                type: mode,
            }
            NetworkService.request('util_getJiyanToken', dataToSend, function (data) {

                if (callback) {
                    callback()
                }

            }, function () {

            })
        }

        // 获得用户列表引用
        UserService.getUserList = function () {
            return userList
        }

        // 按用户名查找用户并返回引用
        UserService.findUserByName = function (userName) {
            var out = undefined
            for (var key in userList) {
                // $log.log('findCompanyByName ',userList[key].name,companyName)
                if (userList[key].name == userName) {
                    out = userList[key]
                    break
                }
            }
            return out
        }

        // 按uid查找用户并返回拷贝
        UserService.findUserById = function (uid) {
            var out = undefined
            for (var key in userList) {
                // $log.log('findCompanyByName ',userList[key].name,companyName)
                if (userList[key].uid == uid) {
                    out = {}
                    userList[key].password = '******';
                    angular.copy(userList[key], out)
                    break
                }
            }
            return out
        };


        UserService.user = {};// 对应服务器User Model
            // 查询用户列表,成功时回调
        UserService.user.getList = function (mode, callback) {
            var defered = $q.defer();
            var dataToSend = {
                type: mode,
            };
            NetworkService.request('user_getList', dataToSend, function (data) {
                angular.copy(data.data, userList);
                defered.resolve();
                if (callback) {
                    callback()
                }
            }, function () {

            });
            return defered.promise;
        };

        // 判断当前是否已登录
        UserService.isLoggedIn = function () {
            // 从cookies获取登录状态
            var t = $cookies.getObject('xyjxcLoginStatus');

            if (t) {
                // $log.log('UserService->isLoggedIn t: ',t)
                angular.copy(t, xyjxcLoginStatus);
                return true
            } else {
                return false
            }
        };

        // * @param  string  username用户名
        // * @param  string  password密码
        // * @param  string  verify_code 验证码
        // * @param  string  where_know从何处得知
        UserService.admin_register = function (username, password, verify_code, where_know, invitated_code) {

            var dataToSend = {
                username: username,
                password: $.md5("sjerp" + password),
                verify_code: verify_code,
                where_know: where_know,
                invitated_code: invitated_code,
            };
            NetworkService.request('admin_register', dataToSend, function (data) {
                xyjxcLoginStatus.status = 1;
                xyjxcLoginStatus.id = data.data.uid;
                xyjxcLoginStatus.shop_name = data.data.shop_name;
                xyjxcLoginStatus.rpg = data.data.rpg;

                if ($window._vds) {
                    $window._vds.push(['setCS1', 'uid', data.data.uid]);
                    $window._vds.push(['setCS2', 'admin_uid', data.data.saas_uid]);
                    $window._vds.push(['setCS3', 'user_name', data.data.name]);
                    $window._vds.push(['setCS4', 'saas_name', data.data.shop_name]);
                    $window._vds.push(['setCS7', 'saas_industry', data.data.industry]);
                    // $log.log('_vds',$window._vds);
                }


                $cookies.putObject('xyjxcLoginStatus', xyjxcLoginStatus);
                EventService.emit(EventService.ev.ADMIN_REGISTER_SUCCESS, data);
                UserService.clearMemberInfo();
            }, function () {

                EventService.emit(EventService.ev.ADMIN_REGISTER_ERROR);
            })

        };

        // * @param  string  username用户名
        // * @param  string  password密码
        // * @param  string  verify_code 验证码
        UserService.admin_recovery = function(usename,password,verify_code){
            var dataToSend = {
                userName: usename,
                password:  $.md5("sjerp" + password),
                verify_code: verify_code,
            };
            NetworkService.request('editUserPasswd',dataToSend,function(data){
                if (data.EC == 1) {
                    PageService.showSharedToast('密码重置成功！');
                    EventService.emit(EventService.ev.ADMIN_RECOVERY_SUCCESS);
                }else{
                    PageService.showSharedToast(data.MSG);
                }
            })
        };



        // 注册普通用户
        UserService.user.register = function (userInfo, callback) {
            var defered = $q.defer();
            var dataToSend = {};
            angular.copy(userInfo, dataToSend);
            dataToSend.password = $.md5("sjerp" + userInfo.password);
            NetworkService.request('user_register', dataToSend, function (data) {
                // PageService.showSharedToast('新建成员成功！');
                PageService.closeDialog();
                defered.resolve();
                //注册成功刷新列表
                UserService.user.getList(1).then(function(){
                    EventService.emit(EventService.ev.USER_REGISTER_SUCCESS,data.data);
                })
            })
            return defered.promise;
        }

        // 编辑用户信息
        UserService.user.editUserInfo = function (userInfo, arg, callback) {
            var dataToSend = {}
            angular.copy(userInfo, dataToSend);
            dataToSend.password = dataToSend.password == '******' ? '' : dataToSend.password;
            dataToSend.password = $.md5("sjerp" + dataToSend.password);

            NetworkService.request('user_editUserInfo', dataToSend, function (data) {
                // PageService.showSharedToast("保存成功！");
                PageService.closeDialog();
                EventService.emit(EventService.ev.GET_USER_INFO);
                if (arg == 1) {

                } else {
                    EventService.emit(EventService.ev.START_MANAGE_USER);

                }
            }, function () {

            })
        };

        UserService.setRpg = function(data){
            xyjxcLoginStatus.rpg = data;
        };




        // @function login 登录
        // @param adminUsername 管理员用户名，普通用户登录时使用的，管理员登录时不起作用
        // @param username
        // @param password
        // @param vericode 验证码
        // @param mode 1-普通 2-管理
        // @param type 1-创建者手机 2-用户名
        // @param callback



        UserService.login = function (adminUsername, username, password, mode, type, geetest_challenge, geetest_validate, geetest_seccode, success_callback, error_callback) {

            // $log.log('UserService.login')

            var dataToSend = {
                admin: adminUsername,
                username: username,
                password: $.md5("sjerp" + password),
                mode: mode,
                type: type,
                geetest_challenge: geetest_challenge,
                geetest_validate: geetest_validate,
                geetest_seccode: geetest_seccode,
            };

            NetworkService.request('login', dataToSend, function (data) {
                $log.debug('loginInfo:',data);
                UserService.loginSuccess(data);
                    // 如果是普通登录，管理员用户名是参数中的adminUsername
                if (mode == 1) {
                    xyLastLogin.staffLogin.admin = adminUsername;
                    xyLastLogin.staffLogin.username = username;
                }
                // 如果是管理登录，管理员用户名是参数中的username
                if (mode == 2) {
                    xyLastLogin.adminLogin.username = username;
                }

                $cookies.putObject('xyLastLogin', xyLastLogin, {
                    expires: expires
                });
                EventService.emit(EventService.ev.START_VIEW_DASHBOARD);//打开主页
                if (success_callback) {
                    success_callback();
                }
            }, function () {
                xyjxcLoginStatus.status = 0;
                xyjxcLoginStatus.id = 0;
                $cookies.putObject('xyjxcLoginStatus', xyjxcLoginStatus);
                if (error_callback) {
                    error_callback();
                }

            })
        };

        //获取登录状态
        UserService.getLoginStatus = function () {
            // if (!loginStatusChecked) {
            //     UserService.isLoggedIn()
            // }
            return xyjxcLoginStatus;
        };

        UserService.getLastLogin = function () {
            return xyLastLogin;
        };

        UserService.logout = function (callback) {
            EventService.emit(EventService.ev.LOGIN_STATUS_CHANGED, {}, 1);
            Cookies.remove('isExperience');
            Cookies.remove('isFree');

            var dataToSend = {};

            NetworkService.request('logout', dataToSend, function () {
                UserService.localLogout();
                if (callback) {
                    callback();
                }
                turnToLogin();
            }, function () {
                UserService.localLogout();
            });
            UserService.clearMemberInfo();
        };

        // 清除本地登录状态
        UserService.localLogout = function () {
            $cookies.remove('xyjxcLoginStatus');
            // $cookies.remove('xyLastLogin')
            xyjxcLoginStatus.status = 0;
            xyjxcLoginStatus.id = 0;
        };

        // 获取店铺设置本地引用
        UserService.getShopInfo_L = function () {
            return shopInfo
        };

        // 查询当前登陆者信息
        UserService.getLoginUserInfo = function(_uid,callback){
            var dataToSend = {
                uid:_uid,
            };
            NetworkService.request('user_getLoginUserInfo',dataToSend,function(data){
                if(callback){
                    callback(data.data);
                }
            },function(){

            });
        };

        // 查询店铺设置
        UserService.getShopInfo = function (callback) {
            var dataToSend = {};

            NetworkService.request('user_getShopInfo', dataToSend, function (data) {
                angular.copy(data.data, shopInfo);
                if (callback) {
                    callback(data.data);
                }
            }, function () {

            });
        };

        //修改店铺信息
        UserService.editShopInfo = function (shopInfo) {
            var dataToSend = {
                shop_name: shopInfo.shop_name,
                industry: shopInfo.industry,
                city: shopInfo.city,
                province: shopInfo.province,
            };

            NetworkService.request('user_editShopInfo', dataToSend, function (data) {
                PageService.showSharedToast('编辑成功');
            }, function () {

            })
        };

        // 反馈model
        UserService.feedback = {};
        UserService.feedback.create = function (content, callback) {
            var dataToSend = {
                content: content,
            };
            NetworkService.request('feedback_create_', dataToSend, function () {
                PageService.showSharedToast('感谢您的反馈！');
                if (callback) {
                    callback()
                }
            }, function () {

            })
        }


        UserService.getShopInfo_L = function () {

        }

        // 获取账户费用信息
        UserService.getUserAccountInfo = function (callback) {
            var defered = $q.defer();
            NetworkService.request('UserAccount_get_', '', function (data) {
                defered.resolve();
                if (callback) {
                    callback(data.data);
                }
            });

            return defered.promise;
        }


        // 下面是自动刷新---------------------------------------------------------------------------------------------------------

        // 新建用户成功刷新列表
        // isAuto   是否自动刷新
        EventService.on(EventService.ev.USER_REGISTER_SUCCESS, function (event, arg) {

        })

        // 监测到用户未登录
        EventService.on(EventService.ev.NOT_LOGGED_IN, function () {
            UserService.localLogout();
            PageService.closeDialog(0); // 关闭所有图层
        })



        return UserService
    }
]);