'use strict';

// 对账单页面
xy.controller('CheckReceiverController', ['$scope', '$log', 'PageService', 'EventService', 'StatementModel',
    function($scope, $log, PageService, EventService, model) {

        $scope.contactList = model.contactList;

        $scope.checkedContact = $scope.contactList[0];
        $scope.contactList[0]['checked-contact'] = 'checked-contact';




        $scope.quit = function() {
            PageService.closeDialog();
        }

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
                $scope.$apply(function () {
                    $scope.quit();
                })
            }
        });

        $scope.$on('$destroy', function () {
            keydownHandle();
        });

        //待分离
        $scope.mobile = "";
        $scope.checkReceiver = function() {
            $log.debug('alert check receiver dialog.');
            model.dialogId = PageService.showDialog('CheckReceiver');
        }
        $scope.checkContact = function (contact) {
            $scope.checkedContact['checked-contact'] = "";
            $scope.checkedContact = contact;
            $scope.checkedContact['checked-contact'] = 'checked-contact';
        }
        $scope.checkPhone = function (mobile) {
            $scope.mobile = mobile;
        }
        $scope.closeCheckReceiver = function () {
            // $log.log('ssss',model.phone.length);
            if(model.phone.length>0){
                var temp = model.phone +"," + $scope.mobile;
                }else{
                    var temp = model.phone + $scope.mobile;
                }
            PageService.closeDialog();
            model.phone = temp;
            $log.debug("append phone:"+model.phone);
            EventService.emit(EventService.ev.START_CHECK_MOBILE,$scope.mobile);
        }

    }
]);
