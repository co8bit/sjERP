'use strict'
xy.factory('ForgetModel', ['EventService','$log', function(EventService, $log){
	//忘记密码使用model
	var model = {};

    // 接收事件判断是否为恢复密码状态
    EventService.on(EventService.ev.IF_RECOVERY,function(event,arg){
	    model.recovery = false;
        if(arg == 1){
            model.recovery = true;
        }
    })


	return model;

}])