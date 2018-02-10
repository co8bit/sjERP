//model or Service
'use strict'

xy.factory('NumPadClassService', ['$rootScope', '$log', 'StockService', 'CstmrService',
    function($rootScope, $log, StockService, CstmrService) {

        var NumPadClassService = {} // or xxxxService = {}


        /* 在controller里的写法

        $scope.padInput1 = '11'
        $scope.padInput2 = '22'
        function pressNumCallback(state, num) {
            switch (state) {
                case 0:
                    $scope.padInput1 = $scope.numPad.joinTwoNum($scope.padInput1, num)
                    break
                case 1:
                    $scope.padInput2 = $scope.numPad.joinTwoNum($scope.padInput2, num)
                    break
                default:
            }
        }
        function pressStepCallback(Step){
            $scope.numPad.state = 1 - $scope.numPad.state
        }
        $scope.numPad = NumPadClassService.newNumPad(pressNumCallback, pressStepCallback)
        $scope.numPad.state = 0
        <!-- 小键盘 -->
        <!--     <input type="text" ng-model="padInput1" ng-class="{ 'activeInput' : numPad.state == 0 }" ng-click="numPad.setState(0)">
        <br>
        <input type="text" ng-model="padInput2" ng-class="{ 'activeInput' : numPad.state == 1 }" ng-click="numPad.setState(1)">
        <br>
        <button ng-click="numPad.pressStep()">Next</button>
        <br>
        <div ng-include="'a.html'"></div> -->
        小键盘类
        @param 状态到要修改的变量的映射
        @param 按步骤键的回调

        */


        function NumPadClass(pressNumCallback, pressOtherCallback) {

            this.NumPadClassCtor = function() {
                this.state = 0
            }

            this.buttonNameStr = {
                'Finish':'完成',
            }

            // 设定按钮显示名
            this.setButtonStr = function(buttonId,buttonNameStr) {
                this.buttonNameStr[buttonId] = buttonNameStr
            }

            //$log.debug('pressNumCallback= ',pressNumCallback)
            var pressNumCallback = pressNumCallback
            var pressStepCallback = pressStepCallback

            //按了数字键的回调
            this.pressNum = function(num) {
                pressNumCallback(this.state, num)
            }

            this.pressOther = function(input) {
                pressOtherCallback(this.state, input)
            }
                // 连接两个数字
            this.joinTwoNum = function(num1, num2) {
                var out
                out = num1.toString() + num2.toString();
                return out
            }
            this.setState = function(state) {
                this.state = state
            }
        }

        NumPadClassService.newNumPad = function(pressNumCallback, pressStepCallback) {
            var out = new NumPadClass(pressNumCallback, pressStepCallback)
            out.NumPadClassCtor();
            return out
        }



        //初始化
        function init() {


        }

        init()

        return NumPadClassService // or return xxxxService

    }
])
