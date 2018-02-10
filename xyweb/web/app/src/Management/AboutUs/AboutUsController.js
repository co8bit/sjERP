//xxxxController
'use strict';

angular.module('XY').controller('AboutUsController', ['EventService', '$scope', '$log','PageService', 'AuthGroupService',
    function(EventService, $scope, $log,PageService, AuthGroupService) {
        $scope.PageService = PageService;
		$scope.IsYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);//易企记
    }
]);
