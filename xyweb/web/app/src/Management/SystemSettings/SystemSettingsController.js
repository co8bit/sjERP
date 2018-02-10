'use strict';

angular.module('XY').controller('SystemSettingsController', ['EventService', '$scope', '$log', 'PageService', 'NetworkService', '$http', 'AuthGroupService',
    function (EventService, $scope, $log, PageService, NetworkService, $http, AuthGroupService) {
        //上传文件时显示等候蒙版
        $scope.maskIsShow = false;
        var domain = NetworkService.getDomain();
        $scope.From_ForCompany = domain + 'index.php?m=Home&c=Other&a=loadExcelFrom_ForCompany';
        $scope.From_ForSku = domain + 'index.php?m=Home&c=Other&a=loadExcelFrom_ForSku';
        $scope.isYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);
        NetworkService.request('config_getShopConfig', '', function (data) {
            $scope.shopConfig = data.data;
        });
        $scope.edit = function () {
            NetworkService.request('config_setShopConfig', $scope.shopConfig, function () {
                PageService.showSharedToast('保存成功！');
            });
        }
        $scope.submitForm = function (type) {
            var url = '', file = '';
            if (type == 1) {
                url = $scope.From_ForCompany;
                var fd = new FormData();
                file = document.getElementById('forCompany').files[0];
                fd.append('exceldata', file);
            } else {
                url = $scope.From_ForSku;
                var fd = new FormData();
                file = document.getElementById('forSku').files[0];
                fd.append('exceldata', file);
                //     exceldata : document.getElementById('forSku').value
            }

            if(file == undefined){
               PageService.showConfirmDialog('请选择要上传的文件');
                return;
            }
            $scope.maskIsShow = true;
            $log.debug('file', file)
            $http({
                method: 'POST',
                url: url,
                data: fd,
                // {
                //     exceldata : file
                // },
                headers: {'Content-Type': undefined},
                transformRequest: angular.identity
            }).success( function ( response ){
                    //上传成功的操作
                if(response.EC < 0){
                    PageService.showConfirmDialog('上传失败：' + response.MSG);
                    $log.warn(url + ' ajax success! EC: ', response.EC);
                    $log.warn('  ' + url + ' send: ', file);
                    $log.warn('  ' + url + ' response: ', response);
                }else{
                    PageService.showConfirmDialog("上传成功！");
                }

                $scope.maskIsShow = false;
            }).error( function(){
                 PageService.showConfirmDialog('上传失败！');
                 $scope.maskIsShow = false;
            });
        }
        $(function(){
            var url = NetworkService.getUrl('query_backUpEveryDay');
            $('#backup').attr('action', url);
        })
    }
]);