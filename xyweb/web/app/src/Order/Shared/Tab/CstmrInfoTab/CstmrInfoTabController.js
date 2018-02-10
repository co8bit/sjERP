//CstmrInfoTabController
'use strict';

angular.module('XY').controller('CstmrInfoTabController', ['EventService', '$scope', '$log', '$timeout', '$filter', 'CstmrModel', 'CstmrService', 'NumPadClassService', 'PageService', 'SalesOrderModel', 'NetworkService',
    function(EventService, $scope, $log, $timeout, $filter, CstmrModel, CstmrService, NumPadClassService, PageService, SalesOrderModel, NetworkService) {
        // 如果订单对象里里有公司名的话，把显示值设定上
        if ($scope.model.orderInfo.company.name) {
            $scope.companyNameInput = $scope.model.orderInfo.company.name
        }
        // 显示往来单位自动完成列表
        $scope.isCompanyListShow = false;
        // 显示联系人电话号码自动完成列表
        $scope.isContactPhonenumListShow = false;
        // 显示停车位置自动完成列表
        $scope.isParkAddressListShow = false;
        // 往来单位信息列表
        $scope.isCstmrInfoShow = false;
        // 车牌号列表
        $scope.isCarLicenseListShow = false;
        //是否显示送货人/联系人
        $scope.contactNameIsShow = SalesOrderModel.contactNameIsShow;
        $scope.whetherDeliver = SalesOrderModel.contactNameIsShow;
        // 往来单位
        $scope.companyList = CstmrService.company.getCompanyList();
        // 建立对联系人信息的映射
        $scope.contactList = CstmrService.contact.getContactList();
        // 映射停车位置
        $scope.parkAddressList = CstmrService.parkAddress.getParkAddressList();

        // 点击新增客户
        $scope.clickAddNewCompany = function() {
            EventService.emit(EventService.ev.START_CREATE_COMPANY, {}, 1);
        }

         

        $scope.clickContact = function(contact) {

            $log.log('clickContact->contact',contact)

            $scope.model.orderInfo.setContact(contact);
            angular.copy(contact, $scope.model.selectedContact);
            $scope.isCstmrInfoShow = true;
        }

        $scope.addContact = function () {
            $scope.model.orderInfo.addContact();
            $scope.model.selectedContact = {};
            $scope.isCstmrInfoShow = true;
        }

        //点击客户输入框
        $scope.clickcompanyNameInput = function() {
            $scope.companyNameInput = '';
            $scope.isCompanyListShow = true;
        }

        //watch客户输入框,输入框内容变化时自动匹配联系人，匹配上了自动选定往来单位
        $scope.$watch('companyNameInput', function(newValue) {
            var company = CstmrService.company.findCompanyByName(newValue);
            $scope.model.orderInfo.setCompany(company);

        })

        // 当选定新的往来单位时自动查询他的联系人信息,以提供“最近使用的联系人”  并在输入框中显示
        $scope.$watch('model.orderInfo.company.cid', function(newValue) {
            $scope.contactList = CstmrService.contact.getContactList();
            // cid>0说明已选定往来单位
            if (newValue > 0) {
                // 查询往来单位的联系人
                CstmrService.contact.queryList(newValue,function(data){
                    // $log.log('data[0]',data[0]);
                    $scope.model.orderInfo.setContact(data[0]);
                });
                // 自动选定往来单位的第一个联系人,不能这样做，因为联系人信息可能还没查回来
                // $scope.clickContact($scope.contactList[0]);
                // 显示名字
                $scope.companyNameInput = $scope.orderInfo.company.name;
                // 更新结余
                $scope.model.orderInfo.calcNumber();
                // $log.log('计算完结余之后',$scope.model.orderInfo);
            }
        });

        var getNewCompanyHandle = EventService.on(EventService.ev.CREATE_COMPANY_SUCCESS, function (event, company) {
            $log.log('newCompany:', company);

            EventService.on(EventService.ev.COMPANY_LIST_LOAD_SUCCESS, function () {
                $scope.companyNameInput = company.name;
            });
        });



        //当发生点击body事件，判断是否点发生在客户姓名输入框
        var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function(angularEvent, domEvent) {

            $timeout(function() {
                // 如果没在输入框上也没在下来列表上就关闭下拉列表
                if (!($(domEvent.target).hasClass('cstmr-name-input')) && !($(domEvent.target).hasClass('auto-complete-content'))) {
                    $scope.isCompanyListShow = false;
                }

                // 关闭联系人电话自动完成列表
                if (!($(domEvent.target).hasClass('contact-phonenum'))) {
                    $scope.isContactPhonenumListShow = false;
                }

                // 关闭停车位置自动完成列表
                if (!($(domEvent.target).hasClass('car-license-input'))) {
                    $scope.isCarLicenseListShow = false;
                }

                // 关闭停车位置自动完成列表
                if (!($(domEvent.target).hasClass('parkaddress-input'))) {
                    $scope.isParkAddressListShow = false;
                }

            });
        })

        // 计算金额（优惠，应收实收应付实付和结余的）
        $scope.updatePaymentInfo = function() {
            $scope.orderInfo.calcNumber();
        }


        //销毁监听
        $scope.$on('$destroy', function() {
            clickHandle();
            getNewCompanyHandle();
        });


        $scope.$watchGroup(['orderInfo.off','orderInfo.online_pay','orderInfo.cash','orderInfo.bank'],function() {
            $scope.orderInfo.calcNumber()
        })

        function pressNumCallback(state, num) {
            switch (state) {
                case 0:
                    $scope.orderInfo.off = $scope.numPad.joinTwoNum($scope.orderInfo.off, num);
                    break;
                case 1:
                    $scope.orderInfo.online_pay = $scope.numPad.joinTwoNum($scope.orderInfo.online_pay, num);
                    break;
                case 2:
                    $scope.orderInfo.cash = $scope.numPad.joinTwoNum($scope.orderInfo.cash, num);
                    break;
                case 3:
                    $scope.orderInfo.bank = $scope.numPad.joinTwoNum($scope.orderInfo.bank, num);
                    break;
                default:
                    ;
            }
        }

        function pressOtherCallback(state, input) {
            switch (input) {
                case 'Clear':
                    switch (state) {
                        case 0:
                            $scope.orderInfo.off = '';
                            break;
                        case 1:
                            $scope.orderInfo.online_pay = '';
                            break;
                        case 2:
                            $scope.orderInfo.cash = '';
                            break;
                        case 3:
                            $scope.orderInfo.bank = '';
                            break;
                    }
                    break;
                case 'Finish':
                    break;
                default:
                    ;
            }
        }

        // 这个页面上的小键盘状态 :
        // state == 0 , 修改数量
        // 1 , 修改单价
        $scope.numPad = NumPadClassService.newNumPad(pressNumCallback, pressOtherCallback);
        $scope.numPad.state = 0;
        $scope.$watch('whetherDeliver', function(newValue) {
            SalesOrderModel.contactNameIsShow = newValue;
            SalesOrderModel.setOptionArray();
            //newValue是false又，说明是选中了送货退出来了，要重新请求list然后重置第一个为联系人
            if(newValue==false && $scope.model.orderInfo.company.cid != undefined){
                CstmrService.contact.queryList($scope.model.orderInfo.company.cid,function(data){
                    // $log.log('data[0]',data[0]);
                    $scope.model.orderInfo.setContact(data[0]);
                    $scope.model.orderInfo.mobile = ''
                    $scope.model.orderInfo.car_license = ''
                });
            }

//          //选货勾选状态
//          if($scope.whetherDeliver != undefined){
//              // Cookies.set('whetherDeliver',$scope.whetherDeliver);
//              model.contactNameIsShow = $scope.whetherDeliver;
//          }

        })
    }
]);



