function firstTestIsMobile() {
	//检验是否为电脑端
	var firstIsMobile = true;
	var sUserAgent = navigator.userAgent.toLowerCase();
	var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
	var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
	var bIsMidp = sUserAgent.match(/midp/i) == "midp";
	var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
	var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
	var bIsAndroid = sUserAgent.match(/android/i) == "android";
	var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
	var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
	// document.writeln("您的浏览设备为：");
	if(bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
		firstIsMobile = true;
		// document.writeln("phone");
	} else {
		firstIsMobile = false;
		// document.writeln("pc");
	}
	return firstIsMobile;
}
if(((window.screen.width == 768 && window.screen.height == 1024) || firstTestIsMobile()) && (window.screen.width >= 640)) //平板锁定缩放，手机不锁定
{
	document.write('<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">');
	// alert("isMobile");
}
window.onready = function(){
	var tips = [
		'提示:不要轻易删除往来单位哦，会把关联数据删除，且不可恢复',
		'提示:单据流在系统设置中设置','提示:打印单据的公司名在店铺设置中编辑',
		'提示:短信对账单的功能用起来了吗？还没用！进入查看所有往来记录页面，快点用起来吧',
		'提示:还在店铺仓库来回跑吗？下载手机库管端，真正做到店仓分离吧'
	];
    var randomTips = Math.floor(Math.random()*tips.length);
    alert(tips[randomTips])
    $("#loadTips").text(tips[randomTips]);
}
window.onload = function() {
	var load = document.getElementById("loader-wrapper");
	var content = document.getElementById("mainContent");
	load.style.display = "none";

	//加入监听两次触摸只接受一次，移动端避免放大缩小
	// document.addEventListener('touchstart',function (event) {
	//     if(event.touches.length>1){
	//         // alert('double touched');
	//         event.preventDefault();
	//     }
	// })

	// var lastTouchEnd=0;
	// document.addEventListener('touchend',function (event) {
	// // alert('double touched');
	//     var now=(new Date()).getTime();

	//     if(now-lastTouchEnd<=500){
	//         // alert('double touched');
	//         event.preventDefault();
	//     }

	//     lastTouchEnd=now;
	// },false)

	//  var clickTime = 0;
	// document.addEventListener('click',function (event) {//一秒内的第二次点击无效
	//     var now=(new Date()).getTime();
	//     if(now - clickTime<=1000){
	//         // console.log('sss');
	//         event.preventDefault();
	//     }
	//     clickTime = now;
	// },false)
}