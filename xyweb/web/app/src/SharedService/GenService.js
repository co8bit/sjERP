//生成器服务,用于生成测试数据调试用
'use strict';

xy.factory('GenService', ['EventService','$log',
    function(EventService,$log) {


        var GenService = {}; 

        var nameList = ['Jimmy','Ulysses','Katte','Jordan','Southey','Esther','Coverdale','Billy','Marjory','Keynes'];
        
        // 随机生成名字
        GenService.genName = function() {
        	return nameList[Math.floor(Math.random()*10)];
        }

        // 随机生成手机号
        GenService.genPhoneNum = function(){
        	return Number('1' + Math.floor(Math.random()*10000000000).toString());
        }

        // $log.log(123);
        // $log.log(GenService.genPhoneNum(),GenService.genName());


        function init() {

        }

        init();


        return GenService; // or return model

    }
]);