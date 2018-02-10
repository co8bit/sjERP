angular.module('wechatOnMobile',[]).controller('wechatOnMobile',['$scope','$filter',function ($scope,$filter) {
	$scope.detailIsShow = false;
	$scope.cid_name = '';
	$scope.balance = 0;
	$scope.itemDetail = {};
	$scope.data = [
        {
            "class": "1",
            "value": "165",
            "operator_name": "跃迁科技",
            "reg_time": "1490633719",
            "cart": [
                {
                    "sku_id": 28,
                    "quantity": 55,
                    "unitPrice": 3,
                    "pilePrice": 165,
                    "cost": "641.5",
                    "sn": "SKU000028",
                    "spu_name": "香槟黄",
                    "spec_name": "41只装"
                }
            ],
            "cid_name": "零售客户",
            "off": "0",
            "cash": "0",
            "bank": "0",
            "online_pay": "0",
            "income": "0",
            "contact_name": "零售",
            "receivable": "165",
            "remain": "165",
            "sn": "OXC000009"
        },
        {
            "admin_uid": "1",
            "class": "1",
            "value": "9801",
            "remark": "",
            "operator_uid": "1",
            "operator_name": "跃迁科技",
            "history": "跃迁科技(编号:USN000001)于2017-03-28日00:50:52创建;跃迁科技(编号:USN000001)于2017-03-28日00:50:52修改状态为已完成;",
            "status": "1",
            "reg_time": "1490633452",
            "update_time": "1490633452",
            "cart": [
                {
                    "sku_id": 28,
                    "quantity": 99,
                    "unitPrice": 99,
                    "pilePrice": 9801,
                    "cost": "641.5",
                    "sn": "SKU000028",
                    "spu_name": "香槟黄",
                    "spec_name": "41只装"
                }
            ],
            "cid": "1",
            "cid_name": "零售客户",
            "off": "0",
            "cash": "0",
            "bank": "0",
            "online_pay": "0",
            "income": "0",
            "name": "",
            "oid": "35",
            "contact_name": "零售",
            "mobile": "",
            "park_address": "南京兴山区",
            "car_license": "",
            "receivable": "9801",
            "balance": "-9801",
            "remain": "9801",
            "history_balance": "1309619.1",
            "total_balance": "1299818.1",
            "leave_time": "0",
            "exceptionNo": "0",
            "exception": "",
            "GeTuiGet": "0",
            "wid": "0",
            "num": "0",
            "check_uid": "0",
            "check_name": "未填写",
            "fid": "0",
            "sn": "OXC000008"
        },
        {
            "admin_uid": "1",
            "class": "1",
            "value": "7744",
            "remark": "",
            "operator_uid": "1",
            "operator_name": "跃迁科技",
            "history": "跃迁科技(编号:USN000001)于2017-03-28日00:49:59创建;跃迁科技(编号:USN000001)于2017-03-28日00:49:59修改状态为已完成;",
            "status": "1",
            "reg_time": "1490633399",
            "update_time": "1490633399",
            "cart": [
                {
                    "sku_id": 28,
                    "quantity": 88,
                    "unitPrice": 88,
                    "pilePrice": 7744,
                    "cost": "641.5",
                    "sn": "SKU000028",
                    "spu_name": "香槟黄",
                    "spec_name": "41只装"
                }
            ],
            "cid": "1",
            "cid_name": "零售客户",
            "off": "0",
            "cash": "0",
            "bank": "0",
            "online_pay": "0",
            "income": "0",
            "name": "",
            "oid": "34",
            "contact_name": "零售",
            "mobile": "",
            "park_address": "",
            "car_license": "",
            "receivable": "7744",
            "balance": "-7744",
            "remain": "7744",
            "history_balance": "1317363.1",
            "total_balance": "1309619.1",
            "leave_time": "0",
            "exceptionNo": "0",
            "exception": "",
            "GeTuiGet": "0",
            "wid": "0",
            "num": "0",
            "check_uid": "0",
            "check_name": "未填写",
            "fid": "0",
            "sn": "OXC000007"
        },
        {
            "admin_uid": "1",
            "class": "1",
            "value": "9801",
            "remark": "",
            "operator_uid": "1",
            "operator_name": "跃迁科技",
            "history": "跃迁科技(编号:USN000001)于2017-03-28日00:46:12创建;跃迁科技(编号:USN000001)于2017-03-28日00:46:12修改状态为已完成;",
            "status": "1",
            "reg_time": "1490633172",
            "update_time": "1490633172",
            "cart": [
                {
                    "sku_id": 28,
                    "quantity": 99,
                    "unitPrice": 99,
                    "pilePrice": 9801,
                    "cost": "641.5",
                    "sn": "SKU000028",
                    "spu_name": "香槟黄",
                    "spec_name": "41只装"
                }
            ],
            "cid": "1",
            "cid_name": "零售客户",
            "off": "0",
            "cash": "0",
            "bank": "0",
            "online_pay": "0",
            "income": "0",
            "name": "",
            "oid": "33",
            "contact_name": "零售",
            "mobile": "",
            "park_address": "",
            "car_license": "",
            "receivable": "9801",
            "balance": "-9801",
            "remain": "9801",
            "history_balance": "1327164.1",
            "total_balance": "1317363.1",
            "leave_time": "0",
            "exceptionNo": "0",
            "exception": "",
            "GeTuiGet": "0",
            "wid": "0",
            "num": "0",
            "check_uid": "0",
            "check_name": "未填写",
            "fid": "0",
            "sn": "OXC000006"
        },
        {
            "admin_uid": "1",
            "class": "1",
            "value": "7744",
            "remark": "",
            "operator_uid": "1",
            "operator_name": "跃迁科技",
            "history": "跃迁科技(编号:USN000001)于2017-03-28日00:45:20创建;跃迁科技(编号:USN000001)于2017-03-28日00:45:20修改状态为已完成;",
            "status": "1",
            "reg_time": "1490633120",
            "update_time": "1490633120",
            "cart": [
                {
                    "sku_id": 28,
                    "quantity": 88,
                    "unitPrice": 88,
                    "pilePrice": 7744,
                    "cost": "641.5",
                    "sn": "SKU000028",
                    "spu_name": "香槟黄",
                    "spec_name": "41只装"
                }
            ],
            "cid": "1",
            "cid_name": "零售客户",
            "off": "0",
            "cash": "0",
            "bank": "0",
            "online_pay": "0",
            "income": "0",
            "name": "",
            "oid": "32",
            "contact_name": "零售",
            "mobile": "",
            "park_address": "",
            "car_license": "",
            "receivable": "7744",
            "balance": "-7744",
            "remain": "7744",
            "history_balance": "1334908.1",
            "total_balance": "1327164.1",
            "leave_time": "0",
            "exceptionNo": "0",
            "exception": "",
            "GeTuiGet": "0",
            "wid": "0",
            "num": "0",
            "check_uid": "0",
            "check_name": "未填写",
            "fid": "0",
            "sn": "OXC000005"
        },
        {
            "admin_uid": "1",
            "class": "1",
            "value": "250050",
            "remark": "",
            "operator_uid": "1",
            "operator_name": "跃迁科技",
            "history": "跃迁科技(编号:USN000001)于2017-03-25日16:24:12创建;跃迁科技(编号:USN000001)于2017-03-25日16:24:12修改状态为已完成;",
            "status": "1",
            "reg_time": "1490430252",
            "update_time": "1490430252",
            "cart": [
                {
                    "sku_id": 24,
                    "quantity": 55,
                    "unitPrice": 33,
                    "pilePrice": 1815,
                    "cost": "484.07",
                    "sn": "SKU000024",
                    "spu_name": "蕃木瓜色",
                    "spec_name": "05只装"
                },
                {
                    "sku_id": 28,
                    "quantity": 555,
                    "unitPrice": 444,
                    "pilePrice": 246420,
                    "cost": "641.5",
                    "sn": "SKU000028",
                    "spu_name": "香槟黄",
                    "spec_name": "41只装"
                },
                {
                    "sku_id": 47,
                    "quantity": 55,
                    "unitPrice": 33,
                    "pilePrice": 1815,
                    "cost": "484.96",
                    "sn": "SKU000047",
                    "spu_name": "玫瑰褐",
                    "spec_name": "57只装"
                }
            ],
            "cid": "1",
            "cid_name": "零售客户",
            "off": "0",
            "cash": "0",
            "bank": "0",
            "online_pay": "0",
            "income": "0",
            "name": "",
            "oid": "31",
            "contact_name": "零售",
            "mobile": "",
            "park_address": "",
            "car_license": "",
            "receivable": "250050",
            "balance": "-250050",
            "remain": "250050",
            "history_balance": "1584958.1",
            "total_balance": "1334908.1",
            "leave_time": "0",
            "exceptionNo": "0",
            "exception": "",
            "GeTuiGet": "0",
            "wid": "0",
            "num": "0",
            "check_uid": "0",
            "check_name": "未填写",
            "fid": "0",
            "sn": "OXC000004"
        },
        {
            "admin_uid": "1",
            "class": "4",
            "value": "7729.12",
            "remark": "这里是备注Quibusdam consequatur ullam harum atque a molestiae ut.",
            "operator_uid": "1",
            "operator_name": "跃迁科技",
            "history": "跃迁科技(编号:USN000001)于2017-03-22日19:01:42创建;跃迁科技(编号:USN000001)于2017-03-22日19:01:42修改状态为已完成;",
            "status": "1",
            "reg_time": "1490180502",
            "update_time": "1490180502",
            "cart": [
                {
                    "sku_id": 34,
                    "quantity": 16,
                    "unitPrice": 483.07,
                    "pilePrice": 7729.12,
                    "cost": "313.79",
                    "sn": "SKU000034",
                    "spu_name": "乌贼墨色",
                    "spec_name": "82只装"
                }
            ],
            "cid": "1",
            "cid_name": "零售客户",
            "off": "28.9",
            "cash": "431077.59",
            "bank": "180045.79",
            "online_pay": "102031.27",
            "income": "713154.65",
            "name": "",
            "oid": "20",
            "contact_name": "谢凤兰",
            "mobile": "15130701272",
            "park_address": "北京华龙区",
            "car_license": "浙YB8204",
            "receivable": "7700.22",
            "balance": "705454.43",
            "remain": "-705454.43",
            "history_balance": "0",
            "total_balance": "705454.43",
            "leave_time": "0",
            "exceptionNo": "0",
            "exception": "",
            "GeTuiGet": "0",
            "wid": "0",
            "num": "0",
            "check_uid": "0",
            "check_name": "未填写",
            "fid": "0",
            "sn": "OCT000002"
        },
        {
            "admin_uid": "1",
            "class": "4",
            "value": "15073.35",
            "remark": "这里是备注Quo aut eos non dolores eveniet excepturi est et.",
            "operator_uid": "1",
            "operator_name": "跃迁科技",
            "history": "跃迁科技(编号:USN000001)于2017-03-22日19:01:42创建;跃迁科技(编号:USN000001)于2017-03-22日19:01:42修改状态为已完成;",
            "status": "1",
            "reg_time": "1490180502",
            "update_time": "1490180502",
            "cart": [
                {
                    "sku_id": 51,
                    "quantity": 33,
                    "unitPrice": 8.61,
                    "pilePrice": 284.13,
                    "cost": "130.71",
                    "sn": "SKU000051",
                    "spu_name": "玫瑰褐",
                    "spec_name": "50只装"
                },
                {
                    "sku_id": 38,
                    "quantity": 22,
                    "unitPrice": 370.07,
                    "pilePrice": 8141.54,
                    "cost": "118.13",
                    "sn": "SKU000038",
                    "spu_name": "蓝色",
                    "spec_name": "44只装"
                },
                {
                    "sku_id": 4,
                    "quantity": 21,
                    "unitPrice": 235.37,
                    "pilePrice": 4942.77,
                    "cost": "372.53",
                    "sn": "SKU000004",
                    "spu_name": "那瓦霍白",
                    "spec_name": "68只装"
                },
                {
                    "sku_id": 29,
                    "quantity": 29,
                    "unitPrice": 58.79,
                    "pilePrice": 1704.91,
                    "cost": "188.03",
                    "sn": "SKU000029",
                    "spu_name": "乌贼墨色",
                    "spec_name": "83只装"
                }
            ],
            "cid": "1",
            "cid_name": "零售客户",
            "off": "28.12",
            "cash": "456555.01",
            "bank": "423662.03",
            "online_pay": "14331.86",
            "income": "894548.9",
            "name": "",
            "oid": "25",
            "contact_name": "郁芬",
            "mobile": "13932092459",
            "park_address": "香港萧山区",
            "car_license": "浙TT1861",
            "receivable": "15045.23",
            "balance": "879503.67",
            "remain": "-879503.67",
            "history_balance": "705454.43",
            "total_balance": "1584958.1",
            "leave_time": "0",
            "exceptionNo": "0",
            "exception": "",
            "GeTuiGet": "0",
            "wid": "0",
            "num": "0",
            "check_uid": "0",
            "check_name": "未填写",
            "fid": "0",
            "sn": "OCT000003"
        }
	];
	if ($scope.data[0].cid_name != undefined) {
		$scope.cid_name = $scope.data[0].cid_name;
	}

	
	for (var v in $scope.data) {
		$scope.balance += parseFloat($scope.data[v].remain);
	    reg_time_show = $filter('date')(parseInt($scope.data[v].reg_time)*1000, "yyyy-MM-dd hh:mm:ss"); 
	    $scope.data[v].reg_time_show = reg_time_show;
	    switch (parseInt($scope.data[v].class)){
	    	case 1:
	    		$scope.data[v].class_show = '销售单';	
	    		break;
	    	case 2:
	    		$scope.data[v].class_show = "销售退货单";
	    		break;
	    	case 3:
	    		$scope.data[v].class_show = "采购单";
	    		break;
	    	case 4:
	    		$scope.data[v].class_show = "采购退货单";
	    		break;
	    	case 5:
	    		$scope.data[v].class_show = "应收款调整单";
	    		break;
	    	case 6:
	    		$scope.data[v].class_show = "应付款调整单";
	    		break;
	    	case 71:
	    		$scope.data[v].class_show = "收款单";
	    		break;
	    	case 72:
	    		$scope.data[v].class_show = "付款单";
	    		break;
	    }
	}
	$scope.clickViewDetails = function (item) {
		$scope.detailIsShow = true;
		$scope.itemDetail = item.cart;
		$scope.detail = item;
	}
	$scope.closeDetail = function () {
		$scope.detailIsShow = false;
	}
}])

//$(function () {
//	var wechat = $('#wechat-content');
//	var detail = $('#detail');
//	detail.css('height',wechat.height() + 'px');
//	detail.addClass('hide');
//	alert(detail.height());
//});