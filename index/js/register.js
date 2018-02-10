/**
 * Created by Raytine on 2017/6/22.
 */
$(function () {
    var $registerWindows = $("#registerWindows"), $phoneNumber = $("#phoneNum"), $getAuth = $("#getAuth"),
        $firstRegister = $("#first-register"), $secondRegister = $("#second-register"), $againSend = $("#againSend"),
        $phoneInput = $("#phoneInput"), $passwordInput = $("#passwordInput"), $authInput = $("#authInput"),
        $verifyImg = $("#verifyImg"), $verifyInput = $("#verifyInput"),
        $registerSub = $("#registerSub"), $smallTittle = $("#smallTittle"), $inputError = $(".inputError"),
        $back = $("#back"), $agreeService = $("#agreeService"),
        currentPage = 0, verifySevice = verify($inputError);
    $registerWindows.height(window.innerHeight);
    $againSend.pass = true,
        $getAuth.pass = true;
    $verifyImg.attr("src", domainData + "index.php?m=Home&c=Util&a=get_xy_verify_code");
    $verifyImg.on("click", function () {
        $verifyImg.attr("src", domainData + "index.php?m=Home&c=Util&a=get_xy_verify_code");
    });
    //first-register
    $smallTittle.text("欢迎注册");
    function advanceDiscern() {
        switch (currentPage) {
            case 0:
                $firstRegister.css('display', "none");
                $secondRegister.css("display", "block");
                currentPage = 1;
                break;
            case 1:
                break
        }
    }

    function backDiscern() {
        switch (currentPage) {
            case 0:
                location.href = "../index.html?xyvs=2.0.1";
                break;
            case 1:
                $firstRegister.css('display', "block");
                $secondRegister.css("display", "none");
                currentPage = 0;
                break
        }

    }

    $back.on("click", function () {
        backDiscern()
    });
    $verifyInput.on("blur", function () {
        $verifyInput.pass = false;
        if (!$verifyImg.val()) {
            $inputError.eq(0).text("验证码不能为空").css("display", "block")
        }
        $inputError.eq(0).text("").css("display", "none");
        $verifyInput.pass = true;
    });
    verifySevice.phoneVerify($phoneNumber, 0);
    $getAuth.on('click', function () {
        if ($phoneNumber.pass === true && $againSend.pass === true && $getAuth.pass === true && $verifyInput.pass) {
            var dataType = {
                xy_verify_code: $verifyInput.val(),
                type: 1,
                mobile: $phoneNumber.val()
            };
            request('requestSMSSendVerifyCode', dataType, function (data) {
                if (data.EC = 1) {
                    advanceDiscern();
                    $phoneInput.val($phoneNumber.val());
                    waitTime($againSend);
                    waitTime($getAuth);
                }
            }, function () {

            }, $inputError.eq(0));
        }
    });
    //second-register
    verifySevice.phoneVerify($phoneInput, 1);
    verifySevice.passwordVerify($passwordInput, 2);
    verifySevice.authVerify($authInput, 3);
    $againSend.on("click", function () {
        $phoneInput.trigger("blur");
        if ($phoneInput.pass === true && $againSend.pass === true) {
            var dataType = {
                type: 4,
                mobile: $phoneInput.val()
            };
            request('requestSMSSendVerifyCode', dataType, function (data) {
                if (data.EC = 1) {
                    waitTime($againSend)
                }
            }, $inputError.eq(3));
        }
    });
    $agreeService.click(function () {
        if ($agreeService.is(':checked')) {
            $inputError.eq(4).css({'display': 'none'})
        }
    });
    $registerSub.on("click", function () {
            if (!$agreeService.is(':checked')) {
                $inputError.eq(4).text("请同意服务协议").css({'display': 'block'});
                return
            }
            $phoneInput.trigger("blur");
            $authInput.trigger("blur");
            if ($passwordInput.pass === true && $authInput.pass === true && $phoneInput.pass === true) {
                var dataType = {
                    username: $phoneInput.val(),
                    password: $.md5("sjerp" + $passwordInput.val()),
                    verify_code: $authInput.val(),
                    type: 1
                };
                request("admin_register", dataType, function (data) {
                    if (data.EC > 0) {
                        cookie.set("loginInfo", JSON.stringify(dataType), 7, 1);
                        window.location.href = "./login.html?xyvs=2.0.1";
                    }
                }, function () {}, $inputError.eq(2))
            }
        }
    );
});