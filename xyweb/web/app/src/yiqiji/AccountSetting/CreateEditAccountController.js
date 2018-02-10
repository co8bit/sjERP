'use strict'

xy.controller('CreateEditAccountController', ['$scope', '$log', 'CreateEditAccountModel', 'PageService', 'ConfigService', 'NetworkService', 'EventService', 'PYService',
	function($scope, $log, model, PageService, ConfigService, NetworkService, EventService, PYService) {
		$scope.model = model;
		var accountInfo_copy = {};
		var cashInfo_copy = {};
		if(model.isCash){
			$scope.cashInfo = model.cashInfo;
		}else{
			$scope.typeList = ['支付宝', '微信', '财付通(QQ)', '百付宝', '易宝', '快钱', '首信易', '好支付'];
			$scope.bankList = ["中国工商银行","招商银行","中国农业银行","中国建设银行","中国银行","中国民生银行",
								"中国光大银行","中信银行","交通银行","兴业银行","上海浦东发展银行","中国人民银行",
								"华夏银行","深圳发展银行","广东发展银行","国家开发银行","厦门国际银行","中国进出口银行",
								"中国农业发展银行","北京银行","上海银行","中国邮政储蓄银行"
							];
			$scope.isEdit = model.isEdit;
			$scope.createOrSave = $scope.isEdit ? '保存' : '新建';
			$scope.accountInfo = model.accountInfo; //订单初始化
			$scope.geoData = ConfigService.geoData; //省市数据
			$scope.cityList = $scope.geoData[0].sub; //市
			$scope.isBank = false; //账户来源类型是否网络
			$scope.isCask = false; //是否现金账户
			if ($scope.accountInfo.account_source_type == 2) {
				$scope.isBank = true;
			}
		}
		/**
		 * 生成速查码
		 */
		$scope.genQcode = function (){
			$scope.accountInfo.qcode = PYService.getPY($scope.accountInfo.account_name)[0];
		}
		/**
		 * 选择省后改变市列表
		 * @param {number} index 、、选中省在数组中的位置
		 */
		$scope.change = function(index) {
			$scope.accountInfo.province = $scope.geoData[index].name;
			$scope.cityList = $scope.geoData[index].sub;
			selectInit(0,2)
		}
		/**
		 *
		 * @param {Object} name 选中的省名字在编辑中传入
		 * @param {Object} type 1是省 2是市
		 */
		function selectInit(name,type){
			if (type == 1) {
				var html = '';
				if (name) {
					for(var k in $scope.geoData) {
						if ($scope.geoData[k].name == name) {
							html += '<option selected="selected" value="' + k + '">' + $scope.geoData[k].name + '</option>'
							$scope.change(k);//改变市下拉框
						}else{
							html += '<option value=' + k + '>' + $scope.geoData[k].name + '</option>'
						}
					}
				}else{
					for(var k in $scope.geoData) {
						if (k == 0) {
							html += '<option selected="selected" value="' + k + '">' + $scope.geoData[k].name + '</option>'
							$scope.change(k);//改变市下拉框
						}else{
							html += '<option value=' + k + '>' + $scope.geoData[k].name + '</option>'
						}
					}
				}
				$('#provice').html(html);
			}else{
				var html = '';
				if (name) {
					for(var k in $scope.cityList) {
						if ($scope.cityList[k].name == name) {
							html += '<option selected="selected" value=' + $scope.cityList[k].name + '>' + $scope.cityList[k].name + '</option>'
						}else{
							html += '<option value=' + $scope.cityList[k].name + '>' + $scope.cityList[k].name + '</option>'
						}
					}
				}else{
					for(var k in $scope.cityList) {
						if (k == 0) {
							html += '<option selected="selected" value=' + $scope.cityList[k].name + '>' + $scope.cityList[k].name + '</option>'
						}else{
							html += '<option value=' + $scope.cityList[k].name + '>' + $scope.cityList[k].name + '</option>'
						}
					}
				}

				$('#city').html(html);
			}
		}

		/**
		 * 选中账户类型 type 1是银行 2是网络
		 * @param {number} type
		 */
		$scope.checkAddType = function(type) {
			if(type == 1) {
				$scope.isBank = false;
			} else {
				$scope.isBank = true;
			}
			model.init();
		}
		/**
		 *
		 */
		$scope.del = function (){
			PageService.showConfirmDialog('确认删除么？',['确认','取消'],function (){
				var dataToSend　= {
					account_id : $scope.accountInfo.account_id,
				}
				NetworkService.request('deleteAccount', dataToSend,function (){
					PageService.closeDialog(model.dialogId);
					EventService.emit(EventService.ev.START_ACCOUNT)
				})
			},function (){
				return
			})
		}
		/**
		 * 格式化结余
		 */
		$scope.updataBalance = function (){
			if($scope.accountInfo.account_balance < 0){
				$scope.accountInfo.account_balance = 0 - $scope.accountInfo.account_balance;
			}
			if($scope.accountInfo.account_balance == ''){
				$scope.accountInfo.account_balance = 0;
			}
		}
		/**
		 * 保存按钮
		 */
		$scope.save = function() {
			var dataToSend = {}
			//账户来源类型是否网络 1是银行 2是网络
			if($scope.isBank) {
				dataToSend = {
					account_source_type: 2, //账户来源
					account_source_name: $scope.accountInfo.account_source_name_2, //账户来源名
					account_creator: $scope.accountInfo.account_creator, //开户人
					account_name: $scope.accountInfo.account_name, //账户名
					account_number: $scope.accountInfo.account_number,//账号
					account_balance: $scope.accountInfo.account_balance,//金额
					qcode: $scope.accountInfo.qcode,//速查码
					account_remark: $scope.accountInfo.account_remark,//备注
					status: $scope.accountInfo.status,//状态
				}
			} else {
				dataToSend = {
					account_source_type: 1, //账户来源
					account_source_name: $scope.accountInfo.account_source_name_1, //账户来源名
					account_creator: $scope.accountInfo.account_creator, //开户人
					account_name: $scope.accountInfo.account_name, //账户名
					account_number: $scope.accountInfo.account_number,//账号
					account_balance: $scope.accountInfo.account_balance,//金额
					qcode: $scope.accountInfo.qcode,//速查码
					province: $scope.accountInfo.province, //省
					city: $scope.accountInfo.city,//市
					bank_name: $scope.accountInfo.bank_name, //开户行名
					account_remark: $scope.accountInfo.account_remark,//备注
					status: $scope.accountInfo.status,//状态
				}
			}
			if($scope.isEdit){
				//是否编辑模式
				dataToSend.account_id = $scope.accountInfo.account_id;
				delete dataToSend.account_source_type;
			}
			if(dataToSend.account_balance == undefined){
				PageService.showConfirmDialog('期初余额不是一个有效的数字',['确认']);
				return
			}
			//判断有填写完整了没有
			for(var k in dataToSend){
				if (k != 'account_remark' && k != 'city' && k != 'status' && k != 'account_balance' && k != 'qcode' && dataToSend[k] == '') {
					PageService.showConfirmDialog('表单未填写完整，请检查！',['确认']);
					return
				}
			}
			//判断银行类型时有没有选择省市
			if (!$scope.isBank) {
				if (dataToSend.province == '请选择' || dataToSend.city == '请选择') {
					PageService.showConfirmDialog('请选择开户行所在的省市',['确认'],function (){

					})
					return
				}
			}
			if ($scope.isEdit) {
				NetworkService.request('editAccount', dataToSend, function(data) {
					PageService.showSharedToast('保存成功');
					PageService.closeDialog(model.dialogId);
					EventService.emit(EventService.ev.START_ACCOUNT);
				})
			}else{
				NetworkService.request('createAccount', dataToSend, function(data) {
					PageService.showSharedToast('新建成功');
					PageService.closeDialog(model.dialogId);
					if(model.isUpdate){
						EventService.emit(EventService.ev.UPDATE_ACCOUNTLIST);
					}else{
						EventService.emit(EventService.ev.START_ACCOUNT);
					}
				})
			}
		}
		/**
		 * 退出
		 */
		$scope.quit = function() {
			if(model.isCash){
				if(angular.equals($scope.cashInfo, cashInfo_copy)) {
					PageService.closeDialog(model.dialogId);
				} else {
					PageService.showConfirmDialog('修改未保存,确认关闭？', ['确认', '取消'], function() {
						PageService.closeDialog(model.dialogId);
					}, function() {
						return
					})
				}
			}else{
				if(angular.equals($scope.accountInfo, accountInfo_copy)) {
					PageService.closeDialog(model.dialogId);
				} else {
					PageService.showConfirmDialog('修改未保存,确认关闭？', ['确认', '取消'], function() {
						PageService.closeDialog(model.dialogId);
					}, function() {
						return
					})
				}
			}
		}
		/**
		 * 状态改变
		 */
		$scope.checkStatus = function (){
			if(model.isCash){
				$scope.cashInfo.status == 0 ? $scope.cashInfo.status = 1 : $scope.cashInfo.status = 0;
			}else{
				$scope.accountInfo.status == 0 ? $scope.accountInfo.status = 1 : $scope.accountInfo.status = 0;
			}
		}
		/**
		 * 现金账户通过
		 */
		$scope.saveCash = function (){
			if (!$scope.cashInfo.account_name) {
				PageService.showConfirmDialog('账户简称不能为空',['确认'])
				return
			}
			if (!$scope.cashInfo.account_source_name) {
				PageService.showConfirmDialog('账户不能为空',['确认'])
				return
			}
			if (!$scope.cashInfo.account_balance) {
				$scope.cashInfo.account_balance = 0;
			}
			delete $scope.cashInfo.province;
			delete $scope.cashInfo.city;
			NetworkService.request('editAccount', $scope.cashInfo, function(data) {
				PageService.showSharedToast('保存成功');
				PageService.closeDialog(model.dialogId);
				if(model.isUpdate){
					EventService.emit(EventService.ev.UPDATE_ACCOUNTLIST);
				}else{
					EventService.emit(EventService.ev.START_ACCOUNT);
				}
			})
		}
		$(function() {
			if(model.isCash){
				angular.copy($scope.cashInfo, cashInfo_copy);
			}else{
				//编辑模式初始化省市为原省市
				if ($scope.isEdit) {
					selectInit($scope.accountInfo.province,1)//初始化省下拉框
					selectInit($scope.accountInfo.city,2)//初始化省下拉框
				}else{
					selectInit(0,1)//初始化省下拉框
				}
				angular.copy($scope.accountInfo, accountInfo_copy);
			}
		})
	}
])