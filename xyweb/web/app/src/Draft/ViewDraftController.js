// 查看草稿单据Controller
'use strict';

angular.module('XY').controller('ViewDraftController', ['EventService', '$scope', '$log', 'PageService', 'ViewDraftModel','MiscService','OrderService','NetworkService', '$q',
    function(EventService, $scope, $log, PageService, model, MiscService, OrderService,NetworkService, $q) {

        $scope.PageService = PageService;
        $scope.EventService = EventService;
        $scope.rpg = model.rpg;
        $scope.pageState = model.pageState;
        $scope.model = model;
        $scope.query = model.query;
        $scope.data = model.query.getData();
        $scope.paginator = model.query.getPaginator();
        $scope.statusCheckbox = model.query.getStatusCheckbox();


        setTimeout(function() {
            $scope.data = model.query.getData();
            // $log.log('data',$scope.data);
        }, 300);

        $scope.activeTabNum = $scope.pageState.activeTab;
        $scope.isMobile = MiscService.testMobile();
        EventService.on(EventService.ev.VIEW_BACK_TO_DRAFT,function(event,args){


            var pageItem = {
                content: $scope.paginator.now_page,
            };

            switch(args){
                case 2:
                    model.query.getStatusCheckbox().selectExclusiveById(2);
                    $scope.pageState.activeTab = 2;
                    $scope.activeTabNum = 2;
                    break;
                case 3:
                    model.query.getStatusCheckbox().selectExclusiveById(5);
                    $scope.pageState.activeTab = 3;
                    $scope.activeTabNum = 3;
                    break;
                default:
                    break;
            }

            $scope.paginator.clickButton(pageItem);

        });

        $scope.switchTab = function (tab) {
            switch (tab) {
                case 1:
                    $scope.query.setIsDraft(true);
                    $scope.pageState.activeTab = 1;
                    $scope.activeTabNum = 1;
                    break;
                case 2:
                    model.query.cleanTime();
                    model.query.setCid('');
                    $scope.statusCheckbox.selectExclusiveById(2);
                    $scope.pageState.activeTab = 2;
                    $scope.activeTabNum = 2;
                    break;
                case 3:
                    model.query.cleanTime();
                    model.query.setCid('');
                    $scope.statusCheckbox.selectExclusiveById(5);
                    $scope.pageState.activeTab = 3;
                    $scope.activeTabNum = 3;
                    break;
            }
        }

        $scope.continueToFill = function(item) {
            if (item.class == 73) {
                EventService.emit(EventService.ev.CONTINUE_CREATE_INCOME,item.fid)
            }
            if (item.class == 74) {
                EventService.emit(EventService.ev.CONTINUE_CREATE_EXPENSE,item.fid)
            }
            if (item.class == 71) {
                EventService.emit(EventService.ev.CONTINUE_CREATE_RECEIPT,item.fid)
            }
            if (item.class == 72) {
                EventService.emit(EventService.ev.CONTINUE_CREATE_PAYMENT,item.fid)
            }
            if ( item.class >= 1  && item.class <= 4 ) {
                EventService.emit(EventService.ev.CONTINUE_CREATE_ORDER,item.oid)
            }
            if (item.class == 53) {
                EventService.emit(EventService.ev.CONTINUE_STOCK_TAKING,item.wid)
            }
            if(item.class == 54){
                EventService.emit(EventService.ev.CONTINUE_REQUISITION,item.wid)
            }
        }

        $scope.viewDetails = function(item) {
            MiscService.sendViewDetailsEvent(item)

            var args = {
                orderPage : model.orderPage,
                draftTab : $scope.pageState.activeTab,
            }
            EventService.emit(EventService.ev.ORDER_ARGS, args)
        }

        $scope.Finished = function (item) {
            item.status = 1;
            OrderService.setOrderStatus(item, function () {
                EventService.emit(EventService.ev.START_VIEW_DRAFT,2);
            });
        }

        $scope.notifyDispatch = function (item) {
            item.status = 6;
            OrderService.setOrderStatus(item, function () {
                EventService.emit(EventService.ev.START_VIEW_DRAFT,3);
            });
        }

        $scope.deleteDraft = function(item){
            var defered = $q.defer();
            /**
             * 删除草稿
             */
            PageService.showConfirmDialog('确定删除草稿?', [], function () {
                // $log.log('now_page',$scope.paginator.now_page);
                var len = $scope.data.length;
                var _content = angular.copy($scope.paginator.now_page);
                var pageItem = {
                        content: _content,
                    };

                if( item.class>=1 && item.class<=4 ){
                    NetworkService.request('DeleteOrderDraft',{oid:item.oid},function(){
                        defered.resolve();
                    });
                }else {
                    if( item.class>=51 && item.class<=53 ){
                        NetworkService.request('DeleteWarehouseDraft',{wid:item.wid},function(){
                            defered.resolve();
                        });
                    }else {
                        if( item.class>=71 && item.class<=74 ){
                            NetworkService.request('DeleteFinanceDraft',{fid:item.fid},function(){
                                defered.resolve();
                            });
                        }
                    }
                }
            });
            defered.promise.then(function(){
                if(len<=1&&pageItem.content>1){
                    pageItem.content = pageItem.content - 1;
                }
                $scope.paginator.clickButton(pageItem);
            })
            $scope.query.setIsDraft(true);
            return defered.promise;
        }
    }
]);
