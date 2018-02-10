/**
 * Created by Raytine on 2017/6/26.
 */
function verify(inputError) {
    var $inputError = inputError;
  return{
      phoneVerify:function(dom,index) {
        dom.on("blur", function () {
            var value = dom.val();
            dom.pass = false;
            if (value === "") {
                $inputError.eq(index).text("手机号不能为空！").css("display", "block");
                return;
            }
            if (!/\d{11}/.test(value)) {
                $inputError.eq(index).text("请输入正确格式的手机号码！").css("display", "block");
                return;
            }
            $inputError.css("display", "none");
            dom.pass = true;
        });
    },//验证手机号码
      usernameVerify:function(dom, index) {
        dom.on("blur", function () {
            var value = dom.val();
            dom.pass = false;
            if (value === "") {
                $inputError.eq(index).text("用户名不能为空！").css("display", "block");
                return;
            }
            if (!/\d{11}/.test(value)) {
                $inputError.eq(index).text("用户名格式错误！(注册手机号码)").css("display", "block");
                return;
            }
            $inputError.css("display", "none");
            dom.pass = true;
        });
    },//验证用户名
      authVerify:function(dom, index) {
        dom.on("blur", function () {
            var value = dom.val().trim();
            dom.pass = false;
            if (value === "") {
                $inputError.eq(index).text("验证码不能为空！").css("display", "block");
                return;
            }
            if (!/\d{4}/.test(value)) {
                $inputError.eq(index).text('请输入4位数字验证码！').css("display", "block");
                return
            }
            $inputError.eq(index).css("display", "none");
            dom.pass = true;
        });
    },//验证验证码
      passwordVerify:function(dom, index) {
        dom.on("blur",function () {
            var value = dom.val().trim();
            dom.pass = false;
            if (value === "") {
                $inputError.eq(index).text("密码不能为空！").css("display", "block");
                return;
            }
            if (!/\w{6,32}/.test(value)) {
                $inputError.eq(index).text("请输入6-32位，数字 字母 特殊字符！").css("display", "block");
                return
            }
            $inputError.css("display", "none");
            dom.pass = true;
        });

    }//验证密码
  }
}//验证服务
function waitTime(againSend) {
    var $againSend = againSend;
    var time = 60;
    $againSend.text("重新发送(" + time-- + ")").css("background-color", "#ddd");
    var timeInter = setInterval(function () {
        $againSend.pass = false;
        $againSend.text("重新发送(" + time-- + ")");
        if (time === 0) {
            $againSend.text("重新发送").css("background-color", "#fff");
            clearInterval(timeInter);
            time = 60;
            $againSend.pass = true;
        }
    }, 1000);
}//验证码重新发送效果
var cookie = {
    set:function(key,val,time,all){//key设置键 val设置值 time有效时间
        if(cookie.get(val)){
            cookie.delete(val)
        }
        var date=new Date();
        var expiresDays=time;
        date.setTime(date.getTime()+expiresDays*24*3600*1000); //格式化为cookie识别的时间
        if(all){
            document.cookie=key + "=" + val +";expires="+date.toGMTString()+";path=/";  //设置cookie
        }else {
            document.cookie=key + "=" + val +";expires="+date.toGMTString();  //设置cookie
        }

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
function clearCookies() {
    cookie.delete("username");
    cookie.delete("password");
    cookie.delete("mode");
    cookie.delete("creator");
}//清除所有cookies