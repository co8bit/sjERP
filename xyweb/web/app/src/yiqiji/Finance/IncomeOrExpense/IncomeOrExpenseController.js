//CreateIncomeController
'use strict';

angular.module('XY').controller('IncomeOrExpenseController', ['EventService', '$scope', '$log', 'IncomeOrExpenseModel', 'PageService', 'NetworkService', 'ViewOrderModel', 'UserService',
	function(EventService, $scope, $log, model, PageService, NetworkService, ViewOrderModel, UserService) {
		$scope.orderInfo = model.orderInfo;
		var orderInfo = {}; //单据备份
		$scope.cartAgent = $scope.orderInfo.cartAgent;

		$scope.query = model.query;
		$scope.userInput = '';
		$scope.userUid = '';

		$scope.catList = $scope.query.getCatList();
		$scope.accountList = model.accountList; //账户列表
		$scope.cardBoxIsShow = false; //弹出选择金额框

		$scope.nowEditItem = {}; //正在编辑总金额的项目

		$scope.thisItemPilePrice = 0;
		$scope.account_operate = {}; //当前已有金额的账户列表
		$scope.gotoQueryCat = false;
		$scope.userInfo = UserService.getLoginStatus();
		$scope.userList = model.userList;
		$scope.isUserListShow = false; //申领人/收入来源下拉列表
		$scope.isAdmin = $scope.userInfo.rpg == 8 ? true : false; //管理员才有新建成员按钮
		// 初始化开单显示时间
		var time = new Date();
		if($scope.orderInfo.fid) {
			//草稿单继续有fid 时间就是保存的时间
			time = new Date(model.reg_time * 1000);
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
		 * 	点击申领人
		 *	显示下拉列表
		 */
		$scope.clickUserInput = function() {
			$scope.userInput = '';
			$scope.userUid = '';
			$scope.isUserListShow = true;
		}
		/**
		 * 选择申领人
		 * @param  {[type]} item [申领人对象]
		 * @return {[type]}      [description]
		 */
		$scope.clickUserItem = function(item) {
			$scope.userInput = item.name;
			$scope.userUid = item.uid;
			$scope.isUserListShow = false;
		}
		/**
		 * 设置选择的申领人
		 * @param  {[type]} name [申领人名]
		 * @param  {[type]} uid  [申领人uid]
		 * @return {[type]}      [description]
		 */
		$scope.changeUserName = function(name, uid) {
			for(var v of $scope.userList) {
				if(v.uid === uid) {
					$scope.clickUserItem(v);
				}
				if(v.name === name) {
					$scope.clickUserItem(v);
					break;
				}
			}
		}
		/**
		 * 新建用户
		 * @return {[type]} [description]
		 */
		$scope.clickAddAccount = function() {
			EventService.emit(EventService.ev.START_CREATE_EDIT_USER);
			// 新建成功
	        EventService.on(EventService.ev.USER_REGISTER_SUCCESS, function(event, arg) {
	        	//用户列表刷新后选中新用户
	            if (arg > 0) {
	            	for(var v of $scope.userList){
						if(v.uid == arg){
							$scope.clickUserItem(v)
							return;
						}
	            	}
	            }
	        });
		}
		if($scope.orderInfo._class == 82) {
			$scope.changeUserName($scope.userInfo.name); //支出单申领人初始选中自己
			if($scope.orderInfo.cid) {
				$scope.changeUserName(null, $scope.orderInfo.cid)
			}
		}
		$scope.$watch('userInput', function(newValue, oldValue) {
			if(newValue) {
				$scope.changeUserName(newValue)
			}
		})
		//---------------------------------------------------------购物车有关操作-----------------------------------------------------------------------------
		/**
		 * 点击类别输入框
		 * @param {Object} item
		 */
		$scope.clickCheckCat = function(item) {
			$scope.cartAgent.setEditCartItem(item);
			item.sku_id = ""; //
			item.spec_name = ""; //
			if($scope.gotoQueryCat) {
				$scope.queryCat(item);
			} else {
				item.catListShow = true;
			}
		}
		/**
		 * 设置item的类型和
		 * @param {Object} item  传入当前操作item
		 * @param {Object} sku 选择传入的sku 如果没有sku判断输入的是否在列表里有 有就输入，没有不做处理 开单时会进行判断
		 */
		$scope.setItemCat = function(item, sku) {

			if(sku) {
				item.sku_id = sku.sku_id; //
				item.spec_name = sku.spec_name; //
				item.catListShow = false;
			} else {
				for(var v of $scope.catList) {
					if(v.spec_name == item.spec_name) {
						item.sku_id = v.sku_id; //
						item.spec_name = v.spec_name; //
					}
				}
			}

		}
		/**
		 *
		 * @param {Object} spec_name 需要创建的类别名
		 */
		$scope.createCat = function(item) {
			//sku_id存在 新建类别不传参数
			if(item.sku_id) {
				EventService.emit(EventService.ev.START_CREATE_CAT); //新建类别
			} else {
				EventService.emit(EventService.ev.START_CREATE_CAT, item.spec_name); //新建类别 带 类别名 默认填写类别名
				item.spec_name = '';
			}
		}
		$scope.addCartItem = function() {
			$scope.cartAgent.addCartItem();

		}
		$scope.deleteCartItem = function(index) {
			$scope.cartAgent.deleteCartItem(index);
			$scope.updataIncome();
		}

		//---------------------------------------------------------cardBox有关操作-----------------------------------------------------------------------------
		/**
		 * 显示账户信息列表框
		 * @param {Object} item 激活列表框的项
		 */
		$scope.showCardBox = function(item) {
			$scope.nowEditItem = item;
			$scope.cardBoxIsShow = true;
			$scope.thisItemPilePrice = item.total_price;
			for(var k in item.account_operate) {
				for(var k2 in $scope.accountList) {
					if(k == $scope.accountList[k2].account_id) {
						$scope.accountList[k2].cost = item.account_operate[k].cost;
						break;
					}
				}
			}
			$scope.updataPrice(); //更新金额
		}
		/**
		 * 新增账户
		 */
		$scope.addAccount = function (){
			EventService.emit(EventService.ev.START_CREATE_ACCOUNT,{type:2});
			$scope.addItemPilePrice();//关闭金额框
			EventService.on(EventService.ev.UPDATE_ACCOUNTLIST,function(event,arg){
				//刷新账户列表后打开金额框
				model.queryAccountList().then(function (){
					$scope.showCardBox($scope.nowEditItem);
				})
			})
		}
		/**
		 * 更新单个项目总金额
		 */
		$scope.updataPrice = function() {
			$scope.thisItemPilePrice = 0;
			$scope.account_operate = {};
			for(var v of $scope.accountList) {
				if(!v.cost) {
					v.cost = 0
				}
				if(v.cost < 0) {
					v.cost = 0 - v.cost;
				}
				if(v.cost > 0) {
					$scope.thisItemPilePrice += v.cost;
					$scope.account_operate[v.account_id] = v;
				}
			}
		}
		/**
		 * 更新购物车总价值
		 */
		$scope.updataIncome = function() {
			$scope.orderInfo.income = 0;
			for(var v of $scope.cartAgent.cartItemList) {
				$scope.orderInfo.income += v.total_price;
			}
		}
		/**
		 * cardBox确认
		 */
		$scope.addItemPilePrice = function() {
			$scope.nowEditItem.total_price = 0;
			$scope.nowEditItem.total_price += $scope.thisItemPilePrice;
			$scope.thisItemPilePrice = 0;
			$scope.nowEditItem.account_operate = {};
			angular.copy($scope.account_operate, $scope.nowEditItem.account_operate);
			$scope.updataIncome();
			$scope.closeCardBox();
		}
		/**
		 * 清除cardBox 内金额数据
		 */
		$scope.clearCardBox = function() {
			for(var v of $scope.accountList) {
				v.cost = 0;
			}
		}
		/**+
		 * 关闭cardBox
		 */
		$scope.closeCardBox = function() {
			$scope.thisItemPilePrice = 0;
			$scope.cardBoxIsShow = false;
			$scope.clearCardBox();
		}

		//---------------------------------------------------------开单       有关操作-----------------------------------------------------------------------------
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
			//默认几口为新建单据
			var apiName = 'createFinanceOrder';
			var dataToSend = {
				class: $scope.orderInfo._class,
				reg_time: $scope.orderInfo.reg_time,
				remark: $scope.orderInfo.remark,
				income: $scope.orderInfo.income,
				cart: {
					data: [],
				}
			};
			if($scope.orderInfo._class == 81) {
				if(!$scope.orderInfo.cid_name) {
					//收入应该有收入来源
					PageService.showConfirmDialog('收入来源不能为空！', ['确认'])
					return;
				}
				dataToSend.cid_name = $scope.orderInfo.cid_name;
			}
			if($scope.orderInfo._class == 82) {
				if(!$scope.userUid) {
					//支出应该有申领人
					PageService.showConfirmDialog('申领人填写错误或不存在！', ['确认']);
					return;
				}
				dataToSend.cid = $scope.userUid;
				dataToSend.cid_name = $scope.userInput;
			}
			var i = 1;
			for(var v of $scope.orderInfo.cartAgent.cartItemList) {
				if(!isDraft) {
					if(!v.sku_id) {
						if(!v.spec_name) {
							PageService.showConfirmDialog('第' + i + '行分类不能为空！请检查', ['确认']);
						} else {
							PageService.showConfirmDialog('第' + i + '行分类不存在,是否创建新类别!', ['去创建', '取消'], function() {
								$scope.createCat(v);
							});
						}
						return
					}
					if(!v.item_name) {
						if($scope.orderInfo._class == 82) {
							PageService.showConfirmDialog('第' + i + '行支出项目不能为空！请检查', ['确认'])
						} else {
							PageService.showConfirmDialog('第' + i + '行收入项目不能为空！请检查', ['确认'])
						}
						return
					}
					if(v.total_price <= 0) {
						PageService.showConfirmDialog('第' + i + '行金额必须大于0！请检查', ['确认'])
						return
					}
					if($scope.orderInfo._class == 82) {
						delete v.account_operate;
					}
				}
				dataToSend.cart.data.push(v);
				i++;
			}
			dataToSend.cart = JSON.stringify(dataToSend.cart); //购物车内数据转换成字符串形式
			if($scope.orderInfo.fid) {
				//如果有fid 则传fid
				//fid 代表草稿继续开单的
				//开单成功后会删除草稿单
				dataToSend.fid = $scope.orderInfo.fid;
			}
			//如果isDraft==true 创建的则是草稿单
			if(isDraft) {
				//创建草稿单接口
				apiName = 'createFinanceOrderDraft';
			}
			NetworkService.request(apiName, dataToSend, function(data) {
				if(data.EC > 0) {
					PageService.showSharedToast('提交成功');
					PageService.closeDialog(model.Dialog);
					ViewOrderModel.query.request();
					if($scope.orderInfo.fid || isDraft) {
						EventService.emit(EventService.ev.START_VIEW_YIQIJI_DRAFT, 1); //打开草稿单据
					} else {
						EventService.emit(EventService.ev.START_VIEW_ALL_ORDER); //打开查看所有单据
					}
					$log.debug('create', $scope.orderInfo._class, 'successs', data)
				}
			})

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

		/**
		 * 全局监听点击事件是否隐藏下拉列表  按钮出实效(待研究)
		 */
		document.addEventListener('click', function(e) {
			if(!$(e.target).hasClass('dropdown-list') && 　!$(e.target).hasClass('dropdown-input')) {
				$scope.cartAgent.hideList();
			}
		})

		/**
		 * 当前页面 跳入创建类别页面成功后接收事件
		 *	事件内部 有关promise.then 会失效
		 * 解决方式 改变 gotoQueryCat状态 在显示之前判断是否重新获取列表
		 */
		EventService.on(EventService.ev.UPDATA_CATLIST, function() {
			$scope.gotoQueryCat = true;
		});

		/**
		 *	重新查询类别列表 在新建类别后用到
		 * @param {Object} item 传入当前操作的item
		 * 查询成功后显示再列表
		 * 关闭 gotoquadraticAt 状态
		 */
		$scope.queryCat = function(item) {
			$scope.query.request().then(function(data) {
				item.catListShow = true;
				$scope.gotoQueryCat = false;
			});
		}

	}
]);