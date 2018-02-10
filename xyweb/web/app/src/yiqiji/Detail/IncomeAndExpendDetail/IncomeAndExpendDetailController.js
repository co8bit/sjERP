'use strict'

xy.controller('IncomeAndExpendDetailController', [ '$scope', '$log', 'PageService', 'IncomeAndExpendDetailModel', 'NetworkService', 'DraftModel', 'MiscService', 'UserService', 'EventService',
	function($scope, $log, PageService, model, NetworkService, DraftModel, MiscService, UserService, EventService) {
		$scope.isView = model.isView;//判断当前模式
		$scope.accountList = model.accountList;
		/**
		 * 页面初始化
		 */
		$scope.userInfo = UserService.getLoginStatus();
		$scope.rpg = $scope.userInfo.rpg;
		if ($scope.isView) {
			//查看模式
			//查看模式下确保不是审核状态
			$scope.isFinance = false;
			$scope.isBoss = false;
			$scope.orderInfo = model.orderInfo;
			$scope.finance_advice = $scope.orderInfo.finance_advice;//财务意见
			$scope.boss_advice = $scope.orderInfo.boss_advice;//boss意见
			$scope.expenseList = [];//支出信息列表
			if ($scope.orderInfo.cart.account_operate){
				$scope.expenseList = $scope.orderInfo.cart.account_operate; //支出单账户信息获取
			}else{
				for (var v of $scope.orderInfo.cart.data) {
					for(var v2 in v.account_operate){
						$scope.expenseList.push(v.account_operate[v2])
					}
				}
			}
		}else{
			//审核模式
			$scope.data = model.data,
			$scope.dataLen = $scope.data.length;
			$scope.dataIndex = model.dataIndex;
			$scope.orderInfo = $scope.data[$scope.dataIndex];//初始化查看的查看的订单
			$scope.isFinance = model.isFinance; //财务审核
			$scope.isBoss = model.isBoss;//boss审核
			$scope.cardBoxIsShow = false;//金额填写框
			$scope.expenseList = [];//支出信息列表
			if ($scope.isBoss) {
				$scope.expenseList = $scope.orderInfo.cart.account_operate;
			}
			$scope.income = 0;//支出确认金额
		}
		var orderInfo = {};
		angular.copy($scope.orderInfo, orderInfo)
		//员工在没有审核时可以编辑备注
		$scope.remarkEdit = $scope.isView && ($scope.orderInfo.status == 81) && ($scope.orderInfo.operator_uid == $scope.userInfo.id); //单据备注 在未审核状态下 修改自己创建的备注
		$scope.finaceRemark = false;//$scope.isView && ($scope.orderInfo.status == 82) && $scope.rpg == 10 && ($scope.orderInfo.operator_uid == $scope.userInfo.id);//财务审核意见 审核已通过下 修改自己审核通过的意见
		$scope.bossRemark = false;//$scope.isView && ($scope.orderInfo.status == 84) && ($scope.rpg == 8 || $scope.rpg == 9) && ($scope.orderInfo.operator_uid == $scope.userInfo.id);//boss备注 true 可修改
		/**
		 *	点击上一条下一条初始化显示单据
		 */
		function init(type){
			if (type) {
				//没有重新获取数据的改变单据
				$scope.orderInfo = $scope.data[$scope.dataIndex];//初始化查看的查看的订单
				if ($scope.isBoss) {
					$scope.expenseList = $scope.orderInfo.cart.account_operate;
				}
			}else{
				$scope.dataLen = $scope.data.length;
				$scope.dataIndex = 0;
				$scope.orderInfo = $scope.data[$scope.dataIndex];//初始化查看的查看的订单
				if ($scope.isBoss) {
					$scope.expenseList = $scope.orderInfo.cart.account_operate;
				}
			}
			$scope.income = 0;
			for (var v of $scope.accountList) {
				v.cost = 0;
			}
		}
		/**
		 * 作废
		 * @return {[type]} [description]
		 */
		 $scope.isDelete = false;
		 if ($scope.rpg == 11 && $scope.orderInfo.status == 81) {
		 	//员工删除
		 	$scope.isDelete = true;
		 }
		 if ($scope.rpg == 10 && $scope.orderInfo.status == 81) {
		 	//财务删除 状态未审核 必须是自己创的单子
		 	if ($scope.orderInfo.operator_uid == $scope.userInfo.id) {
		 		$scope.isDelete = true;
		 	}
		 }
		 if (($scope.rpg == 8 || $scope.rpg == 9) && $scope.orderInfo.status == 81){
		 	//boss审核 状态未审核 必须是自己创的单子
		 	if($scope.orderInfo.operator_uid == $scope.userInfo.id){
		 		$scope.isDelete = true;
		 	}
		 }
		$scope.clickDelete = function (){
			if($scope.isDelete){
				PageService.showConfirmDialog('确定要作废该单据么？',['确认','取消'],function (){
					var dataToSend = {
						fid: $scope.orderInfo.fid,
					};
					NetworkService.request('deleteFinanceOrder',dataToSend,function(data){
						$scope.orderInfo.status = 3;
						PageService.showSharedToast('单据已作废！')
					})
				})
			}else{
				PageService.showConfirmDialog('您没有权限作废单据！',['确认'])
			}
		}
		/**
		 * 查看操作记录
		 */
		$scope.clickHistory = function (){
			PageService.showHistoryDialog($scope.orderInfo.history);
		}
		$scope.saveRemark = function (){
			var isRemark = false;
			if($scope.remarkEdit && !angular.equals(orderInfo,$scope.orderInfo)){
				isRemark = true;
				var dataToSend = {
					fid: $scope.orderInfo.fid,
					remark: $scope.orderInfo.remark,
				}
			}
//			if($scope.finaceRemark && !angular.equals(orderInfo,$scope.orderInfo)){
//				isRemark = true;
//				var dataToSend = {
//					fid: $scope.orderInfo.fid,
//					remark: $scope.orderInfo.remark,
//				}
//			}
//			if($scope.bossRemark && !angular.equals(orderInfo,$scope.orderInfo)){
//				isRemark = true;
//				var dataToSend = {
//					fid: $scope.orderInfo.fid,
//					remark: $scope.orderInfo.remark,
//				}
//			}
			if (isRemark) {
				NetworkService.request('editFinanceOrder',dataToSend,function(data){
					PageService.showSharedToast('修改备注成功！');
				})
			}
		}
		/**
		 * 显示填写支出金额框
		 */
		$scope.ShowCardBox = function (){
        	$scope.cardBoxIsShow = true;
        }

		/**
		 * 新增账户
		 */
		$scope.addAccount = function (){
			EventService.emit(EventService.ev.START_CREATE_ACCOUNT,{type:2});
			var account = angular.copy($scope.accountList,account);
			EventService.on(EventService.ev.UPDATE_ACCOUNTLIST,function(event,arg){
				//刷新账户列表后打开金额框
				model.queryAccount().then(function (){
					for(var i in account){
						$scope.accountList[i].cost = account[i].cost;
					}
					$scope.ShowCardBox($scope.nowEditItem);
				})
			})
		}

		/**
		 * cardBox确认
		 */
		$scope.updataIncome = function (){
			$scope.income = 0;
			for (v of $scope.accountList) {
				if (v.cost == '') {
					v.cost = 0;
				}
				if (v.cost < 0 ) {
					v.cost = 0 - v.cost
				}

				if (v.cost > 0) {
					$scope.income += v.cost;
				}
			}
		}
		/**+
		 * 关闭cardBox
		 */
		$scope.closeCardBox = function (){
			$scope.expenseList = [];
			for (v of $scope.accountList) {
				if (v.cost > 0 ) {
					$scope.expenseList.push(v);
				}
			}
			$scope.cardBoxIsShow = false;
		}
		function cartDate(){
			var cart = {
				data: $scope.orderInfo.cart.data,
				account_operate: [],
			};
			for (var v of $scope.expenseList) {
				if(v.cost > 0){
					cart.account_operate.push(v)
				}
			}
			cart = JSON.stringify(cart);
			return cart
		}
		/**
		 *	通过
		 */
		$scope.passOrder = function (){
			var dataToSend = {
				'fid': $scope.orderInfo.fid,
			};

			if ($scope.isFinance) {
				if($scope.orderInfo.class == 82){
					dataToSend.cart = cartDate();
				}
				dataToSend.status = 82;
				dataToSend.finance_advice = $scope.orderInfo.finance_advice;
			}else{
				dataToSend.status = 84;
				dataToSend.boss_advice = $scope.orderInfo.boss_advice;
			}
			NetworkService.request('financeOrderStatusChange',dataToSend,function (data){
				PageService.showSharedToast('通过成功！');
				if ($scope.orderInfo.status == 81) {
					$scope.orderInfo.status = 82
				}else{
					$scope.orderInfo.status = 84
				}
				model.queryAccount();
				$scope.orderInfo = $scope.data[$scope.dataIndex];//初始化查看的查看的订单
				DraftModel.query.request();
			})
		}
		/**
		 *	驳回
		 */
		$scope.rebut = function (){
			var dataToSend = {
				'fid': $scope.orderInfo.fid,
			};
			if ($scope.isFinance) {
				dataToSend.status = 83;
				dataToSend.finance_advice = $scope.orderInfo.finance_advice;
			}else{
				dataToSend.status = 85;
				dataToSend.boss_advice = $scope.orderInfo.boss_advice;
			}
			NetworkService.request('financeOrderStatusChange',dataToSend,function (data){
				PageService.showSharedToast('驳回成功！');
				if ($scope.orderInfo.status == 81) {
					$scope.orderInfo.status = 83;
				}else{
					$scope.orderInfo.status = 85;
				}
				DraftModel.query.request();
			})
		}

		$scope.changeOrder = function (step){
			PageService.showSharedToast('数据获取中',10000);
			if (step == 1) {
				//下一条
				if ($scope.dataIndex == $scope.dataLen -1) {
					//超出data长度
					$scope.isTimeOut = false;//超时提醒
					setTimeout(function (){
						if (!$scope.isTimeOut) {
							PageService.showConfirmDialog('获取数据超时',['确认'])
						}
					},9999)

					// 异步任务计数器
					var task = MiscService.newAsyncTask(1);

					// 新建一个管理查询的对象
					DraftModel.query.setCustomCallback(function() {
						// 查询成功后计数
						task.finishOneProc();
					});

					// 设定回调
					task.setFinalProc(function() {
						// 初始查询完成后清除回调
						DraftModel.query.setCustomCallback(undefined);
						// 获取数据
						angular.copy(DraftModel.query.getData(),$scope.data)
						$scope.isTimeOut = true;//拿到数据关闭超时提醒
						PageService.isSharedToastShow = false;
						if ($scope.data.length == 0) {
							PageService.showConfirmDialog('已经是最后一条了',['确认']);
						}else{
							init();//初始化显示内容
						}
					})

					DraftModel.query.request();
				}else{
					++$scope.dataIndex
					init(1);
					PageService.isSharedToastShow = false;
				}

			}else{
				//上一条
				if ($scope.dataIndex == 0) {
					PageService.isSharedToastShow = false;
					PageService.showConfirmDialog('已经是第一条了',['确认']);
				}else{
					--$scope.dataIndex
					init(-1);
					PageService.isSharedToastShow = false;
				}
			}

		}
		$scope.quit = function () {
			PageService.closeDialog(model.Dialog);
		}
	}
])
