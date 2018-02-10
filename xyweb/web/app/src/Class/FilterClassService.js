'use strict';

// or xy.factory('xxxxModel')
xy.factory('FilterClassService', ['$rootScope', '$log', 'StockService', 'CstmrService',
    function($rootScope, $log, StockService, CstmrService) {

      
        // $log.debug('FilterClassService init');

        var service = {}; // or xxxxModel = {}

        function docFilter() {

            this.search = ""

            // 时间选择 下标0-1:上午,下午
            this.bTime = [1,1]

            //文件类别
            this.cDocClass = [1,1,1,1,1]
            //订单状态
            this.status = []

            //


            //指定开单人
            this.bCheckOperator = false
            this.operator_uid = 0

            this.check = function(value) {

                var out = true

                // 过滤搜索输入框
                var bSearch = false
                if ((value.oid.toString().search(this.search) >= 0) || (value.cart.search(this.search) >= 0) || (value.remark.search(this.search) >= 0)) {
                    bSearch = true
                }
                out = out && bSearch



                return out
            }

        }


        service.newDocFilter = function() {
        	return new docFilter()
        }

                //初始化
        function init() {


        }

        init();


        return service; // or return xxxxModel

    }
]);


