/**
 * Created by 木马 on 2017/7/18.
 */
xy.controller("FaceDetailController",["$log","$scope","$q","FaceDetailModel","PageService",
    function ($log,$scope,$q,model,PageService) {
    $scope.backToFace = function () {
        PageService.setNgViewPage('FaceRecognition');
    }
}]);