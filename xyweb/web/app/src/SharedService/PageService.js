// PageService 页面服务，处理页面跳转, 弹出对话框等
'use strict'

xy.factory('PageService', ['$rootScope', '$log', '$window', 'ConfigService', '$timeout', '$location',
    function ($rootScope, $log, $window, ConfigService, $timeout, $location) {
        var xyvs = '?xyvs=2.0.1';//版本号
        //回调列表 给页面间协作使用,前面的的Controller销毁之前设置一下回调，后面的Controller到这里来获取
        var callbackList = {};
        //跳转主页问题
        var host = $location.host();
        var domainSkin = '';
        if (host == 'localhost') {
            domainSkin = 'http://' + host + '/index';
        } else {
            domainSkin = 'http://' + host;
        }
        // 页面名字对应的html相对于ConfigService.srcLocation的路径
        var dialogPageNameToUrl = {
            SalesOrder: 'Order/SalesOrder/SalesOrder.html' + xyvs,

            NewSalesOrder: 'Order/NewSalesOrder/NewSalesOrder.html' + xyvs,


            OrderDetails: 'Order/OrderDetails/OrderDetails.html' + xyvs,
            OrderDetailsExceptionPrompt: 'Order/OrderDetails/OrderDetailsExceptionPrompt.html' + xyvs,

            CreateEditCat: 'SPUSKU/CreateEditCat.html' + xyvs,
            CreateSPU: 'SPUSKU/CreateSPUBigDialog.html' + xyvs,
            EditSPU: 'SPUSKU/EditSPU.html' + xyvs,
            CreateCstmr: 'Cstmr/CreateCstmr.html' + xyvs,
            IncomeAndExpense: 'Finance/IncomeAndExpense/IncomeAndExpense.html' + xyvs,


            ModifyIncomeOrExpense: 'Finance/ModifyIncomeOrExpense/ModifyIncomeOrExpense.html' + xyvs,
            ModifyIncomeOrExpenseDetails: 'Finance/Details/ModifyIncomeOrExpenseDetails.html' + xyvs,

            IncomeAndExpenseDetails: 'Finance/Details/IncomeAndExpenseDetails.html' + xyvs,
            ReceiptAndPaymentDetails: 'Finance/Details/ReceiptAndPaymentDetails.html' + xyvs,
            ReceiptAndPayment: 'Finance/ReceiptAndPayment/ReceiptAndPayment.html' + xyvs,
            StockTaking: 'Stock/StockTaking/StockTaking.html' + xyvs,
            StockTakingDetails: 'Stock/StockTaking/StockTakingDetails.html' + xyvs,
            Requisition: 'Requisition/Requisition.html' + xyvs,

            CreateEditUser: 'Management/ManageUser/CreateEditUser.html' + xyvs,
            CreateEditParkAddress: 'Management/ParkAddress/CreateEditParkAddress.html' + xyvs,

            ExpenseCenter: 'ExpenseCenter/ExpenseCenter.html' + xyvs,
            AccountPay: 'AccountPay/AccountPay.html' + xyvs,
            Statement: 'Statement/Statement.html' + xyvs,
            CheckReceiver: 'Statement/CheckReceiver.html' + xyvs,


            // ExperienceRegister: 'Login/ExperienceRegister.html' + xyvs,
            CreateEditWarehouse: 'Warehouse/CreateEditWarehosue/CreateEditWarehosue.html' + xyvs,
            WareHouse: "Warehouse/Warehouse.html?xyvs=2.0.1",
            FaceRecognition: 'FaceRecognition/FaceRecognition.html' + xyvs,//人脸识别
            FaceDetail: 'FaceRecognition/FaceDetail/FaceDetail.html' + xyvs,//人脸识别详情
            StockDetail: 'StockDetail/StockDetail.html' + xyvs,//仓库单详情
            //易企记开始
            CreateEditAccount: 'yiqiji/AccountSetting/CreateEditAccount.html' + xyvs, //新建编辑账户
            CreateOrEditCat: 'yiqiji/CreateOrEditCat/CreateOrEditCat.html' + xyvs, //创建编辑类别
            CreateIncomeOrExpense: 'yiqiji/Finance/IncomeOrExpense/IncomeOrExpense.html' + xyvs, //创建支出收入单
            CreateTransferAccounts: 'yiqiji/Finance/TransferAccounts/TransferAccounts.html' + xyvs, //创建内部转账单
            CreateWithdraw: 'yiqiji/Finance/Withdraw/Withdraw.html' + xyvs, //创建内部提现单
            ReceiptFill: 'yiqiji/Finance/ReceiptFill/ReceiptFill.html' + xyvs, //发票填补
            IncomeAndExpendDetail: 'yiqiji/Detail/IncomeAndExpendDetail/IncomeAndExpendDetail.html' + xyvs, //收支出详情
            TransferAndWithdrawDetail: 'yiqiji/Detail/TransferAndWithdrawDetail/TransferAndWithdrawDetail.html' + xyvs, //转账提现详情

            //跳转到官网
            Login: '/web/login.html' + xyvs,
            Register: '/web/login.html' + xyvs
        };

        // 共用Toast的promise
        var sharedToastTimeoutPromise = undefined
        var sharedToastContent = '未指定内容'

        var PageService = {}

        // 全屏对话框内容的Url
        PageService.bigDialogUrl = ''

        // ng-view内容
        PageService.activeNgViewPageName = ''

        // 全屏对话框默认是关闭的
        PageService.isBigDialogShow = false
        // 共用Toast默认是隐藏的
        PageService.isSharedToastShow = false
        // 共用确认对话框默认是隐藏的
        PageService.isConfirmDialogShow = false

        // 弹窗存储列表 pageService里统一实现弹窗这个功能
        PageService.dialogList = []
        // 对话框的标识
        PageService.dialogIdentifier = 0

        // 对话框类
        function DialogInfoClass(pageName, dialogStyle) {

            this.url = undefined
            this.id = undefined
            this.dialogStyle = ""

            // 如果传入了pageName, 去查找pageName对应的url属性
            if (pageName) {
                this.url = ConfigService.srcLocation + dialogPageNameToUrl[pageName];
                // 如果没有传入pageName把url设置为预先准备的empty.html?xyvs=2.0.1
            } else {
                this.url = ConfigService.srcLocation + 'Others/empty.html' + xyvs;
            }

            // 如果传入了dialogStyle,保存之
            if (dialogStyle) {
                this.dialogStyle = dialogStyle;
            }

            // 设定该对话框唯一标识
            this.id = ++PageService.dialogIdentifier;
        }

        //新建弹窗,并返回该弹窗id
        PageService.showDialog = function (pageName, dialogStyle) {
            // $log.debug('showDialog->pageName',pageName)
            var tmpDialog = new DialogInfoClass(pageName, dialogStyle);
            PageService.dialogList.push(tmpDialog);
            return tmpDialog.id;
        };
        // 关闭对话框,有id关闭id对应，没id关闭最新创建的
        PageService.closeDialog = function (id) {
            // 如果有id参数则移除id相等的那个
            if (id) {
                if (id > 0) {
                    for (var i = this.dialogList.length - 1; i >= 0; i--) {
                        if (this.dialogList[i].id == id) {
                            this.dialogList.splice(i, 1);
                        }
                    }
                } else {
                    this.dialogList = [];
                }
                // 否则移除最顶一个
            } else {
                this.dialogList.pop();
            }

            --PageService.dialogIdentifier;
        }


        // 显示共用Toast并延时自动隐藏
        PageService.showSharedToast = function (content, duration) {
            // 如果之前已经在展示则取消之前的定时关闭
            if (PageService.isSharedToastShow) {
                $timeout.cancel(sharedToastTimeoutPromise);
            }
            // 展示持续时间
            var dTime = 1100
            //如果传入了时间则使用传入时间
            if (duration) {
                dTime = duration
            }
            //展示内容
            PageService.sharedToastContent = content
            //展示
            PageService.isSharedToastShow = true;
            //定时关闭
            sharedToastTimeoutPromise = $timeout(function () {
                PageService.isSharedToastShow = false;
            }, dTime)
        }


        PageService.toastAgent = {};
        PageService.toastAgent.showToastByEC = function (ec) {
            var content = ConfigService;
        };

        //改变ng-view页面(在XY.js里配置路由)
        PageService.setNgViewPage = function (ngViewPageName) {
            // $log.log('setNgViewPage', ngViewPageName)
            $window.location.href = '#/' + ngViewPageName;
            this.activeNgViewPageName = ngViewPageName;
        };

        // 设定回调
        // @param callbackName 回调名
        // @param callback     函数引用
        PageService.setCallback = function (callbackName, callback) {
            callbackList[callbackName] = callback;
        };

        //通过回调名获取回调
        PageService.getCallback = function (callbackName) {
            if (callbackList[callbackName]) {
                return callbackList[callbackName];
            } else {
                $log.error('PageService.getCallback: callback not found! ', callbackName);
            }
        }

        PageService.getActivePages = function () {
            return activePages;
        }

        /**
         * 显示共用确认对话框
         *
         * @param content 提示内容
         * @param btns 按钮文字数组
         * @param callback0 按第一个按钮的回调
         * @param callback1 按第二个按钮的回调
         * @param callback2 按第三个按钮的回调
         */
        PageService.showConfirmDialog = function (content, btns, callback0, callback1, callback2, isHideHotKeyTip, isShowCloseBtn) {
            PageService.isConfirmDialogShow = true;
            $timeout(function () {
                document.getElementById('confirm-dialog').focus();
            });

            PageService.confirmDialog = {};
            PageService.confirmDialog.btns = [];
            PageService.confirmDialog.content = content;
            PageService.confirmDialog.callback0 = callback0;
            PageService.confirmDialog.callback1 = callback1;
            PageService.confirmDialog.callback2 = callback2;

            if (content == '确定删除往来单位?') {
                PageService.confirmDialog.isTipsShow = true;
            } else {
                PageService.confirmDialog.isTipsShow = false;
            }

            if (btns) {
                if (btns.length > 0) {
                    PageService.confirmDialog.btns = btns;
                    if (PageService.confirmDialog.btns[0]) {
                        if (isHideHotKeyTip) {
                            PageService.confirmDialog.btns[0] = PageService.confirmDialog.btns[0];
                        } else {
                            PageService.confirmDialog.btns[0] = PageService.confirmDialog.btns[0] + '[ENTER]';
                            //    PageService.confirmDialog.btns[0].setAttribute("backgroundColor", "#e3613e");
                        }
                    }
                    if (PageService.confirmDialog.btns[1]) {
                        if (isHideHotKeyTip) {
                            PageService.confirmDialog.btns[1] = PageService.confirmDialog.btns[1];
                        } else {
                            PageService.confirmDialog.btns[1] = PageService.confirmDialog.btns[1] + '[F2]';
                            //    PageService.confirmDialog.btns[1].style.backgroundColor = '#e3613e';
                        }
                    }
                    if (PageService.confirmDialog.btns[2]) {
                        if (isHideHotKeyTip) {
                            PageService.confirmDialog.btns[2] = PageService.confirmDialog.btns[2];
                        } else {
                            PageService.confirmDialog.btns[2] = PageService.confirmDialog.btns[2] + '[ESC]';
                        }
                    }
                } else {
                    if (isHideHotKeyTip) {
                        PageService.confirmDialog.btns[0] = '确定';
                        PageService.confirmDialog.btns[1] = '取消';
                    } else {
                        PageService.confirmDialog.btns[0] = '确定[ENTER]';
                        PageService.confirmDialog.btns[1] = '取消[ESC]';
                    }
                }
            } else {
                if (isHideHotKeyTip) {
                    PageService.confirmDialog.btns[0] = '确定';
                } else {
                    PageService.confirmDialog.btns[0] = '确定[ENTER]';
                }
            }

            if (PageService.confirmDialog.btns.length == 3) {
                PageService.confirmDialog.isWider = true;
            }
            if (PageService.confirmDialog.content.length > 300) {
                PageService.confirmDialog.isBigger = true;
            }
            if (isShowCloseBtn) {
                PageService.confirmDialog.isShowCloseBtn = true;
            } else {
                PageService.confirmDialog.isShowCloseBtn = false;
            }

            PageService.confirmDialog.click = function (index) {
                PageService.isConfirmDialogShow = false;
                switch (index) {
                    case 0:
                        if (callback0) {
                            callback0();
                        }
                        break;
                    case 1:
                        if (callback1) {
                            callback1();
                        }
                        break;
                    case 2:
                        if (callback2) {
                            callback2();
                        }
                        break;
                }
            };
        }

        PageService.closeConfirmDialog = function () {
            PageService.isConfirmDialogShow = false;
        }

        PageService.showHistoryDialog = function (history) {
            if (typeof history != 'object') {
                PageService.showSharedToast('记录格式错误！')
                return
            }
            PageService.isHistoryDialogShow = true;
            PageService.historyDialog = {};
            PageService.historyDialog.history = history;
            PageService.historyDialog.click = function () {
                PageService.isHistoryDialogShow = false;
            };
        };
        //跳转到主页
        PageService.showIndexPage = function (apiName) {
            window.location.href = domainSkin + dialogPageNameToUrl[apiName]
        };
        return PageService
    }
])