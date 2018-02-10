//xxxxController
'use strict';

angular.module('XY').controller('CreateEditUserController', ['AuthGroupService', 'EventService', '$scope', '$log', 'PageService', 'ManageUserModel', 'UserService',
	function (AuthGroupService, EventService, $scope, $log, PageService, model, UserService) {

		$scope.IsYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);//是否易企记
		$scope.userInfo = model.userObj;
		var userInfo = angular.copy($scope.userInfo, userInfo);
		$scope.rpg = UserService.getLoginStatus().rpg;
		$scope.departmentList = model.departmentList;//部门列表
		$scope.isEdit = model.isEdit; //是否编辑模式
		$scope.isAdmin = false;
		$scope.selectRoleIsShow = true;//是否显示成员选择
		if (model.userObj.rpg == 1 || model.userObj.rpg == 8) {
			$scope.isAdmin = true;
			if ($scope.isEdit) {
				//编辑模式下管理员不能更改角色
				$scope.selectRoleIsShow = false;
			}
		}
		if($scope.IsYqjShow){
			$scope.department = $scope.departmentList[0];
			if($scope.userInfo.depart_id){
				for (var v of $scope.departmentList) {
					if(v.depart_id == $scope.userInfo.depart_id){
						$scope.department = v;
					}
				}
			}
		}

		var dialogUserInfo = function (type) {
			PageService.showConfirmDialog('<div class="title">'+ type + '成功！</div>' +
				'<div class="title">员工登录会用到以下信息：</div>' +
				'<div><a class="left">公司用户名：</a><a class="right">' + UserService.getLoginStatus().shop_name + '</a></div>' +
				'<div><a class="left">员工用户名：</a><a class="right">' + $scope.userInfo.username + '</a></div>' +
				'<div><a class="left">密&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;码：</a><a class="right">' + $scope.userInfo.password + '</a></div>',
				null
			)
		}
		$scope.createUser = function () {
			$scope.validate(function () {
				$scope.userInfo.status = Number($scope.userInfo.status);
				if($scope.department){
					$scope.userInfo.depart_id = $scope.department.depart_id;
				}
				UserService.user.register($scope.userInfo).then(function () {
					dialogUserInfo('新建');
				});
			});
		}

		$scope.saveChange = function () {
			$scope.validate(function () {
				$scope.userInfo.status = Number($scope.userInfo.status);
				if($scope.department){
					$scope.userInfo.depart_id = $scope.department.depart_id;
				}
				UserService.user.editUserInfo($scope.userInfo, 2, function(){
					dialogUserInfo('编辑');
				});
			});
		}

		$scope.quit = function () {
			if (angular.equals(userInfo, $scope.userInfo)) {
				PageService.closeDialog();
			} else {
				PageService.showConfirmDialog('确定放弃编辑吗？', [], function () {
					PageService.closeDialog();
				});
			}
		}

		$scope.validate = function (callback) {
			if($scope.userInfo.username == ''){
				PageService.showConfirmDialog('用户名不能为空！');
				return
			}
			if ($scope.userInfo.password == '' || $scope.userInfo.password.length < 6 || $scope.userInfo.password.length > 32) {
				PageService.showConfirmDialog('密码长度应在6-32个字符之间！');
				return
			}
			if($scope.userInfo.mobile == ''){
				PageService.showConfirmDialog('手机号码不能为空！');
				if($scope.userInfo.mobile.length != 11){
					PageService.showConfirmDialog('手机号码不合法');
				}
				return
			}
			if($scope.userInfo.name == ''){
				PageService.showConfirmDialog('真实姓名不能为空！');
				return
			}
			if($scope.userInfo.rpg == ''){
				PageService.showConfirmDialog('请选择成员角色！');
				return
			}
			callback();
		}

		var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
			if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
				$scope.$apply(function () {
					$scope.quit();
				})
			}
		})

		$scope.$on('$destroy', function () {
			keydownHandle();
		})
		$scope.onlyNumber = function(item, value) {
			item[value] = parseInt(item[value]);
            if( item[value] < 0)
                item[value] *= -1;
        }
	}
]);