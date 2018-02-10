'user strict'

xy.controller('WithdrawController', ['$scope', 'WithdrawModel', '$log', 'PageService', 'NetworkService', 'EventService',
	function($scope, model, $log, PageService, NetworkService, EventService) {
		$scope.orderInfo = model.orderInfo; //单据信息;
		var orderInfo = {};
		angular.copy($scope.orderInfo, orderInfo); //备份单据信息
		$scope.cartAgent = model.orderInfo.cartAgent; //购物车信息
		$scope.accountTypeList = ['银行', '网络', '现金'] //账户类型
		$scope.accountData = model.accountData; //账户信息
		// 初始化开单显示时间
		var time = new Date();
		if($scope.orderInfo.fid) {
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

		/**
		 * 点击有下拉框的输入框
		 * @param {Object} item 购物车项
		 * @param {Object} index 购物车内序号
		 */
		$scope.checkInput = function(item, index) {
			$scope.cartAgent.setEidtItem(item); //设置为当前编辑项
			$scope.cartAgent.hiseList(index); //只显示选中列表
		}
		$scope.clearItem = function(item) {
			item.account_creator = '';
			item.account_operate.account_number = '';
			item.account_balance = 0;
			item.account_operate.account_id = '';
			$scope.updataCost(item);
		}
		/**
		 * 选择账户类型
		 * @param {Object} item 购物车项
		 * @param {Object} x 列表项
		 */
		$scope.setItemType = function(item, x) {
			item.account_type = x;
			item.accountList = $scope.accountData[x]
			$scope.cartAgent.hiseList(-1); //隐藏
		}
		/**
		 *
		 * @param {Object} item 购物车项
		 * @param {Object} x 列表项
		 */
		$scope.setItemAccount = function(item, x) {
			for(var v of $scope.orderInfo.cartAgent.cartItemList) {
				if(v.account_operate.account_id == x.account_id &&　!$scope.orderInfo.fid) {
					PageService.showConfirmDialog('此账户已在提现列表中！请勿重复选择！', ['确认'])
					return
				}
			}
			item.account_operate.account_id = x.account_id;
			item.account_operate.account_number = x.account_number;
			item.account_name = x.account_name;
			item.account_creator = x.account_creator;
			item.account_source_name = x.account_source_name;
			item.account_source_type = x.account_source_type;
			item.account_balance = Number(x.account_balance);
		}
		/**
		 * 输入账户名的时候自动匹配
		 * @param {Object} item
		 */
		$scope.itemChange = function(item) {
			var a = true;
			for(var v of item.accountList) {
				if(v.account_name == item.account_name) {
					$scope.setItemAccount(item, v);
					a = false;
				}
			}
			if(a) {
				$scope.clearItem(item);
			}
		}
		/**
		 * 全局监听点击事件是否隐藏下拉列表  按钮出实效(待研究)
		 */

		$scope.updataCost = function(item) {
			$scope.orderInfo.income = 0;
			for(var v of $scope.cartAgent.cartItemList) {
				if (v.account_operate.cost < 0) {
					v.account_operate.cost = 0 - v.account_operate.cost;
				}
				if (v.account_operate.cost > 0) {
					$scope.orderInfo.income += v.account_operate.cost;
				}
			}
		}

		$scope.addCartItem = function() {
			$scope.cartAgent.addCartItem();

		}
		$scope.deleteCartItem = function(index) {
			$scope.cartAgent.deleteCartItem(index);
			$scope.updataCost();
		}
		/**
		 * 退出检查是否需要保存草稿
		 */
		$scope.quit = function() {
			if(angular.equals($scope.orderInfo, orderInfo)) {
				PageService.closeDialog(model.Dialog);
			} else {
				PageService.showConfirmDialog('单据未提交,确认退出？', ['直接退出', '保存为草稿单据并退出', '取消'], function() {
					PageService.closeDialog(model.Dialog);
				}, function() {
					$scope.createOrder(true);
				});
			}
		}

		$scope.createOrder = function(isDraft) {
			var apiName = 'createFinanceOrder';
			var dataToSend = {
				'class': 83,
				'income': $scope.orderInfo.income,
				'remark': $scope.orderInfo.remark,
				'reg_time': $scope.orderInfo.reg_time,
				'cart': {
					data: []
				}
			};
			for(var v of $scope.cartAgent.cartItemList) {
				if(v.account_operate.account_number == '') {
					PageService.showConfirmDialog('提现列表数据不合法!', ['确认'])
					return
				}
				if(v.account_operate.cost <= 0) {
					PageService.showConfirmDialog('提现列表中某个金额不合法!', ['确认'])
					return
				}
				delete v.accountList;
				delete v.fillItem;
			}

			angular.copy($scope.cartAgent.cartItemList, dataToSend.cart.data);
			dataToSend.cart = JSON.stringify(dataToSend.cart);

			if ($scope.orderInfo.fid) {
				dataToSend.fid = $scope.orderInfo.fid;
			}
			//如果isDraft==true 创建的则是草稿单
			if(isDraft) {
				//创建草稿单接口
				apiName = 'createFinanceOrderDraft';
			}

			$log.debug('createOrder', dataToSend)
			NetworkService.request(apiName, dataToSend, function(data) {
				PageService.showSharedToast('开单成功!');
				PageService.closeDialog(model.Dialog);
				if($scope.orderInfo.fid) {
					EventService.emit(EventService.ev.START_VIEW_YIQIJI_DRAFT, 1); //跳回草稿单据
				} else {
					EventService.emit(EventService.ev.START_VIEW_ALL_ORDER); //打开查看所有单据
				}
			})
		}

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

		//fid说明草稿单继续
		if ($scope.orderInfo.fid) {
			for (var v of $scope.orderInfo.cartAgent.cartItemList) {
				if (v.account_source_type) {
					$scope.setItemType(v,getSourcetType(v.account_source_type));
					if (v.account_name) {
						for (var v2 of v.accountList) {
							if (v2.account_name == v.account_name) {
								$scope.setItemAccount(v,v2)
							}
						}
					}
				}
			}

		}

		$(function() {
			angular.copy($scope.orderInfo, orderInfo);
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

		document.addEventListener('click', function(e) {
			if(!$(e.target).hasClass('dropdown-list') && 　!$(e.target).hasClass('dropdown-input')) {
				$scope.cartAgent.hiseList('-1');
			}
		})
	}
])