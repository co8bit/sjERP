/**
 * Created by 木马 on 2017/7/18.
 */
angular.module("XY").factory("CameraModel",["$log","$q","EventService","NetworkService","PageService",
    function ($log,$q,EventService,NetworkService,PageService) {
    var model={};
    EventService.on(EventService.ev.START_CAMERA,function () {
        PageService.setNgViewPage("Camera");
    });
    return model;
}]);