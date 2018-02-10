//xxxxController
'use strict';

xy.controller('NumPadController', ['EventService', '$scope', '$log', 'NumPadClassService',
    function(EventService, $scope, $log, NumPadClassService) {


        // $log.log('NumPadController')
        // <!-- 小键盘 -->
        // <!--     <input type="text" ng-model="padInput1" ng-class="{ 'activeInput' : numPad.state == 0 }" ng-click="numPad.setState(0)">   
        // <br> 
        // <input type="text" ng-model="padInput2" ng-class="{ 'activeInput' : numPad.state == 1 }" ng-click="numPad.setState(1)">
        // <br>
        // <button ng-click="numPad.pressStep()">Next</button> 
        // <br>
        // <div ng-include="'a.html'"></div> -->

        // $scope.$on('EDITING_GOOD_CHANGED', function() {
        //     $log.log('EDITING_GOOD_CHANGED');
        //     $scope.numPad.setState(0);
        // })

        // function pressNumCallback(state, num) {
        //     switch (state) {
        //         case 0:
        //             $scope.cartAgent.newCartItem.quantity = $scope.numPad.joinTwoNum($scope.cartAgent.newCartItem.quantity, num);
        //             break;
        //         case 1:
        //             $scope.cartAgent.newCartItem.unit_price = $scope.numPad.joinTwoNum($scope.cartAgent.newCartItem.unit_price, num);
        //             break;
        //         default:;

        //     }
        // }

        // function pressOtherCallback(state, input) {
        //     switch (input) {
        //         case 'Cancel':
        //         	switch (state) {
        //         		case 0:
        //         			$scope.cartAgent.newCartItem.quantity = '';
        //         			break;
        //         		case 1:
        //         			$scope.cartAgent.newCartItem.unit_price = '';
        //         			break;
        //         	}
        //             break;
        //         case 'Step':
        //             $scope.numPad.state = 1 - $scope.numPad.state;
        //             break;
        //         default:;
        //     }

        // }

        // function init() {
        //     $scope.numPad = NumPadClassService.newNumPad(pressNumCallback, pressOtherCallback);
        //     $scope.numPad.state = 0;
        // }

        // init();

    }
]);
