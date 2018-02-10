'use strict';

// 对账单页面
angular.module('XY').controller('StatementWechatController', ['$scope', '$log', 'PageService', 'EventService', 'StatementWechatModel', 'NetworkService',
	function($scope, $log, PageService, EventService, model, NetworkService) {
		$scope.model = model;
		var qrcode;
		// $scope.statementWechat_wechatQR = "http://www.xingyunbooks.com";
		// 监听guid 改变新的guid后改变二维码
		$scope.$watch('model.statementWechat_wechatQR',function(newValue,oldValue){
			if(document.getElementById("wechat-QR")) {
				if (oldValue != newValue ){ // 新值不等于旧值 && qrcode 为真的时候（页面刚加载就监听到变化，但是还没有创建qrcode此时的qrcode为false） 
					$scope.statementWechat_wechatQR = NetworkService.getDomain() + 'index.php?m=Home&c=StatementOfAccount&a=show_statement&s_guid=' + newValue;
					qrcode = new QRCode(document.getElementById("wechat-QR"), {
					    text: $scope.statementWechat_wechatQR,
					    width: 150,
					    height: 150,
					    correctLevel : QRCode.CorrectLevel.H,
					});
				}
			}
		})
		
		 $scope.statementWechat_quit = function() {
			EventService.emit('CLOSE_STATEMENTWECHAT');
        }
	}
]);