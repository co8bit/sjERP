//xxxxController
'use strict';

angular.module('XY').controller('ShopInfoController', ['EventService', '$scope', '$log','PageService','ConfigService','UserService','ManagementModel',
    function(EventService, $scope, $log,PageService,ConfigService, UserService, model) {
    	$scope.$log = $log;
        $scope.PageService = PageService;

        // 引用店铺信息
        $scope.model = model;
        $scope.shopInfo = model.shopInfo;
        $scope.isYqj = model.isYqj;
        $scope.isEditting = UserService.getLoginStatus().rpg == 1 || UserService.getLoginStatus().rpg == 8 ? true : false;
        // 地址选择器相关
        // $scope.geoData = ConfigService.geoData;
        // $scope.selectedProvince = $scope.geoData[0];
        // $scope.selectedCity =     $scope.selectedProvince.sub[0];
        // $scope.selectedDistrict = $scope.selectedCity.sub[0];
        //当省份变化时，城市自动选择 省份.sub[0]
        // $scope.$watch('model.selectedProvince',function(){
        //  if ($scope.model.selectedProvince.sub) {
        //      $scope.model.selectedCity = $scope.model.selectedProvince.sub[0];
        //  }
        // });

        // 完成编辑
        $scope.clickFinishEdit = function(){
            $scope.model.shopInfo.province = model.selectedProvince == undefined ? '' : model.selectedProvince.name;
            $scope.model.shopInfo.city = model.selectedCity == undefined ? '' : model.selectedCity.name;
            UserService.editShopInfo(model.shopInfo);
        }

        // $scope.$watch('shopInfo.shop_name',function () {
        //     if($scope.shopInfo.shop_name) {
        //
        //     }
        // });

        // $scope.$watch('selectedCity',function(){
        // 	if ($scope.selectedCity) {
	       //  	if ($scope.selectedCity.sub) {
	       //  		$scope.selectedDistrict = $scope.selectedCity.sub[0];
	       //  	}
        // 	}
        // });


    }

]);

