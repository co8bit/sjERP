'use strict';

angular.module('XY').factory('StatementWechatModel', ['EventService', '$log', '$filter', 'PageService', 'NetworkService', '$rootScope',
    function (EventService, $log, $filter, PageService, NetworkService, $rootScope) {
    	var model = {};

		model.data = [];
		model.cid_name = '';
        model.sharePasswd = '123456';
        model.statementWechat_wechatQR = null;
        EventService.on(EventService.ev.START_STATEMENTWECHAT, function (event, dataToSend) {
            $log.debug('dataToSend',dataToSend)
            model.data = [];
            angular.copy(dataToSend,model.data);
            model.cid_name = model.data[0].cid_name;;
            //计算结余
            model.balance = 0;
            model.sharePasswd = Math.floor(Math.random()*8999+1000);
            var dataSend = {
                // data: model.data,
                cid: model.data[0].cid,
                pwd: model.sharePasswd,
            };
            dataSend.idList = [];
            for(var v of model.data){
                model.balance += parseFloat(v.remain);
                if(v.status !=100){
                    if (v.oid > 0) {
                        dataSend.idList.push({id: v.oid,class: 1})
                    }
                    if (v.fid > 0) {
                        dataSend.idList.push({id: v.fid,class: 2})
                    }
                }
            }
            dataSend.idList = JSON.stringify(dataSend.idList)
            // $log.error(dataSend)
            // return
            NetworkService.request('requestStatementOfWechatAccount',dataSend,function (data){
                if (data.EC == 1) {
                    // model.statementWechat_wechatQR = null;
                    model.statementWechat_wechatQR = data.data;
                }
            })
			
			
       	});
    	
    	return model;
    }
])
