//开报溢报损单Model
'use strict';

xy.factory('CreateOverflowOrLossModel', ['$rootScope', '$log', 'StockService', 'CstmrService','OrderInfoClassService','CartClassService',
    function($rootScope, $log, StockService, CstmrService,OrderInfoClassService,CartClassService) {

      
        // $log.debug('CreateOverflowOrLossModel init'); 

        var model = {}; 

        function init() {
        	//报溢报损单对象,本业务的核心
        	//model.order = OrderInfoClassService.newOverFlowOrLossOrder()
        }

        init();


        return model; 

    }
]);



