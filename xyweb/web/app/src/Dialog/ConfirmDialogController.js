'use strict';

xy.controller('ConfirmDialogController', ['EventService', '$scope', '$log',
    function (EventService, $scope, $log) {

        $scope.isShow = false;

        $scope.pressEnter = function () {
            if (!$scope.isShow) {
                return;
            }
            $scope.isShow = false;
            if ($scope.callback0) {
                $scope.callback0();
            }
        }

        $scope.pressF2 = function () {
            if (!$scope.isShow) {
                return;
            }
            $scope.isShow = false;
            if ($scope.callback1) {
                $scope.callback1();
            }
        }

        $scope.pressEsc = function () {
            if (!$scope.isShow) {
                return;
            }
            $scope.isShow = false;
            if ($scope.callback2) {
                $scope.callback2();
            }
        }

        var dialogHandle = EventService.on(EventService.ev.CONFIRM_DIALOG, function (event, args) {
            $scope.isShow = true;
            $scope.content = args.content;
            $scope.btns = args.btns;
            $scope.callback0 = args.callback0;
            $scope.callback1 = args.callback1;
            $scope.callback2 = args.callback2;
        })

        $scope.click = function (index) {
            $scope.isShow = false;
            switch (index) {
                case 0:
                    if ($scope.callback0) {
                        $scope.callback0();
                    }
                    break;
                case 1:
                    if ($scope.callback1) {
                        $scope.callback1();
                    }
                    break;
                case 2:
                    if ($scope.callback2) {
                        $scope.callback2();
                    }
                    break;
            }
        };

        //
        // var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
        //     if (domEvent.keyCode == 27) {
        //
        //     }
        // })


        // $scope.pressEnter = function () {
        //     console.log('enter-------keydown-------');
        //     if (!PageService.isConfirmDialogShow) {
        //         return;
        //     }
        //     PageService.isConfirmDialogShow = false;
        //     if (PageService.confirmDialog.callback0) {
        //         PageService.confirmDialog.callback0();
        //     }
        // }
        //
        // $scope.pressF2 = function () {
        //     console.log('f2-------keydown-------');
        //     if (!PageService.isConfirmDialogShow) {
        //         return;
        //     }
        //     PageService.isConfirmDialogShow = false;
        //     if (PageService.confirmDialog.callback1) {
        //         PageService.confirmDialog.callback1();
        //     }
        // }
        //
        // $scope.pressEsc = function () {
        //     console.log('esc-------keydown-------');
        //     if (!PageService.isConfirmDialogShow) {
        //         return;
        //     }
        //     PageService.isConfirmDialogShow = false;
        //     if (PageService.confirmDialog.callback2) {
        //         PageService.confirmDialog.callback2();
        //     }
        // }

    }
]);

