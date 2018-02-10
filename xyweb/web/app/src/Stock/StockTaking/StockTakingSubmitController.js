'use strict'

angular.module('XY').controller('StockTakingSubmitController', ['EventService', '$scope', '$log', 'StockTakingModel', 'StockService', 'UserService', '$timeout', 'LockService', 'PageService',
	function (EventService, $scope, $log, model, StockService, UserService, $timeout, LockService, PageService) {

		$scope.model = model;
		$scope.orderInfo = model.orderInfo;
		$scope.cartAgent = model.orderInfo.cartAgent;
		$scope.isUserListShow = false;
		$scope.userList = UserService.getUserList();
		$scope.rpg = UserService.getLoginStatus().rpg;

		// 初始化开单显示时间
        var time = new Date();
        var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
        var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        $scope.reg_time = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
        // 使用jqUI
        $(function () {
            $scope.orderInfo.reg_time = Math.floor(new Date().getTime() /1000) ;
            $("#datepicker1").datepicker({
            	showAnim : 'slide',
                onSelect: function (timeStr) {
                    $scope.orderInfo.reg_time = new Date(timeStr).getTime() /1000 ;
                    $log.debug('datepicker1',new Date(timeStr).getTime() /1000 );
                    
                    //当前小时 分 秒 判断选择日期是否为今天？返回当前时间 ：返回选择日期的23:59
                    var Hours=new Date().getHours(),
                    Minutes=new Date().getMinutes(),
                    Seconds=new Date().getSeconds();
                    if($scope.orderInfo.reg_time-8*3600 == parseInt(new Date()/1000)-Hours*3600-Minutes*60-Seconds){
                        $scope.orderInfo.reg_time = new Date().getTime() /1000;
                        $log.debug("testnow:",$scope.orderInfo.reg_time,new Date($scope.orderInfo.reg_time*1000));
                    }else{
                        $scope.orderInfo.reg_time = $scope.orderInfo.reg_time + 16 * 3600 - 60 ;
                        $log.debug("testnow:",$scope.orderInfo.reg_time,new Date($scope.orderInfo.reg_time*1000));
                    }
                },
                showButtonPanel: true,
            })

        });

		// 盘点人输入框内容
		$scope.userNameInput = $scope.model.orderInfo.check_name;


		$scope.clickDelete = function (item) {
			$scope.orderInfo.cartAgent.deleteItem(item)
		};

		// 点击用户名输入框
		$scope.clickUserInput = function () {
			$scope.isUserListShow = true
		};

		// 点击自动完成中列表中的用户
		$scope.clickUser = function (item) {
			// 选定盘点人
			$scope.orderInfo.check_uid = Number(item.uid)
			// 输入框上也填上
			$scope.userNameInput = item.name
		};

		// watch输入框,输入框内容变化时自动匹配用户，匹配上了自动选定往来单位
		$scope.$watch('userNameInput', function (newValue) {

			// $log.debug('userNameInput changed',newValue)

			// 名字有变化查找用户
			var checker = UserService.findUserByName(newValue)
			// 找到或没找到都立即设定盘点单信息
			if (checker) {
				$scope.orderInfo.check_uid = checker.uid
			} else {
				$scope.orderInfo.check_uid = undefined
			}
		});

		//当发生点击body事件，判断是否点发生在客户姓名输入框
		var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function (angularEvent, domEvent) {

			$timeout(function () {
				// 如果没在输入框上也没在下来列表上就关闭下拉列表
				if (!($(domEvent.target).hasClass('auto-complete-content'))) {
					$scope.isUserListShow = false
				}

			})
		})

		$scope.$on('$destroy', function () {
			clickHandle();
		});
		$scope.clickExecute = function () {
			if ($scope.orderInfo.check_uid == undefined) {
				PageService.showConfirmDialog('<div class="title"><a class="red">盘点人错误</a></div>'
					+ '<div><a>可能是您直接在输入框中手动输入了不存在的<span class="red">盘点人名称</span>所导致。您可以通过以下三种中的任意一种方法解决：</a></div>'
					+ '<div><a>1.选择<span class="red">盘点人</span>下拉列表中已有的盘点人名称，而不是手动输入。</a></div>'
					+ '<div><a>2.手动输入<span class="red">盘点人</span>下拉列表中已经存在的盘点人名称。</a></div>'
					+ '<div><a>3.如果该类别确实是新员工，请先保存为草稿，然后去成员管理中<span class="red">新建成员</span>。</a></div>');
				return;
			}
			 // $log.error($scope.orderInfo);
			StockService.createStockTaking($scope.orderInfo, LockService.token);
		}
	}
])
