/**
 * Created by 木马 on 2017/7/17.
 */
xy.controller("FaceRecognitionController",['$scope','$log','$q','FaceRecognitionModel','EventService',
    function ($scope,$log,$q,Model,EventService) {
      $scope.faceDetail = function (item) {
          EventService.emit(EventService.ev.OPEN_FACE_DETAIL,item);
      }
}]);