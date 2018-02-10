//xxxxController
'use strict';

angular.module('XY').controller('FeedBackController', ['EventService', '$scope', '$log','PageService','UserService',
    function(EventService, $scope, $log,PageService,UserService) {
        $scope.PageService = PageService;
        $scope.feedbackContent = "";

        // $log.log('FeedBackController');

        $scope.send = function() {
        	UserService.feedback.create($scope.feedbackContent, function () {
                $scope.feedbackContent = "";
            });
        }

        var handle1 = EventService.on(EventService.ev.feedback_create_,function(event,arg){
        	if (arg > 0) {
        		$scope.feedbackContent = "";
        	}
        });

        $scope.$on('$destroy',function(){
        	handle1();
        });
    }
]);

