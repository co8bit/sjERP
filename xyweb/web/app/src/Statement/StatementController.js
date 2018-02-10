'use strict';

// 对账单页面
xy.controller('StatementController', ['$scope', '$log', 'PageService', 'EventService', 'StatementModel',
    function($scope, $log, PageService, EventService, model) {

        $scope.data = model.data;
        $scope.contactList = model.contactList;
        $scope.phone = model.phone ;
        $scope.checkedContact = $scope.contactList[0];
        $scope.contactList[0]['checked-contact'] = 'checked-contact';
        $log.debug('scope phone:'+$scope.phone);
        $log.debug('model  '+model.phone);
        $scope.isHas = true; // 默认选取已有联系人
        $scope.contactNow = '';
        $scope.checkedContact = {};
        var isOneClick = true;//是否第一次点击
        $scope.sendStatementWithSMS = function () {
            if (isOneClick){
                isOneClick = false;//不是第一次点击
                model.sendStatementWithSMS($scope.phone);
                $scope.quit();
                setTimeout(function(){
                    isOneClick = true;
                },1000)

            }
        };

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
        $scope.checkReceiver = function () {
            $log.debug('alert check receiver dialog.');
        model.dialogId = PageService.showDialog('CheckReceiver');
        }
        $scope.checkContact = function (contact) {
            // $scope.checkedContact['checked-contact'] = "";
            // $scope.checkedContact = contact;
            // $scope.checkedContact['checked-contact'] = 'checked-contact';
            $scope.contactNow = contact.contact_name;
            $scope.checkedContact = contact;

        }
        $scope.checkMobile = function (value) {
            if(!$scope.phone.includes(value.mobile)){
                if($scope.phone == ''){
                    $scope.phone += value.mobile;
                }else{
                    $scope.phone += ',' + value.mobile;
                }
            }
        }
        $scope.checkPhone = function (mobile) {
            $scope.mobile = mobile;
        }
        $scope.closeCheckReceiver = function () {
            if(model.phone.length > 0){
                var temp = model.phone +"," + $scope.mobile;
                }else{
                    var temp = model.phone + $scope.mobile;
                }
            PageService.closeDialog();
            model.phone = temp;
            $log.debug("append phone2:" + model.phone);
            EventService.emit(EventService.ev.START_CHECK_MOBILE,$scope.mobile);
        }
        EventService.on(EventService.ev.START_CHECK_MOBILE,function (event,mobile) {
            $log.debug("Receive mobile:" + mobile);
            $scope.phone = model.phone;
            // if($scope.phone.search(mobile)<0)
            //     if($scope.phone > 0 ){
            //         $scope.phone = $scope.phone +',' + mobile;
            //     }else {
            //         $scope.phone = $scope.phone +mobile;
            //     }
        })

    }
]);
