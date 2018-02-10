//自定义过滤器

/**
 * 用户角色过滤器
 */

xy.filter('rpg', function () {
    return function (input) {

        var rpg = '';

        switch (input) {
            case '1':
                rpg = '创建者';
                break;
            case '2':
                rpg = '销售';
                break;
            case '3':
                rpg = '库管';
                break;
            case '4':
                rpg = '股东';
                break;
            case '7':
                rpg = '管理员';
                break;
            case '8':
                rpg = '创建者';
                break;
            case '9':
                rpg = '老板';
                break;
            case '10':
                rpg = '财务';
                break;
            case '11':
                rpg = '员工';
                break;
            case '12':
                rpg = '管理员';
                break;
        }

        return rpg;
    }
});

xy.filter('_class', function () {
    return function (input) {
    	var _class = '';
    	switch (Number(input)) {
			case 1:
				_class = '销售单';
				break;
			case 2:
				_class = '销售退货单';
				break;
			case 3:
				_class = '采购单';
				break;
			case 4:
				_class = '采购退货单';
				break;
			case 5:
				_class = '应收款调整';
				break;
			case 6:
				_class = '应付款调整';
				break;
			case 51:
				_class = '报溢单';
				break;
			case 52:
				_class = '报损单';
				break;
			case 53:
				_class = '盘点单';
				break;
			case 54:
				_class = '调拨单';
				break;
			case 71:
				_class = '收款单';
				break;
			case 72:
				_class = '付款单';
				break;
			case 73:
				_class = '其他收入单';
				break;
			case 74:
				_class = '费用单';
				break;
			case 81:
				_class = '收入单';
				break;
			case 82:
				_class = '支出单';
				break;
			case 83:
				_class = '提现单';
				break;
			case 84:
				_class = '转账单';
				break;
			case 85:
				_class = '发票填补单';
				break;

    	}
		return _class;
	}
});

xy.filter('status', function (){
	return function (input){
		var status = '';
		switch (Number(input)){
			case 0:
				status = '错误状态';
				break;
			case 1:
				status = '已完成';
				break;
			case 2:
				status = '异常';
				break;
			case 3:
				status = '已作废';
				break;
			case 4:
				status = '立即处理';
				break;
			case 5:
				status = '暂缓发货';
				break;
			case 6:
				status = '正在通知库管';
				break;
			case 7:
				status = '已通知库管，库管未确认';
				break;
			case 8:
				status = '库管已打印单据，但未出库';
				break;
			case 9:
				status = '已出库但未送达';
				break;
			case 10:
				status = '库管已打印单据，但未入库';
				break;
			case 11:
				status = '待审核-暂缓发货';
				break;
			case 12:
				status = '待审核-立即处理';
				break;
			case 81:
				status = '财务待审核';
				break;
			case 82:
				status = '财务审核通过';
				break;
			case 83:
				status = '财务审核未通过';
				break;
			case 84:
				status = '老板审核通过';
				break;
			case 85:
				status = '老板审核未通过';
				break;
			case 90:
				status = '未完成';
				break;
			case 91:
				status = '红冲单据';
				break;
			case 92:
				status = '红冲附属单';
				break;
			case 99:
				status = '系统自动创建的期初应收、期初应付';
				break;
			case 100:
				status = '草稿单';
				break;
		}
		return status;
	}
});

xy.filter('accountType', function () {
    return function (input) {
    	var accountType = '';
    	switch (Number(input)) {
			case 1:
				accountType = '银行';
				break;
			case 2:
				accountType = '网络';
				break;
			case 3:
				accountType = '现金';
				break;
    	}

		return accountType;
	}
});

xy.filter('time', function () {
    return function (input) {
    	var time = '';
    	var t = new Date(input*1000);
    	var y = t.getFullYear();
    	var m = t.getMonth() + 1;
    	var d = t.getDate();
    	var h = t.getHours();
    	var mi = t.getMinutes();
    	if (d < 10) {
    		d = '0' + d;
    	}
		if (mi < 10) {
			mi = '0' + mi;
		}
    	time = y + '/' + m + '/' + d + ' ' + h + ':' + mi;


		return time;
	}
});