'use strict'

xy.controller('CorporateSectorController', [ '$scope', '$log', 'CorporateSectorModel', 'NetworkService', 'PageService',
	function($scope, $log, model, NetworkService, PageService) {
		$scope.departmentList = model.departmentList;
		$scope.addSectorIsShow = false;
		$scope.isEdit = false;
		$scope.depart_name = '';
		$scope.remark = '';
		$scope.depart_id = '';
		$scope.time = new Date().getTime();
		/**
		 * keydown 共用事件
		 * @param {Object} e
		 */
		function kedown(e){
			if (e.keyCode === 27) {
				$scope.close();
			}
			if (e.keyCode === 13) {
				if (!PageService.isConfirmDialogShow) {
					$scope.createDepartment();
				}
			}
		}
		/**
		 * 新建编辑显示 box
		 * @param {Object} item 有则是编辑 没有则是新建
		 */
		$scope.showBox = function (item){
			if (item) {
				$scope.depart_name = item.depart_name;
				$scope.remark = item.remark;
				$scope.isEdit = true;
				$scope.depart_id = item.depart_id;
			}else{
				$scope.depart_name = '';
				$scope.remark = '';
				$scope.isEdit = false;
			}
			$scope.addSectorIsShow = true;
			document.addEventListener('keydown',kedown);//添加keydown事件
		}

		/**
		 * 关闭box
		 */
		$scope.close = function (){
			$scope.addSectorIsShow = false;
			document.removeEventListener('keydown',kedown);//移除keydown事件
		}

		/**
		 * 创建部门
		 */
		$scope.createDepartment = function (){
			var apiName = 'createDepartment';
			var dataToSend = {
				depart_name: $scope.depart_name,
				remark: $scope.remark,
				status: 1,
			};
			if ($scope.isEdit) {
				dataToSend.depart_id = $scope.depart_id;
				apiName = 'editDepartment';
			}
			NetworkService.request(apiName,dataToSend,function (data){
				$scope.addSectorIsShow = false;
				model.getDepartment();
				var edit = '';
				if ($scope.isEdit) {
					edit = '编辑';
				}else{
					edit = '创建';
				}
				PageService.showSharedToast(edit + '成功！')
			})
		}
		/**
		 * 删除部门
		 * @param {Object} __id
		 */
		$scope.deleteDepartment = function (__id){
			PageService.showConfirmDialog('确定删除该部门？',['删除','取消'],function (){
				var dataToSend = {
					depart_id: __id,
				};
				NetworkService.request('deleteDepartment',dataToSend,function (data){
					$log.error(data);
					PageService.showSharedToast('删除部门成功！');
					model.getDepartment();
				})
			})
		}

	}
])