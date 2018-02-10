//controller
'use strict';

xy.controller('CreateOverflowOrLossController', ['$rootScope', '$scope', '$log', 'PageService', 'StockService', 'CartClassService', 'CreateOverflowOrLossModel',
    'OrderService',
    function($rootScope, $scope, $log, PageService, StockService, CartClassService, model,
        OrderService) {

        $log.debug('CreateOverflowOrLossController');



        $scope.test = function() {
            //$log.debug("spus:", StockService.get_spus_info())
            $log.debug('order: ',$scope.order)
        }

        //点击商品列表中的商品
        $scope.clickSPU = function(spu) {

            $scope.cartAgent.startAddingANewCartItem(spu)
            $scope.$broadcast('SPU_SELECTED')
            //$('#dialog-add-new').dialog('open')
        }



        //去选择盘点人
        $scope.gotoSelectChecker = function() {
            PageService.setCallback('finishSelectChecker', function() {
                PageService.setCurrentPage('CreateOverflowOrLoss')
            })
            PageService.setCurrentPage('SelectUser')
        }

        //提交报溢报损单
        $scope.createOverflowOrLoss = function() {
            OrderService.createOverflowOrLoss($scope.order)
        }



        $scope.initDialog = function() {
            $("#dialog-add-new").dialog({
                appendTo: '.functionPage',
                autoOpen: false,
                show: {
                    duration: 200
                },
                modal: true,
                open: function() {
                    $('.ui-widget-overlay').hide().fadeIn(100);
                },

            })
        }


        function init() {

            $scope.checker = 1

            //商品列表
            $scope.goodList = StockService.get_spus_info()
            //报溢报损单对象
            $scope.order = model.order
            //购物车
            $scope.cartAgent = model.order.cartAgent

        }

        init()



        // to delete
        // 监听ng-include来初始化对话框，已使用onload替代
        // $scope.$on('$includeContentLoaded', function(event, templateName) {

        //     $log.debug('templateName: ', templateName)

        //     if (templateName == 'src/SharedComponent/EditCartItemDialog/AddANewCartItem.html') {
        //         $("#dialog-add-new").dialog({
        //             appendTo: '.functionPage',
        //             autoOpen: false,
        //             show: {
        //                 duration: 200
        //             },
        //             modal: true,
        //             open: function() {
        //                 $('.ui-widget-overlay').hide().fadeIn(100);
        //             },

        //         })

        //     }

        // })




    }
]);
