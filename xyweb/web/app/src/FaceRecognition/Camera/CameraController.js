/**
 * Created by 木马 on 2017/7/18.
 */
xy.controller("CameraController",['$scope','$log','$q','NetworkService',
    function ($scope,$log,$q,NetworkService) {
        var video = document.getElementById("video");
        var pic;
        var canvas = document.getElementById("canvas");
        var context = canvas.getContext("2d");
        var errocb = function () {
            console.log('sth wrong!');
        };
        if (navigator.getUserMedia) { // 标准的API
            navigator.getUserMedia({ "video": true }, function (stream) {
                video.src = URL.createObjectURL(stream);
                video.play();
            }, errocb);
        } else if (navigator.webkitGetUserMedia) { // WebKit 核心的API
            navigator.webkitGetUserMedia({ "video": true }, function (stream) {
                video.src = window.webkitURL.createObjectURL(stream);
                video.play();
            }, errocb);
        }
        function drawImg() {
            context.drawImage(video, 0, 0, 640, 480);
            $('#canvas').faceDetection({
                complete: function (faces) {
                    if(faces.length>0){
                        var dataType={};
                        dataType.img = canvas.toDataURL("image/png");
                        NetworkService.request("sendPicture",dataType,function (data) {
                            $log.error(data);
                        })
                    }
                }
            });
        }
        $scope.start = function(time) {
            pic = setInterval(function () {
                drawImg()
            },time)
        };
        $scope.stop = function() {
            clearInterval(pic);
        };
        $scope.start(3000);
}]);