/**
 * Created by 木马 on 2017/7/18.
 */
angular.module("XY").factory("FaceDetailModel",["$log","$q","EventService","NetworkService","PageService",
    function ($log,$q,EventService,NetworkService,PageService) {
    var  Model ={};
EventService.on(EventService.ev.OPEN_FACE_DETAIL,function (arg) {
    PageService.setNgViewPage("FaceDetail");
});
    return Model;
}]);