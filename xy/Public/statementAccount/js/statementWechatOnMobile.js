angular.module('wechatOnMobile',[]).controller('wechatOnMobile',['$scope', '$filter', '$http',function ($scope, $filter, $http) {
	$scope.detailIsShow = false;
	$scope.cid_name = '';
	$scope.balance = 0;
	$scope.itemDetail = {};
    $scope.passwdInputIsShow = true;
    $scope.changeStep = function (isShow){
        $scope.step1isShow = false;console.log('true')
        
    }
    var oneTouce = true;
    $scope.lookInfo = function (){
        if (oneTouce) {
            oneTouce = false;
            var _url ;
            var _href = window.location.host;
            if( _href == '112.74.90.144'){
                _url = 'http://112.74.90.144/server/index.php?m=Home&c=StatementOfAccount&a=show_statement';
            }
            if( _href == 'www.xingyunbooks.com'){
                _url = 'http://www.xingyunbooks.com/server/index.php?m=Home&c=StatementOfAccount&a=show_statement';
            }
            if (!$scope.passwd){
                alert('密码为空')
                return
            }
            var dataToSend = {
                s_pwd: $scope.passwd,
                s_guid: ($('#guid').text()).trim(),
            }
            $http({
                method: 'POST',
                url: _url,
                data: dataToSend,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest: function(obj) {  
                  var str = [];  
                  for (var s in obj) {  
                    str.push(encodeURIComponent(s) + "=" + (encodeURIComponent(obj[s])).trim());  
                  }  
                  return str.join("&");  
                }  
            }).then(
                function successCallback(data) {
                    console.error(data)
                    if(data.data.EC == 1){
                        console.log(data);
                        $scope.data = [];
                        angular.copy(data.data.data, $scope.data);
                        $scope.updatePrice();
                        $scope.passwdInputIsShow = false;
                    }else{
                        alert('密码错误！')
                        console.error(data.data.MSG)
                    }
        		}, function errorCallback(response) {
                        // 请求失败执行代码
                });
            setTimeout(function(){
                oneTouce = true;
            },2000)
        }else{
            alert('操作频繁，稍后再试！')
        }
	}
    $scope.$watch('passwdInputIsShow',function(newValue){
        console.log('new',newValue)
    })
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
        }
	];

    $scope.updatePrice = function (){
    	if ($scope.data[0].cid_name != undefined) {
    		$scope.cid_name = $scope.data[0].cid_name;
    	}
    	for (var v of $scope.data) {
    		$scope.balance += parseFloat(v.remain);
    	    reg_time_show = $filter('date')(parseInt(v.reg_time)*1000, "yyyy-MM-dd HH:mm:ss");
    	    v.reg_time_show = reg_time_show;
    	    switch (parseInt(v.class)){
    	    	case 1:
    	    		v.class_show = '销售单';
    	    		break;
    	    	case 2:
    	    		v.class_show = "销售退货单";
    	    		break;
    	    	case 3:
    	    		v.class_show = "采购单";
    	    		break;
    	    	case 4:
    	    		v.class_show = "采购退货单";
    	    		break;
    	    	case 5:
    	    		v.class_show = "应收款增加单";
    	    		break;
    	    	case 6:
    	    		v.class_show = "应付款增加单";
    	    		break;
    	    	case 71:
    	    		v.class_show = "收款单";
    	    		break;
    	    	case 72:
    	    		v.class_show = "付款单";
    	    		break;
    	    }
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