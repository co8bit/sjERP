'use strict';

xy.controller('ExperienceRegisterController', ['$scope','EventService','$log','$cookies','NetworkService','UserService','PageService',
    function ($scope,EventService,$log,$cookies,NetworkService,UserService,PageService) {
        $scope.mobile = '';
        $scope.registerVisitorMobile = function () {
            if ($scope.selected == 1) { $scope.where_know = '网上搜索' }
            if ($scope.selected == 2) { $scope.where_know = '熟人推荐' }
            if ($scope.selected == 3) { $scope.where_know = '手机应用商店' }
            if ($scope.selected == 4) { $scope.where_know = $scope.other }
            var dataToSend = {
                mobile: $scope.mobile,
            }
            if($scope.where_know){
                dataToSend.where_know = $scope.where_know;
            }
            NetworkService.request('ExperienceRegister', dataToSend, function (data) {
                UserService.login('NA', '12345678901', '123456', 2, 1, '', '', '', function () {
                    Cookies.set('isExperience',true);
                    UserService.noviceBootIsShow = true;
                    UserService.clearExperience();
                    PageService.closeDialog();
                }, function () {});
            })
        }
    }
]);