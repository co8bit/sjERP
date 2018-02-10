//xxxxController
'use strict';

xy.controller('UserInfoController', ['EventService', '$scope', '$log','PageService','UserService','LockService', '$timeout', '$q', 'NetworkService', 'AuthGroupService',
    "$timeout",function(EventService, $scope, $log, PageService, UserService, LockService, $timeout, $q, NetworkService, AuthGroupService) {
        $scope.userInfo = {};
        $scope.IsYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);
        $scope.getDepartment = function(){
            var defered = $q.defer();
            $scope.departmentList = [];
            NetworkService.request('getDepartment',{},function (data){
                angular.copy(data.data,$scope.departmentList)
                defered.resolve();
            })
            return defered.promise;
        }
        $q.all([UserService.user.getList(1),$scope.getDepartment()]).then(function(){
            $scope.userInfo = UserService.findUserById(UserService.getLoginStatus().id);
            $scope._userInfo = angular.copy($scope.userInfo, $scope._userInfo);
            $scope._userInfo.rpg === 1 || $scope._userInfo.rpg === 8 ? $scope.isAdmin = "true" : $scope.isAdmin = "false";

            $scope.department = $scope.departmentList[0];
            if($scope.userInfo.depart_id){
                for (var v of $scope.departmentList) {
                    if(v.depart_id == $scope.userInfo.depart_id){
                        $scope.department = v;
                    }
                }
            }
        });

        $scope.saveChange = function() {
            LockService.getLockShopStatus(0, function () {
                $scope.validate(function () {
                    UserService.user.editUserInfo($scope._userInfo, 1);
                    PageService.showSharedToast('编辑成功！');
                });
            });
        }


        $scope.validate = function (callback) {
			if ($scope._userInfo.password == '' || ($scope._userInfo.password.length >= 6 && $scope._userInfo.password.length <= 32)) {
				callback();
			} else {
				PageService.showConfirmDialog('密码长度应在6-32个字符之间！');
			}
		}

    }
]);
