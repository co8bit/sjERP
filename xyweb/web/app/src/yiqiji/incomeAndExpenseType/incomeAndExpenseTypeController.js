'use strict'

xy.controller('incomeAndExpenseTypeController',['$scope','incomeAndExpenseModel','$log', 'EventService', 'PageService', 'NetworkService',
	function ($scope, model, $log, EventService, PageService, NetworkService) {
		$scope.query = model.query;
		$scope.catList = $scope.query.getCatList();
		$scope.createCat = function (){
			EventService.emit(EventService.ev.START_CREATE_CAT);//新建类别
		}

		$scope.editCat = function (item){
			EventService.emit(EventService.ev.START_EDIT_CAT,item);//编辑类别
		}
		/**
		 *
		 * @param {number} sku_id 要删除的sku_id
		 */
		$scope.deleteCat = function (sku_id){
			$log.error(sku_id)
			PageService.showConfirmDialog('确认删除此类别？',['删除','取消'],function (){
				var dataToSend = {
					sku_id: sku_id,
				}
				NetworkService.request('deleteFinanceCart',dataToSend,function (data){
					$log.debug('deleteSuccess',data);
					PageService.showSharedToast('删除成功！')
					$scope.query.request();
				})
			},function (){

			})
		}
	}
])