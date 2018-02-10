'use strict'

xy.controller('TransferAccountsController', ['$scope', 'TransferAccountsModel', '$log', 'EventService', 'PageService', 'NetworkService',
	function($scope, model, $log, EventService, PageService, NetworkService) {

		$scope.orderInfo = model.orderInfo; //单据信息
		var orderInfo = {};
		$scope.accountTypeList = ['银行', '网络', '现金'] //账户类型
		$scope.accountData = model.accountData; //账户信息
		$scope.accountList = []; //account列表默认显示全部
		$scope.targetList = []; //account列表默认显示全部
		$scope.nowEditItem = -1; //正在编辑的项
		$scope.showList = [
			[0, 0],
			[0, 0]
		]; //下拉框是否显示数组
		$scope.accountCart = {
			account_id: '',
			accountType: '',
			account_name: '',
			account_number: '',
			account_souce_name: '',
			account_souce_type: '',
			account_balance: 0,
			remain: 0,
		}
		$scope.targetCart = {
			target_id: '',
			targetType: '',
			target_name: '',
			target_number: '',
			target_souce_name: '',
			target_souce_type: '',
			target_balance: 0,
			remain: 0,
		}

		/**
		 *
		 * @param {Object} item 当前编辑项
		 * @param {Object} index 下拉框index
		 */
		$scope.checkInput = function(item, index) {
			if($scope.nowEditItem == item || $scope.nowEditItem == -1) {
				$scope.showList[item] = [0, 0];
				if(index != -1) {
					$scope.showList[item][index] = 1;
					$scope.nowEditItem = item;
				}
			} else {
				$scope.showList[$scope.nowEditItem] = [0, 0];
				if(index != -1) {

				}
				$scope.nowEditItem = item;
				$scope.showList[item][index] = 1;
			}
		}

		/**
		 * 选择账户类型
		 * @param {Object} item
		 */
		$scope.setItemAccountType = function(item) {
			$scope.clearCartItem(1);
			$scope.accountCart.accountType = item;
			$scope.accountList = $scope.accountData[item]

		}

		/**
		 * 选择账户
		 * @param {Object} item
		 */
		$scope.setItemAccountName = function(item) {
			$scope.accountCart.account_id = item.account_id;
			$scope.accountCart.account_name = item.account_name;
			$scope.accountCart.account_number = item.account_number;
			$scope.accountCart.account_balance = item.account_balance;
			$scope.accountCart.account_source_name = item.account_source_name;
			$scope.accountCart.account_source_type = item.account_source_type;
			$scope.updataBalance();
		}
		/**
		 * 手动输入账户名时 判断是否在列表里，在就选中
		 * @param {Object} index
		 */
		$scope.changeName = function(index) {
			if(index == 1) {
				for(var v of $scope.accountList) {
					if(v.accountName == $scope.accountCart.account_name) {
						setItemAccountName(v);
					}
				}
			}
		}
		/**
		 * 更新显示转前转余额
		 *
		 */
		$scope.updataBalance = function() {
			if($scope.accountCart.account_id == $scope.targetCart.target_id) {
				//转入转出同一个账号 转账后余额不变
				$scope.accountCart.remain = Number($scope.accountCart.account_balance);
				$scope.targetCart.remain = Number($scope.targetCart.target_balance);
			} else {
				//转入转出不是一个账户  计算转账后余额
				$scope.accountCart.remain = Number($scope.accountCart.account_balance) - Number($scope.orderInfo.cart.data.account_operate.cost);
				$scope.targetCart.remain = Number($scope.targetCart.target_balance) + Number($scope.orderInfo.cart.data.account_operate.cost);
			}
		}
		/**
		 * 转入账户类型选择
		 * @param {Object} item
		 */
		$scope.setItemTargetType = function(item) {
			$scope.clearCartItem(2);
			$scope.targetCart.targetType = item;
			$scope.targetList = $scope.accountData[item]
		}
		/**
		 * 转入账户，账号选择
		 * @param {Object} item
		 */
		$scope.setItemTargetName = function(item) {
			$scope.targetCart.target_id = item.account_id;
			$scope.targetCart.target_name = item.account_name;
			$scope.targetCart.target_number = item.account_number;
			$scope.targetCart.target_balance = item.account_balance;
			$scope.targetCart.target_source_name = item.account_source_name;
			$scope.targetCart.target_source_type = item.account_source_type;
			$scope.updataBalance();
		}
		/**
		 * 清空行
		 * @param {Object} index
		 */
		$scope.clearCartItem = function(index) {
			if(index == 1) {
				$scope.accountCart = {
					account_id: '',
					accountType: '',
					account_name: '',
					account_number: '',
					account_souce_name: '',
					account_souce_type: '',
					account_balance: 0,
					remain: 0,
				}
				$scope.accountList = model.allAccountList; //account列表默认显示全部
			} else {
				$scope.targetCart = {
					target_id: '',
					targetType: '',
					target_name: '',
					target_number: '',
					target_souce_name: '',
					target_souce_type: '',
					target_balance: 0,
					remain: 0,
				}
				$scope.targetList = model.allAccountList; //account列表默认显示全部
			}
			$scope.orderInfo.cart.data.account_operate.cost = 0;
			$scope.updataBalance();
		}

		$scope.quit = function (){
			if(angular.equals($scope.orderInfo, orderInfo)) {
				PageService.closeDialog(model.Dialog);
			}else {
				PageService.showConfirmDialog('单据未提交,确认退出？', ['直接退出', '保存为草稿单据并退出', '取消'], function() {
					PageService.closeDialog(model.Dialog);
				}, function() {
					$scope.createOrder(true);
				});
			}
		}
		$scope.createOrder = function(isDraft) {
			//默认几口为新建单据
			var apiName = 'createFinanceOrder';
			var dataToSend = {};
			if($scope.accountCart.account_id == $scope.targetCart.target_id) {
				PageService.showConfirmDialog('转入转出不能为同一账户！', ['确认']);
				return
			}
			delete $scope.orderInfo._class;
			angular.copy($scope.orderInfo, dataToSend);
			dataToSend.income = dataToSend.cart.data.account_operate.cost;
			dataToSend.cart.data.account_operate.account_id = $scope.accountCart.account_id; //转出账户id
			dataToSend.cart.data.account_operate.target_id = $scope.targetCart.target_id; //转出入账户id

			dataToSend.cart.data.account_number = $scope.accountCart.account_number; //转出账户号
			dataToSend.cart.data.account_name = $scope.accountCart.account_name; //账户简称
			dataToSend.cart.data.account_source_name = $scope.accountCart.account_source_name; //来源名称
			dataToSend.cart.data.account_source_type = $scope.accountCart.account_source_type; //来源名称
			dataToSend.cart.data.account_balance = $scope.accountCart.account_balance; //转前余额
			dataToSend.cart.data.target_number = $scope.targetCart.target_number; //转出账号
			dataToSend.cart.data.target_name = $scope.targetCart.target_name; //转出账户id
			dataToSend.cart.data.target_source_name = $scope.targetCart.target_source_name; //来源名称
			dataToSend.cart.data.target_source_type = $scope.targetCart.target_source_type; //来源名称
			dataToSend.cart.data.target_balance = $scope.targetCart.target_balance; //转前余额
			dataToSend.cart = JSON.stringify(dataToSend.cart)
//			return
			//如果isDraft==true 创建的则是草稿单
			if(isDraft) {
				//创建草稿单接口
				apiName = 'createFinanceOrderDraft';
			}
			NetworkService.request(apiName, dataToSend, function(data) {
				PageService.showSharedToast('开单成功!');
				PageService.closeDialog(model.Dialog);
				if($scope.orderInfo.fid || isDraft) {
					EventService.emit(EventService.ev.START_VIEW_YIQIJI_DRAFT, 1); //打开草稿单据
				} else {
					EventService.emit(EventService.ev.START_VIEW_ALL_ORDER); //打开查看所有单据
				}
				$log.debug('create', $scope.orderInfo._class, 'successs', data)
			})
		}

		// 初始化开单显示时间
		var time = new Date();
		//初始化默认选中账户类型
		$scope.setItemAccountType('银行'); //设置转出账户 默认选中银行
		$scope.setItemTargetType('银行'); //设置转入账户 默认选中银行
		/**
		 * 判断类型转换成文字
		 * @param {Object} type 账户类型
		 */
		function getSourcetType(type) {
			var typeName = '';
			if(type == 1) {
				typeName = '银行';
			}
			if(type == 2) {
				typeName = '网络';
			}
			if(type == 3) {
				typeName = '现金';
			}
			return typeName;
		}
		if($scope.orderInfo.fid) {
			//fid说明是草稿单继续开单
			var cartData = $scope.orderInfo.cart.data;
			$scope.setItemAccountType(getSourcetType(cartData.account_source_type)); //选中转出账户类型
			$scope.setItemTargetType(getSourcetType(cartData.target_source_type)); //选中转入账户类型
			for(var v of model.allAccountList) {
				if(cartData.account_number == v.account_number) {
					$scope.setItemAccountName(v); //选中转出账户
				}
				if(cartData.target_number == v.account_number) {
					$scope.setItemTargetName(v); //选中转出账户
				}
			}
			$scope.orderInfo.cart.data.account_operate.cost = model.cost;
			time = new Date($scope.orderInfo.reg_time * 1000);
		}

		if(model.timeModeTime) {
			time = new Date(model.timeModeTime)
			$scope.orderInfo.reg_time = time.getTime() / 1000 + 16 * 3600 - 60; //初始化时间到选中日期的23:59
		}
		var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
		var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
		$scope.reg_time = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
		$scope.orderInfo.reg_time = time.getTime() / 1000;

		$(function() {
			$scope.updataBalance();
			angular.copy($scope.orderInfo, orderInfo)
			$("#datepicker1").datepicker({
				showAnim: 'slide',
				onSelect: function(timeStr) {
					$scope.orderInfo.reg_time = new Date(timeStr).getTime() / 1000;
					$log.debug('datepicker1', new Date(timeStr).getTime() / 1000);

					//当前小时 分 秒 判断选择日期是否为今天？返回当前时间 ：返回选择日期的23:59
					var Hours = new Date().getHours(),
						Minutes = new Date().getMinutes(),
						Seconds = new Date().getSeconds();
					if($scope.orderInfo.reg_time - 8 * 3600 == parseInt(new Date() / 1000) - Hours * 3600 - Minutes * 60 - Seconds) {
						$scope.orderInfo.reg_time = new Date().getTime() / 1000;
						$log.debug("testnow:", $scope.orderInfo.reg_time, new Date($scope.orderInfo.reg_time * 1000));
					} else {
						$scope.orderInfo.reg_time = $scope.orderInfo.reg_time + 16 * 3600 - 60;
						$log.debug("testnow:", $scope.orderInfo.reg_time, new Date($scope.orderInfo.reg_time * 1000));
					}
				},
				showButtonPanel: true,
			})
		})

		/**
		 * 全局监听点击事件是否隐藏下拉列表  按钮出实效(待研究)
		 */
		document.addEventListener('click', function(e) {
			if(!$(e.target).hasClass('dropdown-list') && 　!$(e.target).hasClass('dropdown-input')) {
				$scope.showList = [
					[0, 0],
					[0, 0]
				];
			}
		})
	}
])