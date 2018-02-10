'use strict';

xy.controller('ProfileController', ['$scope', '$log', 'PageService', 'UserService',
    function($scope, $log, PageService, UserService) {


         // $scope.logout = function (){
         //    UserService.logout(function(){
         //        PageService.setCurrentPage('selectLogInMode');
         //    });
         // }


        // 把页面跳转服务映射到$scope里
        // $scope.PageService = PageService;

        // UserService.getLoginStatus(function(loginStatus) {
        //     $log.log('getLoginStatusCallback');
        //     $log.log(loginStatus);
        //     if (loginStatus.status == 1) {
        //         PageService.setCurrentPage('index');
        //     }else {
        //     	PageService.setCurrentPage('selectLogInMode');
        //     }
        // })

        
        
        // var pageNameToNum = {
        // 	index : 1,
        // 	newSalesOrder : 2,
        // 	submitSalesOrder: 3,
        // 	viewStock : 4,
        // 	selectLogMode : -1,
        // 	manageLogin : -2,
        // 	normalLogin : -3,
        // }

    }
]);
