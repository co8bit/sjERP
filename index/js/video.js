var videoArr = [
		'http://oprnat1l0.bkt.clouddn.com/1.%20%E7%99%BB%E5%BD%95%E4%B8%8E%E6%B3%A8%E5%86%8C.mp4',//0
		'http://oprnat1l0.bkt.clouddn.com/2.%20%E5%8D%95%E6%8D%AE%E6%B5%81%E8%AE%BE%E7%BD%AE.mp4',//1
		'http://oprnat1l0.bkt.clouddn.com/3.%20%E6%96%B0%E5%BB%BA%E5%95%86%E5%93%81%E7%B1%BB%E5%88%AB.mp4',//2
		'http://oprnat1l0.bkt.clouddn.com/4.%20%E6%96%B0%E5%BB%BA%E5%95%86%E5%93%81.mp4',//3
		'http://oprnat1l0.bkt.clouddn.com/5.%20%E6%96%B0%E5%BB%BA%E5%BE%80%E6%9D%A5%E5%8D%95%E4%BD%8D.mp4',//4
		'http://oprnat1l0.bkt.clouddn.com/6.%20%E9%94%80%E5%94%AE%E5%BC%80%E5%8D%95.mp4',//5
		'http://oprnat1l0.bkt.clouddn.com/7.%20%E5%BC%80%E6%94%B6%E6%AC%BE%E5%8D%95.mp4',//6
		'http://oprnat1l0.bkt.clouddn.com/8.%20%E6%9F%A5%E8%AF%A2%E5%BE%80%E6%9D%A5%E5%8D%95%E4%BD%8D%E4%B8%8E%E6%8A%A5%E8%A1%A8.mp4'//7
	];
var videoTitle = [
	'登录与注册',//0
	'单据流设置',//1
	'新建商品类别',//2
	'新建商品',//3
	'新建往来单位',//4
	'销售开单',//5
	'开收款单',//6
	'查询往来单位与报表'//7
]
var videoIsOpen = false;
function openVideo (num) {
	var src = videoArr[num];
	var video = $('#example_video_1');
	var wrap = $('#video-player-wrap');

	var wrap = $('#video-player-wrap');
	var height = $('#showPage').height() + 490;

	wrap.css('height', height)
	wrap.removeClass('hide');
	video.attr('src',src);
	$('#source1').attr('src',src);
	$(".helpVideo").css('top',window.scrollY + 70)
	videoIsOpen = true;
//	video.load();
}
$(window).scroll(function(){
	if(videoIsOpen){
		$(".helpVideo").css('top',window.scrollY + 70)
	}

})
function closeVideo() {
	$('#video-player-wrap').addClass('hide');
	$('#example_video_1').attr('src','');
	$('#source1').attr('src','');
	videoIsOpen = false;
}