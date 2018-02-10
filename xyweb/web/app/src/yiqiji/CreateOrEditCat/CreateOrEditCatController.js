'use strict'

xy.controller('CreateOrEditCatController', ['$scope', '$log','CreateOrEditCatModel', 'EventService', 'PageService', 'NetworkService',
    function($scope, $log, model, EventService, PageService, NetworkService) {
		// 引用正在编辑的cat对象
    	$scope.catInfo = model.catInfo;//类别信息对象
    	$scope.isEdit = model.isEdit;//是否编辑模式
		var catInfo = angular.copy($scope.catInfo, catInfo);//备份
		if (model.spec_name) {
			$scope.catInfo.spec_name = model.spec_name;
		}
		/**
		 *
		 * @param {Object} isContinue 是否继续，true创建一个并继续，false 创建完成就关闭
		 */
    	$scope.createCat =  function(isContinue){
    		if(!$scope.catInfo.spec_name){
    			PageService.showSharedToast('类别名不能为空！')
    			return
    		}
    		// $scope.catInfo.status = parseInt($scope.catInfo.status);
    		// $scope.catInfo.sku_index = parseInt($scope.catInfo.sku_index);
    		// $scope.catInfo.sku_id = parseInt($scope.catInfo.sku_id);
    		var dataToSend = {};
    		angular.copy($scope.catInfo, dataToSend);
			NetworkService.request('createFinanceCart',dataToSend,function (data){
				$log.debug('CreatSuccess',data);
				angular.copy(catInfo, $scope.catInfo);
				PageService.showSharedToast('创建成功！');
				EventService.emit(EventService.ev.UPDATA_CATLIST);//创建成功刷新类别管理列表
				if (!isContinue) {
				//false 创建并关闭
					PageService.closeDialog(model.dialogId);
				}
			})
    	}

    	$scope.editCat = function() {
    		if(!$scope.catInfo.spec_name){
    			PageService.showSharedToast('类别名不能为空！')
    			return
    		}
    		// $scope.catInfo.status = parseInt($scope.catInfo.status);
    		// $scope.catInfo.sku_index = parseInt($scope.catInfo.sku_index);
    		// $scope.catInfo.sku_id = parseInt($scope.catInfo.sku_id);
			var dataToSend = {};
    		angular.copy($scope.catInfo, dataToSend);
			NetworkService.request('editFinanceCart',dataToSend,function (data){
				$log.debug('editSuccess',data);
				angular.copy(catInfo, $scope.catInfo);
				PageService.showSharedToast('保存成功！')
				EventService.emit(EventService.ev.UPDATA_CATLIST);
				PageService.closeDialog(model.dialogId);
			})
		}

		$scope.deleteCat = function (){
			PageService.showConfirmDialog('确认删除么',['删除','取消'],function (){
				$log.error($scope.catInfo.sku_id)
				var dataToSend = {
					sku_id: $scope.catInfo.sku_id,
				}
				NetworkService.request('deleteFinanceCart',dataToSend,function (data){
					$log.debug('editSuccess',data);
					angular.copy(catInfo, $scope.catInfo);
					EventService.emit(EventService.ev.UPDATA_CATLIST);
					PageService.closeDialog(model.dialogId);
				})
			},function (){

			})
		}

		$scope.quit = function() {
			if (angular.equals(catInfo, $scope.catInfo)) {
				PageService.closeDialog();
			} else {
				PageService.showConfirmDialog('确定放弃编辑吗？',[],function () {
					PageService.closeDialog();
				});
			}
		}

		var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
			if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
				$scope.$apply(function () {
					$scope.quit();
				})
			}
			if (domEvent.keyCode == 13) {
				if(!PageService.isConfirmDialogShow){
					$scope.createCat();
				}
			}
		})

		$scope.$on('$destroy', function () {
			keydownHandle();
		})
    }
])