/**
 * Created by Raytine on 2017/6/23.
 */
$(function () {
    var $login = $("#loginWindows"), $creatorLogin = $("#creatorLogin"), $staffLogin = $("#staffLogin"),
        $selectWindows = $("#select-loginWay"), $loginInput = $("#login-input"), $changeLoginWay = $("#changeLoginWay"),
        $province = $("#province"), $loginSub = $("#loginSub"), $city = $("#city"),
        $back = $("#back"), $thirdNext = $("#third_next"), $jingxuaocunSub = $("#jinxiaocunSub"),
        $yiqijiSub = $("#yiqijiSub"), $selectVersion = $("#select-version"), $thirdLogin = $("#third-login"),
        $selectBack = $("#select_back"), $inputError = $(".inputError"), $companyName = $("#companyName"),
        // $rememberNum = $("#rememberNum"), $rememberPassword = $("#rememberPassword"),
        $industryName = $("#industryName"), $loginBottom = $("#login-bottom"), $creatorUsername = $("#creatorUsername"),
        $staffUsername = $("#staffUsername"),
        $password = $("#password"), $loginInputBack = $("#login-input-back"), $forgepassword = $("#forget-password"),
        $forgetInput = $("#forget-password-input"), $forgetTo = $("#forgetTo"), $forgetGetAuth = $("#forgetGetAuth"),
        $phoneNum = $("#phoneNum"), $phoneInput = $("#phoneInput"), $authInput = $("#authInput"),
        $passwordInput = $("#passwordInput"),
        $againSend = $("#againSend"), $staffInput = $("#staff-input"), $forgetSub = $("#forgetSub"),
        verifySevice = verify($inputError),
        currentPage = 0,//当前的所在的页面
        userInfo = cookie.get("userInfo"), loginInfo = cookie.get("loginInfo"),
        respondData;
    (function () {
        var data = decodeURIComponent(loginInfo);
        if(loginInfo){
            data = JSON.parse(data);
            if (data) {
                var dataType = {
                    username: data.username,
                    password: data.password,
                    mode: 2,
                    type: 1
                };
                login(dataType)
            }
        }
    })();

    function cleatWarn() {
        for (var i = 0; i < $inputError.length; i++) {
            $inputError.eq(i).text("").css({"display": "none"})
        }
    }

    function login(dataType) {
        request('login', dataType, function (data) {
            if (data.EC > 0) {
                // window.location.href=apiToUrl["skinApp"];
                if (data.data.rpg == 12) {
                    respondData = data.data;
                    advanceDiscern();
                    advanceDiscern(1);
                } else {
                    cookie.set("userInfo", encodeURIComponent(JSON.stringify(data.data)), 7, 1);
                    cookie.set("loginInfo", encodeURIComponent(JSON.stringify(dataType)), 7, 1);
                    window.location.href = apiToUrl['skinApp'];
                }
            } else {

            }
        }, function () {

        }, $inputError.eq(2))
    }

    $(document).keydown(function (event) {
        if (event.keyCode == 13) {
            $loginSub.trigger("click")
        }
    });


    $changeLoginWay.loginFigure = null;
    $login.height(window.innerHeight);
    function backDiscern() {
        switch (currentPage) {
            case 0:
                location.href = "../index.html?xyvs=2.0.1";
                break;
            case 1:
                $loginInput.css("display", "none");
                $selectWindows.css("display", "block");
                $loginBottom.css("display", "block");
                currentPage = 0;
                break;
            case 2:
                $thirdLogin.css("display", "none");
                $loginInput.css("display", "block");
                $loginBottom.css("display", "none");
                currentPage = 1;
                break;
            case 3:
                $thirdLogin.css("display", "block");
                $selectVersion.css("display", "none");
                $loginBottom.css("display", "block");
                currentPage = 2;
                break;
            case 4:
                $forgepassword.css("display", "none");
                $loginInput.css("display", "block");
                $loginBottom.css("display", "none");
                currentPage = 1;
                break;
            case 5:
                $forgetInput.css("display", "none");
                $forgepassword.css("display", "block");
                break;
        }
    }//页面后退
    function advanceDiscern(index) {
        switch (currentPage) {
            case 0:
                $selectWindows.css("display", "none");
                $loginInput.css("display", "block");
                $loginBottom.css("display", "none");
                currentPage = 1;
                break;
            case 1:
                $loginInput.css("display", "none");
                $loginBottom.css("display", "block");
                if (index === 1) {
                    $thirdLogin.css("display", "block");
                    currentPage = 2;
                }
                if (index === 2) {
                    $forgepassword.css("display", "block");
                    currentPage = 4
                }
                break;
            case 2:
                $thirdLogin.css("display", "none");
                $selectVersion.css("display", "block");
                $loginBottom.css("display", "none");
                currentPage = 3;
                break;
            case 3:
                break;
            case 4:
                $forgetInput.css("display", "block");
                $forgepassword.css("display", "none");
                $loginBottom.css("display", "block");
                currentPage = 5;
                break;
            case 5:
                break;

        }
    }//页面前进 index 是同一页面 跳不同页面的参数
    $back.on("click", function () {
        if (currentPage == 2) {
            backDiscern();
        }
        backDiscern();
    });
    //选择登陆方式
    $creatorLogin.on("click", function () {
        $selectWindows.loginFigure = "creator";
        $changeLoginWay.text("以员工账号登陆");
        $staffInput.css("display", "none");
        advanceDiscern()
    });
    $staffLogin.on("click", function () {
        $selectWindows.loginFigure = "staff";
        $changeLoginWay.text("以创建者账号登陆");
        $staffInput.css("display", "block");
        advanceDiscern()
    });
    $loginInputBack.on("click", function () {
        cleatWarn()
        backDiscern()
    });
    //登陆页面
    verifySevice.usernameVerify($creatorUsername, 0);
    verifySevice.passwordVerify($password, 2);
    //登陆方式切换
    $changeLoginWay.on("click", function () {
        cleatWarn();
        $creatorUsername.val("");
        $password.val("");
        if ($selectWindows.loginFigure === "creator") {
            $(this).text("以创建者账号登陆");
            $staffInput.css("display", "block");
            $selectWindows.loginFigure = "staff";
        } else if ($selectWindows.loginFigure === "staff") {
            $(this).text("以员工账号登陆");
            $staffInput.css("display", "none");
            $selectWindows.loginFigure = "creator"
        }
    });

    $loginSub.on("click", function () {
        var pass;
        $password.trigger("blur");
        $creatorUsername.trigger("blur");
        if ($selectWindows.loginFigure === 'staff') {
            pass = $password.pass === true && $creatorUsername.pass === true
        } else {
            pass = $password.pass === true && $creatorUsername.pass === true
        }
        if (pass) {
            var dataType = {},
                password = $.md5("sjerp" + $password.val());
            if ($selectWindows.loginFigure === "staff") {
                dataType = {
                    admin: $creatorUsername.val(),
                    username: $staffUsername.val(),
                    password: password,
                    mode: 1,
                    type: 1
                }
            } else if ($selectWindows.loginFigure === "creator") {
                dataType = {
                    username: $creatorUsername.val(),
                    password: password,
                    mode: 2,
                    type: 1
                }
            }
            login(dataType);
        }
    });

    //--------------------------信息填写页面
    //信息填写页面输入验证

    $companyName.on("blur", function () {
        $companyName.pass = false;
        if ($companyName.val() === "") {
            $inputError.eq(2).text("请输入公司名！");
            return
        }
        $inputError.eq(2).css("display", "none");
        $companyName.pass = true;
    });
    $industryName.on("blur", function () {
        $industryName.pass = false;
        if ($industryName.val() === "") {
            $inputError.eq(3).text("请输入行业名！");
            return
        }
        $inputError.eq(3).css("display", "none");
        $industryName.pass = true;
    });
    $city.on("blur", function () {
        var value = $(this).val();
        $city.pass = false;
        if (value === "") {
            $inputError.eq(4).text("请选择地区！");
            return;
        }
        $city.pass = true
    });
    //获取省份和市
    $city.canLoad = true;//当前城市可以加载
    for (var i = 0, len = place.length; i < len; i++) {
        $province.append("<option value =" + i + ">" + place[i].name + "</option>");
    }
    $province.on("blur", function () {
        $city.empty();
        for (var i = 0, len = place[$province.val()].sub.length; i < len; i++) {
            $city.append("<option value =" + i + ">" + place[$province.val()].sub[i].name + "</option>");
        }
    });
    $thirdNext.on("click", function () {
        if ($companyName.pass && $industryName.pass && $city.pass) {
            var dataType = {
                shop_name: $companyName.val(),
                industry: $industryName.val(),
                city: place[$province.val()].sub[$city.val()].name,
                province: place[$province.val()].name
            };
            request("setShopInfo", dataType, function (data) {
                if (data.EC > 0) {
                    advanceDiscern();
                }
            },$inputError.eq(4))
        } else {
            alert(4);
            $companyName.trigger("blur");
            $city.trigger('blur');
            $industryName.trigger("blur");
        }

    });
    $selectBack.on("click", function () {
        backDiscern()
    });
    //--------------------------忘记密码获取验证码页面
    verifySevice.phoneVerify($phoneNum, 6);
    $forgetTo.on("click", function () {
        if($creatorUsername.val()){
            $phoneNum.val($creatorUsername.val())
        }
        advanceDiscern(2);
    });

    //---------------------------密码重置页面
    verifySevice.phoneVerify($phoneInput, 6);
    verifySevice.authVerify($authInput, 7);
    verifySevice.passwordVerify($passwordInput, 8);
    $forgetGetAuth.on("click", function () {//获取验证码
        if ($phoneNum.pass === true) {
            var dataType = {
                type: 3,
                mobile: Number($phoneNum.val().trim())
            };
            request("requestSMSSendVerifyCode", dataType, function (data) {
                if (data.EC > 0) {
                    waitTime($againSend);
                    advanceDiscern();
                    $phoneInput.val($phoneNum.val());
                }
            }, function () {}, $inputError.eq(6))
        }else {
            $phoneNum.trigger("blur");
        }
    });
    $forgetSub.on("click", function () {
        $phoneInput.trigger("blur");
        if ($phoneInput.pass === true && $authInput.pass === true && $passwordInput.pass === true) {
            var dataType = {
                password: $.md5("sjerp" + $passwordInput.val()),
                admin_mobile: Number($phoneInput.val()),
                verify_code: $authInput.val()
            };
            request("editUserPasswd", dataType, function (data) {
                if (data.EC > 0) {
                    var loginData = {
                        username:Number($phoneInput.val()),
                        password: $.md5("sjerp" + $passwordInput.val()),
                        mode: 2,
                        type: 1
                    };
                    login(loginData)
                }
            })
        } else {
            $phoneInput.trigger("blur");
            $passwordInput.trigger("blur");
            $authInput.trigger("blur");
        }
    });
    $againSend.on("click", function () {
        $phoneInput.trigger("blur");
        if ($phoneInput.pass === true && $againSend.pass === true) {
            var dataType = {
                type: 3,
                mobile: $phoneInput.val()
            };
            request('requestSMSSendVerifyCode', dataType, function (data) {
                if (data.EC = 1) {
                    waitTime($againSend)
                }
            }, function () {

            }, $inputError.eq(8));
        }
    });


    //--------------------------版本选择页面
    $selectBack.on('click', function () {
        cleatWarn();
        backDiscern();
    });
    function changeRPG(mode) {
        var dataType = {
            rpg_mode: mode
        };
        request('changeRPG', dataType, function (data) {
            if (data.EC > 0) {
                respondData.rpg = mode ==10001 ? 1 : 8;
                cookie.set("userInfo", encodeURIComponent(JSON.stringify(respondData)), 7, 1);
                window.location.href = apiToUrl['skinApp'];
            }
        })
    }

    $jingxuaocunSub.on("click", function () {//选择进销存
        changeRPG(10001)
    });
    $yiqijiSub.on("click", function () {//选择易企记
        changeRPG(10002)
    })
});