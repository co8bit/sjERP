/**
 * Created by Raytine on 2017/6/24.
 */
/**
 * 一堆的解释
 * cookies----nowPage 缓存当前的页面 刷新页面的时候获取缓存页面展示
 */
var showPage;
var nowPage = '';
var url = window.location.host;
var titleArr = [
    '从此,进销存不再复杂',
    '财务报销、审核一体化管理',
    '通用版进销存',
    '星云面向冻品市场',
    '星云五金进销存软件',
    '星云客户展示平台',
    '星云进销存是'
]
var txtArr = [
    '店铺销售、采购、库存、账目新一代管理软件',
    '是时候做个省心的老板了',
    '一体化管理，让生意更加简单！',
    '开启真正的店仓分离全新模式',
    '是五金行业开门店，销售软件的首选',
    '选择我们选对未来',
    "一体化的企业、商业管理平台"
]
var imgUrl=[
    'web/minor_enterprises.html',
    'web/common.html',
    'web/freeze.html',
    'web/metals.html',
    'web/clientCase.html',
    'web/agency.html'
]
//页面初始化
$(function() {
    $('#header').load('../web/share/header.html');
    $('#footer').load('../web/share/footer.html');
})

function changeBg(index) {
    var wrap = $('#home_top_wrap')
    var title = $('#home_top_text_title');
    var txt = $('#home_top_text_node');
    var to = $('#home_top_text');
    switch(index) {
        case 0:
            wrap.css('background', '#f25749');
            title.text(titleArr[0]);
            txt.text(txtArr[0]);
            break;
        case 1:
            wrap.css('background', '#78cc47');
            title.text(titleArr[1]);
            txt.text(txtArr[1]);
            to.attr("href",imgUrl[0]);
            break;
        case 2:
            wrap.css('background',"#6d8cf2");
            title.text(titleArr[2]);
            txt.text(txtArr[2]);
            to.attr("href",imgUrl[1]);
            break;
        case 3:
            wrap.css('background', '#4992f2');
            title.text(titleArr[3]);
            txt.text(txtArr[3]);
            to.attr("href",imgUrl[2]);
            break;
        case 4:
            wrap.css('background', '#f2bb13');
            title.text(titleArr[4]);
            txt.text(txtArr[4]);
            to.attr("href",imgUrl[3]);
            break;
        case 5:
            wrap.css('background', '#f26638');
            title.text(titleArr[5]);
            txt.text(txtArr[5]);
            to.attr("href",imgUrl[4]);
            break;
        case 6:
            wrap.css("background","#fa709c");
            title.text(titleArr[6]);
            txt.text(txtArr[6]);
            to.attr("href",imgUrl[5]);
            break;
    }
}

// 用户开通选择的类型和开通的月份
var memberInfo = {
    type: 0,
    count: 0,
}

// 价格表
var price = [{
    original: [360],
    current: []
}, {
    original: [888],
    current: []
}, {
    original: [999,1699,2199],
    current: []
},{
    original: [1288,2288,2888],
    current: []
},{
    original: [1666,2888,3666],
    current: []
},{
    original: [2999,4999,6999],
    current: []
},{
    original: [299],
    current: []
}];
/**
 * 设置价格
 */
function setPrice() {
    var t = memberInfo.type;
    if(t<3){
        $("#month-2").css({display:"none"});
        $("#month-3").css({display:"none"});
    }else {
        $("#month-2").css({display:"flex"});
        $("#month-3").css({display:"flex"});
    }
    if (t > 0) {
        for(var i =0,len = price[t-1].original.length;i<len;i++){
            $("#original-price-" + i).html(addYuan(price[t - 1].original[i]));
        }
    }
    showSelectDialog();
}
/**
 * 给价格前加上¥
 *
 * @param {Number} price 价格
 * @returns 加上¥后的价格
 */
function addYuan(price) {
    var out = '';
    if(price){
        out = '¥' + String(price);
    }
    return out;
}

/**
 * 显示支付时长的选择dialog
 */
function showSelectDialog() {
    selectMonth(4);
    $(".select-dialog").css({
        "visibility": "visible"
    });
}

/**
 * 隐藏支付时长的选择的dialog
 */
function hideSelectDialog() {
    $(".select-dialog").css({
        "visibility": "hidden"
    });
}

var selectedYear = 0;

/**
 * 设置选中的月份
 *
 * @param {Number} month 选中的月份
 */
function selectMonth(month) {
    switch (month) {
        case 1:
            selectedYear = 1;
            break;
        case 2:
            selectedYear = 2;
            break;
        case 3:
            selectedYear = 3;
            break;
    }

    for (var i = 1; i <= 3; i++) {
        if (i == month) {
            $('#month-' + i).addClass('selected');
        } else {
            $('#month-' + i).removeClass('selected');
        }
    }
}

/**
 * 设置用户开通类型
 *
 * @param {Number} type
 */
function setMemberInfoType(type) {
    if (type == 0) {
        register();
        Cookies.set('free',true);
    } else {
        memberInfo.type = type;
        setPrice();
    }
}

/**
 * 设置用户开通选择的月份
 */
function setMemberInfoCount() {
    memberInfo.count = selectedYear*12;
    hideSelectDialog();
    register();
}


/**
 * 跳转到应用页面
 */
function skipToAppPage() {
    if(url == 'www.xingyunbooks.com' || url == '112.74.90.144'){
        window.location.href = '/app';
    }else{
        window.location.href = '/xyweb';
    }

}

/**
 * 跳转到app页面登陆
 */
function login() {
    Cookies.remove('memberInfo');
    Cookies.remove('free');
    Cookies.remove('experience');
    skipToAppPage();
}

/**
 * 跳转到app页面进行注册
 */
function register() {
    Cookies.remove('experience');
    Cookies.set('memberInfo', memberInfo);
    skipToAppPage();
}

/**
 * 立即体验
 */
function experience() {
    Cookies.remove('memberInfo');
    Cookies.remove('free');
    Cookies.set('experience', true);
    skipToAppPage();
}