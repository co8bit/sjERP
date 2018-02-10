//controller
'use strict';

xy.controller('SelectUserController', ['$rootScope', '$scope', '$log', 'PageService', 'StockService','UserService',
    function($rootScope, $scope, $log, PageService, StockService,UserService) {

        $log.debug('SelectUserController born');


        $scope.cancelSelecUser = function() {
        	var callback = PageService.getCallback('finishSelectChecker')
        	callback()
        }




    }
]);