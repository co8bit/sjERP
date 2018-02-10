//xxxxModel or Service or Class
'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('ManagementModel', ['EventService', '$log', 'PageService', 'CstmrService', 'UserService', 'ConfigService','PrintSettingsModel',
    function(EventService, $log, PageService, CstmrService, UserService, ConfigService, PrintSettingsModel) {

        var model = {};

        // 店铺设置相关
        model.shopInfo = {};
        model.geoData = ConfigService.geoData;
        model.selectedProvince = model.geoData[0];
        model.selectedCity = model.selectedProvince.sub[0];
        model.isYqj = false;
        EventService.on(EventService.ev.START_VIEW_SHOPINFO, function(event, arg) {

            UserService.getShopInfo(function(data) {
                angular.copy(data, model.shopInfo);

                // 匹配当前选定的省市
                for (var i = 0; i < model.geoData.length; i++) {
                    if (model.geoData[i].name == model.shopInfo.province) {
                        model.selectedProvince = model.geoData[i];
                        for (var j = 0; j < model.selectedProvince.sub.length; j++) {
                            if (model.selectedProvince.sub[j].name == model.shopInfo.city) {
                                model.selectedCity = model.selectedProvince.sub[j];
                                break;
                            }
                        }
                        break;
                    }
                }
                if(arg == 'YQJ'){
                    model.isYqj = true;
                }

                PageService.setNgViewPage('ShopInfo');
            });
        });

        EventService.on(EventService.ev.user_editShopInfo,function(event,arg){
            if (arg > 0) {
                EventService.emit(EventService.ev.START_VIEW_SHOPINFO);
            }
        });

        EventService.on(EventService.ev.START_CREATE_EDIT_PARK_ADDRESS, function(event, arg) {
            if (arg) {
                model.editingParkAddress = {};
                angular.copy(arg, model.editingParkAddress);
                model.dialogId = PageService.showDialog('CreateEditParkAddress');
            } else {
                model.editingParkAddress = {};
                model.dialogId = PageService.showDialog('CreateEditParkAddress');
            }
        });

        //打开系统设置
        EventService.on(EventService.ev.SYSTEM_SETTINGS, function () {
            PageService.setNgViewPage('SystemSettings');
        });

        return model; // or return model

    }
]);
