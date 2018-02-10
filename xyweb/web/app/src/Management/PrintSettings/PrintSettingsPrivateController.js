'user strict'

angular.module('XY').controller('PrintSettingsPrivateController', ['EventService', '$scope', '$log', 'PageService', 'NetworkService', '$http', 'PrintSettingsModel',
	function(EventService, $scope, $log, PageService, NetworkService, $http, model) {
		var optionArray = [];

		$scope.getUID_template = function(){
			if($scope.adminUID == undefined){
				alert('uid为空');
				return;
			}

			var dataToSend = {
				'class': 1 ,
				admin_uid: $scope.adminUID
			};
			NetworkService.request('getTemplatePrivate',dataToSend,function(data){
				$log.debug('data',data);
				if(data.EC > 0) {
					$scope.print_template = {};
					angular.copy(data.data,$scope.print_template);//获取打印模板
				}
			});
		}

		$scope.isCheckAll = false; //初始默认不全选

		$scope.gridLine = false;
		$scope.$watch('print_template',function(){

			if ($scope.print_template == undefined) return;

			if ($scope.print_template.optionArray[201] != undefined) //判断是否有网格
				$scope.gridLine = $scope.print_template.optionArray[201] == 1 ? true : false;
			else
				$scope.gridLine = false;//服务器端没有数值 初始默认没有网格

			$scope.font_size_checked = $scope.print_template.font_size; //获取字体大小

			$scope.font_size = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]; //字体列表

			$scope.data = [{
					id: 0,
					name: '名称',
					isChecked: $scope.print_template.optionArray[0] == 1 ? true : false,
				},
				{
					id: 1,
					name: '规格',
					isChecked: $scope.print_template.optionArray[1] == 1 ? true : false,
				},
				{
					id: 2,
					name: '单价',
					isChecked: $scope.print_template.optionArray[2] == 1 ? true : false,
				},
				{
					id: 3,
					name: '数量',
					isChecked: $scope.print_template.optionArray[3] == 1 ? true : false,
				},
				{
					id: 4,
					name: '金额',
					isChecked: $scope.print_template.optionArray[4] == 1 ? true : false,
				}
			];
		})

		// 全选
		$scope.checkAll = function() {
			$scope.isCheckAll ? false : true;
			for(var i in $scope.data) {
				if($scope.isCheckAll) {
					$scope.data[i].isChecked = true;
				} else {
					$scope.data[i].isChecked = false;
				}
			}
		}
		//单选 如果全选状态点击后单选按钮取消全选
		$scope.checkbox = function(isChecked) {
			isChecked ? $scope.isChecked = false : $scope.isChecked = true;
			if($scope.isCheckAll && $scope.isChecked)
				$scope.isCheckAll = false;
		}
		$scope.template_desing = function() {

			//			编辑模板代码内变量;
            //修改一下文件请对应修改printService.js文件下的对应值
			var lodop_mShopName = '__lodop_mShopName__';
			var lodop_reg_date = '__lodop_reg_date__';
			var lodop_cid_name = '__lodop_cid_name__';
			var lodop_contact_name = '__lodop_contact_name__';
			var lodop_car_license = '__lodop_car_license__';
			var lodop_park_address = '__lodop_park_address__';
			var lodop_value = '__lodop_value__';
			var lodop_off = '__lodop_off__';
			var lodop_yingshou = '__lodop_yingshou__';
			var lodop_income = '__lodop_income__';
			var lodop_remain = '__lodop_remain__';
			var lodop_sn = '__lodop_sn__';
			var lodop_cash = '__lodop_cash__';
			var lodop_bank = '__lodop_bank__';
			var lodop_onlinepay = '__lodop_onlinepay__';
			var lodop_operator_name = '__lodop_operator_name__';
			var lodop_data_table = '__lodop_data_table__';
			var lodop_num = '__lodop_num__';
			var lodop_remark = '__lodop_remark__';

			eval($scope.print_template.content); //导入模板代码

		}

		//打印设计
		$scope.desing = function() {
			if ($scope.print_template == undefined) {
				alert('请获取模板后再试');
				return;
			}
			LODOP = getLodop();
			if(!LODOP){
                $log.debug('initial lodop failed.');
                PageService.showConfirmDialog('您没有安装打印服务或者打印服务版本过低，请点击【下载】，下载最新打印安装包。如果您已经安装最新打印服务请自行运行。',['下载','取消'],function () {
                    //打开某个网站
                    window.open("http://www.lodop.net/download.html?xyvs=2.0.1");
                    // window.location.href = 'http://www.lodop.net/download.html'
                    },function () {
                });
            }else{
				$scope.template_desing();
				LODOP.PRINT_DESIGN();
				LODOP.On_Return = function(TaskID, value) {
					if(value) {
						// 使用正则表达式替换掉value中所有的   "__   __"
						value = value.replace(/\"__/g,'');
						value = value.replace(/__\"/g,'');
						$scope.printTemplateContent = value;
						document.getElementById("printTemplateContent").value = value;

					} else {
						$scope.printTemplateContent = '';
					}
				}
            }
		};

		//提交模板
		$scope.submit_template = function() {
			function replacer(key, value) {
			 	if (typeof value === 'undefined') {
			    	return 0;
			  	}
			 	return value;
			}

			optionArray [201] = $scope.gridLine == true ? 1 : 0;

			if($scope.printTemplateContent == '' || $scope.printTemplateContent == undefined) {
				$scope.printTemplateContent = $scope.print_template.content;
			}

			for(var k in $scope.data) {
				optionArray[k] = $scope.data[k].isChecked == true ? 1 : 0;
			}

			if($scope.adminUID == undefined){
				alert('uid为空');
				return;
			}
			var dataToSend = {
				class: '1',
				content: $scope.printTemplateContent,
				optionArray: JSON.stringify(optionArray,replacer),
				font_size: $scope.font_size_checked.toString(),
				admin_uid : $scope.adminUID,

			};

			NetworkService.request('createTemplatePrivate', dataToSend, function(data) {
				if(data.EC > 0) {
					PageService.showSharedToast('上传成功');
					//上传成功后重新获取最新的模板
					model.requestPrintTemplate();
				}
			});
		}


		/**
		 * 默认模板代码
		 */
		var give_template = 'LODOP.PRINT_INITA(0, 3, 911, 351, "星云进销存线下打印_标准");\
							LODOP.SET_PRINT_PAGESIZE(1, 2410, 930, "");\
							LODOP.SET_PRINT_MODE("PROGRAM_CONTENT_BYVAR", true);\
							LODOP.ADD_PRINT_TEXT(9, 224, 320, 40, lodop_mShopName);\
							LODOP.SET_PRINT_STYLEA(0, "FontName", "微软雅黑");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 14);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.SET_PRINT_STYLEA(0, "Vorient", 1);\
							LODOP.ADD_PRINT_TEXTA("DistributorMobile", 42, 369, 232, 30, lodop_contact_name);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TABLE(114, 55, 826, 187, lodop_data_table);\
							LODOP.SET_PRINT_STYLEA(0, "ItemName", "productinfo");\
							LODOP.ADD_PRINT_TEXTA("PAmount", 92, 130, 105, 30, lodop_value);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXTA("PrintTime", 25, 543, 143, 20, lodop_reg_date);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(42, 53, 79, 30, "客户：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXTA("Remark", 66, 127, 268, 30, lodop_park_address);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(92, 52, 90, 30, "货物价值：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXTA("DistributorName", 42, 99, 214, 30, lodop_cid_name);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(66, 52, 97, 30, "送货地址：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(42, 313, 65, 30, "联系人:");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(41, 643, 172, 30, lodop_car_license);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(41, 574, 107, 30, "车牌号：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(92, 235, 79, 30, "优惠：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(92, 286, 101, 30, lodop_off);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(92, 387, 147, 30, lodop_yingshou);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(25, 687, 47, 20, "第");\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 2);\
							LODOP.ADD_PRINT_TEXT(25, 727, 65, 20, "页，共");\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 3);\
							LODOP.ADD_PRINT_TEXT(25, 792, 27, 20, "页");\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(25, 54, 49, 20, "单号：");\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(25, 95, 139, 20, lodop_sn);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(92, 533, 166, 30, lodop_income);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(92, 691, 97, 30, "本次结余：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(92, 766, 110, 30, lodop_remain);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(311, 52, 76, 28, "经办人：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(311, 125, 179, 28, lodop_operator_name);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.ADD_PRINT_TEXT(311, 261, 66, 28, "备注：");\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.SET_PRINT_STYLEA(0, "Vorient", 1);\
							LODOP.ADD_PRINT_TEXT(311, 324, 508, 28, lodop_remark);\
							LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);\
							LODOP.SET_PRINT_STYLEA(0, "ItemType", 1);\
							LODOP.SET_PRINT_STYLEA(0, "Vorient", 1);'


						give_template = give_template.replace(/\s/g,'');
		/**
		 * 恢复默认模板
		 */
		$scope.recover = function(){
			$scope.printTemplateContent = give_template;
		}
	}
])