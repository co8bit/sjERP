/**
 * Created by 木马 on 2017/7/17.
 */
angular.module('XY').factory('FaceRecognitionModel',['EventService','$log','$q','NetworkService','PageService',
    function (EventService,$log,$q,NetworkService,PageService) {
       var model = {};
       function getFace() {
           var defer = $q.defer();
           NetworkService.request('',function () {
               PageService.setNgViewPage('FaceRecognition')
           })
       }
       EventService.on(EventService.ev.OPEN_FACE_RECOGNITION,function () {
           PageService.setNgViewPage('FaceRecognition')
       });
       return model;
}]);