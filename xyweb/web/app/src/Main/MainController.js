'use strict'

xy.controller('MainController', ['$scope', '$log', '$timeout', 'UserService', 'PageService', 'NetworkService', 'NumPadClassService', 'OrderService', 'EventService', 'BootStrapService', 'ConfigService',
	'StockService', 'IncomeAndExpenseModel', '$parse', 'SalesOrderModel', 'CstmrService', '$filter', 'ReceiptAndPaymentModel', 'CreateEditCatModel', 'OrderDetailsModel',
	'FinanceService', 'StockTakingModel', 'FinancialDocumentDetailsModel', 'CstmrModel', 'ManageCstmrModel', 'ClassService', 'CstmrClass', 'ManagementModel', 'ManageUserModel', 'CheckboxWidgetClass', 'QueryService',
	'MiscService', 'CstmrDetailsModel', 'QueryClass', 'ViewDraftModel', 'ViewOrderModel', 'CreateEditSPUModel', 'SearchModel', 'DashboardModel', 'SummarySheetService', 'SummarySheetModel',
	 '$window', 'LocalTableClass', 'ViewStockModel', 'ManageGoodModel', 'ManageCatModel', 'ManageParkAddressModel', 'LockService', 'ExpenseCenterModel',
	'AccountPayModel', 'StatementModel', 'AuthGroupService', 'ModifyIncomeOrExpenseModel', 'SalesSummarySheetModel', 'NewSalesOrderModel', 'WarehouseModel', 'CreateEditWarehosueModel', 'StatementWechatModel',
	'SkuDetailModel', 'PrintSettingsModel', 'AccountSettingModel', 'CorporateSectorModel', 'QueryCatClass', 'IncomeOrExpenseClass', 'WithdrawClass', 'TransferAccountsModel',
	'IncomeOrExpenseModel', 'incomeAndExpenseModel', 'ExpenseAndIncomeSummarySheetModel', 'DraftModel', 'WithdrawModel', 'CreateOrEditCatModel', 'ReceiptFillModle',
	'IncomeAndExpendDetailModel', 'CreateEditAccountModel','FaceRecognitionModel','FaceDetailModel','CameraModel', 'TransferAndWithdrawDetail','StockDetailModel','RequisitionModel',
	function($scope, $log, $timeout, UserService, PageService, NetworkService, NumPadClassService, OrderService, EventService, BootStrapService, ConfigService,
		StockService, IncomeAndExpenseModel, $parse, SalesOrderModel, CstmrService, $filter, ReceiptAndPaymentModel, CreateEditCatModel, OrderDetailsModel,
		FinanceService, StockTakingModel, FinancialDocumentDetailsModel, CstmrModel, ManageCstmrModel, ClassService, CstmrClass, ManagementModel, ManageUserModel, CheckboxWidgetClass, QueryService,
		MiscService, CstmrDetailsModel, QueryClass, ViewDraftModel, ViewOrderModel, CreateEditSPUModel, SearchModel, DashboardModel, SummarySheetService, SummarySheetModel,$window, LocalTableClass, ViewStockModel, ManageGoodModel, ManageCatModel,
		ManageParkAddressModel, LockService, ExpenseCenterModel, AccountPayModel, StatementModel, AuthGroupService, SalesSummarySheetModel, NewSalesOrderModel, WarehouseModel, CreateEditWarehosueModel, StatementWechatModel,
		SkuDetailModel, PrintSettingsModel, AccountSettingModel, CorporateSectorModel, QueryCatClass, IncomeOrExpenseClass, WithdrawClass, TransferAccountsModel,
		IncomeOrExpenseModel, incomeAndExpenseModel, ExpenseAndIncomeSummarySheetModel, DraftModel, WithdrawModel, CreateOrEditCatModel, ReceiptFillModle,
		IncomeAndExpendDetailModel, CreateEditAccountModel, FaceRecognitionModel,FaceDetailModel,CameraModel,TransferAndWithdrawDetail,StockDetailModel,RequisitionModel
	) {

		// EventService.emit(EventService.ev.START_ACCOUNT_PAY);
		// EventService.emit(EventService.ev.START_EXPENSE_CENTER);

		// QueryService.query.dashboard()

		// FinanceService.queryOneDocument(17,function(){})

		// OrderService.queryOneOrder(9)

		// StockService.getStockTaking(3)

		// SummarySheetService.everydaySummarySheet.get() // 测试今日报表接口

		// UserService.user.getList(1)
		// EventService.emit(EventService.ev.START_VIEW_DASHBOARD)
		// $log.debug('Event Send')
		// EventService.emit(EventService.ev.START_MANAGE_PARK_ADDRESS)                      // 管理停车位置
		// EventService.emit(EventService.ev.START_VIEW_SHOPINFO)                            // 店铺设置
		// EventService.emit(EventService.ev.START_VIEW_DRAFT,UserService.getLoginStatus.id) // 查看草稿
		// EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER)                         // 查看今日单据
		// PageService.setNgViewPage('ManageCstmr')

		// EventService.emit(EventService.ev.FINANCE_VIEW_DETAILS,1)
		// EventService.emit(EventService.ev.START_CREATE_INCOME)// 收入单
		// EventService.emit(EventService.ev.START_CREATE_RECEIPT)
		// EventService.emit(EventService.ev.START_CREATE_PAYMENT)

		// EventService.emit(EventService.ev.START_CREATE_ORDER,1)      // 开销售单
		// EventService.emit(EventService.ev.ORDER_VIEW_DETAILS, 1)     // 销售单详情
		// EventService.emit(EventService.ev.CONTINUE_CREATE_ORDER,50)  // 从草稿继续开销售单
		// EventService.emit(EventService.ev.CONTINUE_CREATE_INCOME,17) // 从草稿继续开收入单

		// EventService.emit(EventService.ev.CONTINUE_CREATE_RECEIPT,21)// 从草稿继续开收款单

		// EventService.emit(EventService.ev.START_STOCK_TAKING)
		// EventService.emit(EventService.ev.START_CREATE_COMPANY)
		// EventService.emit(EventService.ev.COMPANY_EDIT_CONTACT,2)    // 查看和编辑往来单位

		// EventService.emit(EventService.ev.STOCK_TAKING_VIEW_DETAILS, 1) // 盘点单详情
		// EventService.emit(EventService.ev.CONTINUE_STOCK_TAKING,12)

		// EventService.emit(EventService.ev.START_VIEW_TODAY_SUMMARY_SHEET) // 查看实时数据

		// CstmrService.company.queryList(1)

		// EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,item.cid)

		// var t = new Date()
		// $log.debug('current time: ',t.getTime(),t.getHours())

		// $log.log(new Date().getFullYear().toString() + '/' + (new Date().getMonth()+1).toString() + '/'+ new Date().getDate().toString())

		// 把一堆服务映射到$scope里
		$scope.UserService = UserService;
		$scope.PageService = PageService;
		$scope.ConfigService = ConfigService;
		$scope.EventService = EventService;
		$scope.$log = $log;
		$scope.ViewOrderModel = ViewOrderModel; // 为了高亮今日单据或所有单据，得到这里来查看页面状态
		$scope.SummarySheetModel = SummarySheetModel;
		// $scope.LoginModel = LoginModel;

		$scope.SalesSummarySheetModel = SalesSummarySheetModel;

		$scope.checkTimeModeBoxIsShow = false;

		// 默认隐藏加号菜单
		$scope.isPlusMenuShow = false;

		//////////////////// 隐藏设置菜单
		$scope.isFeedBackMenuShow = false;

		// 隐藏设置菜单
		$scope.isSettingMenuShow = false;
		// 隐藏用户菜单
		$scope.isUserMenuShow = false;

		$scope.isSearchInputFocus = false;

		//默认不显示显示新手引导
		$scope.novice_boot = false;
		//是否显示更新提示
		$scope.upDateTipIsShow = false;
		$scope.nowVersion = 2000000; //手动修改当前版本 版本号3位进如1.1.1版本就是001 001 001;
		// 根据登录状态显示不同页面
		$scope.loginStatus = UserService.getLoginStatus();
		$scope.isFree = UserService.isFree;
		$scope.isExperience = UserService.isExperience;
		$scope.rpg = UserService.getLoginStatus().rpg;
		// $log.log('rpg',UserService.getLoginStatus().rpg);
		$scope.xingYunTitle = '进销存';//网页头部显示logo
		/**
		 * 获取模块显示状态
		 */
		function getModuleIsShow() {
			$scope.isAccountPayShow = AuthGroupService.getIsShow(AuthGroupService.module.AccountPay);
			$scope.isFeedBackShow = AuthGroupService.getIsShow(AuthGroupService.module.FeedBack);
			$scope.isShopInfoShow = AuthGroupService.getIsShow(AuthGroupService.module.ShopInfo);
			$scope.isManageUserShow = AuthGroupService.getIsShow(AuthGroupService.module.ManageUser);
			$scope.isManageParkAddressShow = AuthGroupService.getIsShow(AuthGroupService.module.ManageParkAddress);
			$scope.isPrintSettingsShow = AuthGroupService.getIsShow(AuthGroupService.module.PrintSettings);
			$scope.isSystemSettingsShow = AuthGroupService.getIsShow(AuthGroupService.module.SystemSettings);
			$scope.isAboutUsShow = AuthGroupService.getIsShow(AuthGroupService.module.AboutUs);
			$scope.isUserInfoShow = AuthGroupService.getIsShow(AuthGroupService.module.UserInfo);
			$scope.isSalesOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.SalesOrder);
			$scope.isPurchaseOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.PurchaseOrder);
			$scope.isStockTakingShow = AuthGroupService.getIsShow(AuthGroupService.module.StockTaking);
			$scope.isReceiptAndPaymentShow = AuthGroupService.getIsShow(AuthGroupService.module.ReceiptAndPayment);
			$scope.isIncomeAndExpenseShow = AuthGroupService.getIsShow(AuthGroupService.module.IncomeAndExpense);
			$scope.isCreateSPUShow = AuthGroupService.getIsShow(AuthGroupService.module.CreateSPU);
			$scope.isCreateCstmrShow = AuthGroupService.getIsShow(AuthGroupService.module.CreateCstmr);
			$scope.isCreateEditCatShow = AuthGroupService.getIsShow(AuthGroupService.module.CreateEditCat);
			$scope.isDashboardShow = AuthGroupService.getIsShow(AuthGroupService.module.Dashboard);
			$scope.isViewDraftShow = AuthGroupService.getIsShow(AuthGroupService.module.ViewDraft);
			$scope.isViewOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.ViewOrder);
			$scope.isManageCstmrShow = AuthGroupService.getIsShow(AuthGroupService.module.ManageCstmr);
			$scope.isViewStockShow = AuthGroupService.getIsShow(AuthGroupService.module.ViewStock);
			$scope.isManageGoodAndCatShow = AuthGroupService.getIsShow(AuthGroupService.module.ManageGoodAndCat);
			$scope.isTodaySummarySheetShow = AuthGroupService.getIsShow(AuthGroupService.module.TodaySummarySheet);
			$scope.isEverydaySummarySheetShow = AuthGroupService.getIsShow(AuthGroupService.module.EverydaySummarySheet);
			$scope.isSalesSummarySheetShow = AuthGroupService.getIsShow(AuthGroupService.module.SalesSummarySheetShow);
			$scope.isRequisitionShow = AuthGroupService.getIsShow(AuthGroupService.module.Requisition);
			// 这里添加采购汇总表是否显示标志位
			$scope.isPurchaseSummarySheetShow = AuthGroupService.getIsShow(AuthGroupService.module.PurchaseSummarySheet);
			//易企记显示权限
			$scope.IsYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);//易企记
			$scope.IncomeStatistics = AuthGroupService.getIsShow(AuthGroupService.module.IncomeStatistics);// 支出
			$scope.ExpenditureStatistics = AuthGroupService.getIsShow(AuthGroupService.module.ExpenditureStatistics);// 收入
			$scope.TransferAccounts = AuthGroupService.getIsShow(AuthGroupService.module.TransferAccounts);// 转账
			$scope.Withdrawals = AuthGroupService.getIsShow(AuthGroupService.module.Withdrawals);// 提现
			$scope.ReceiptFill = AuthGroupService.getIsShow(AuthGroupService.module.ReceiptFill);// 填补
			$scope.accountSeting = AuthGroupService.getIsShow(AuthGroupService.module.accountSeting);// 账户设置
			$scope.corporateSector = AuthGroupService.getIsShow(AuthGroupService.module.corporateSector);// 部门设置
			$scope.IncomeAndExpenseSummarySheet = AuthGroupService.getIsShow(AuthGroupService.module.IncomeAndExpenseSummarySheet);// 收入支出统计
			if($scope.IsYqjShow){
				$scope.xingYunTitle = '易企记';
			}else{
				$scope.xingYunTitle = '进销存';
			}
		}

		$scope.$watch('loginStatus.status', function() {
			if($scope.loginStatus.status > 0) {
				UserService.getUserAccountInfo(function(data) {
					$scope.userAccountInfo = data;
				});
				//判断是否体验账号，否再判断是否第一次登陆，undefined为第一次登陆,显示新手引导
				NetworkService.request('getOptionArray', {}, function(data) {
					$scope.option_array = data.data.option_array;
					if(!$scope.IsYqjShow){
						if(data.data.option_array == undefined) {
							$scope.novice_boot = true;
							return
						}
						//是否显示新手引导
						if(data.data.option_array[0] == 0) {
							$scope.novice_boot = true;
							return;//显示新手页的时候不要显示提示升级
						}

						if(data.data.option_array[50] != $scope.nowVersion) {
							$scope.upDateTipIsShow = true;
						}
					}
				});

				if(UserService.memberInfo) {
					setTimeout(function() {
						if(UserService.memberInfo.type > 0) {
							EventService.emit(EventService.ev.START_ACCOUNT_PAY, UserService.memberInfo); // 跳转到支付页面
							UserService.clearMemberInfo();
						}
					}, 200);

					// if (UserService.memberInfo.type == 0) {
					//     UserService.logout(function () {
					//         LoginModel.status = 4;
					//     });
					// }
				} else {
					EventService.emit(EventService.ev.START_VIEW_DASHBOARD);//打开主页
				}
				getModuleIsShow();

				// 获取当前登录的账户的真实姓名
				UserService.user.getList(2).then(function() {
					$scope.userInfo = UserService.findUserById(UserService.getLoginStatus().id);
					if($scope.loginStatus.rpg != $scope.userInfo.rpg) {
						UserService.setRpg($scope.userInfo.rpg);
					}
				});
				// 获取锁定状态
				LockService.getLockShopStatus(2);}
			// } else {
			// 	if(UserService.memberInfo && !isNaN(UserService.memberInfo.type)) {
			// 		LoginModel.status = 4;
			// 	} else {
			// 		LoginModel.status = 1;
			// 	}
			// }
		})
		$scope.$watch('userInfo.name',function (newValue){
			//获取用户名字动态修改设置菜单弹出位置和新手引导图片位置
			if (newValue) {
				//6为体验账号 每次都显示新手引导
				if($scope.userInfo.mobile == 12345678901){
					if(!$scope.upDateTipIsShow){
						$scope.novice_boot = true;
					}
				}
				setTimeout(function(){
					var len = parseInt($('#user-menu').css('width')); //登陆用户名长度
					$('#setting-menu').css('right', len);
				},100)
			}
		})
		// 获取是否要登入体验账户
		if(UserService.experience) {
			$log.debug("体验账户登录。");
			PageService.showIndexPage("Register");
			// $log.debug(LoginModel.status);
			// UserService.login('NA', '12345678901', '123456', 2, 1, '', '', '', function () {
			//     UserService.clearExperience();
			// }, function () {});
			// if ($scope.loginStatus.status > 0) {
			//     UserService.logout(function () {
			//         UserService.login('NA', '12345678901', '123456', 2, 1, '', '', '', '', function () {});
			//     });
			// } else {
			//     UserService.login('NA', '12345678901', '123456', 2, 1, '', '', '', '', function () {});
			// }
		}
		EventService.on(EventService.ev.GET_USER_INFO, function() {
			if($scope.loginStatus.status > 0) {
				UserService.user.getList(1, function() {
					$scope.userInfo = UserService.findUserById(UserService.getLoginStatus().id);
				})
			}
		})

		// 点击加号按钮
		$scope.clickPlusButton = function() {
			$scope.isPlusMenuShow = !$scope.isPlusMenuShow
		}

        ////////////////// 点击反馈框按钮
		$scope.clickFeedBackButton = function() {
			$scope.isFeedBackMenuShow = ($scope.isFeedBackMenuShow == false) ? true : false
			// $log.debug($scope.isSettingMenuShow)

		}


		$scope.feedbackContent = "";

        // $log.log('FeedBackController');

        $scope.send = function() {
        	UserService.feedback.create($scope.feedbackContent, function () {
                $scope.feedbackContent = "";
                $scope.isFeedBackMenuShow = false;
            });
        }

        var handle1 = EventService.on(EventService.ev.feedback_create_,function(event,arg){
        	if (arg > 0) {
        		$scope.feedbackContent = "";
        	}
        });

        $scope.$on('$destroy',function(){
        	handle1();
        });

		$scope.clickSummarySheet = function (){
			// if($scope.isExpendSummarySheetShow){
			// 	EventService.emit(EventService.ev.START_VIEW_EXPENSE_SUMMARY_SHEET);
			// }else{
			// 	EventService.emit(EventService.ev.START_VIEW_TODAY_SUMMARY_SHEET);
			// }
			 $scope.areaIsShow('sheet');
		}
		// 点击设置按钮
		$scope.clickSettingBurron = function() {

			$scope.isSettingMenuShow = ($scope.isSettingMenuShow == false) ? true : false
			// $log.debug($scope.isSettingMenuShow)
		}

		// 点击用户按钮
		$scope.clickUserButton = function() {
			$scope.isUserMenuShow = ($scope.isUserMenuShow == false) ? true : false
		}

		//给点击body发出事件，用于实现加号菜单展开时点击外部关闭之
		$("body").bind("touchstart click", function(e) {
			EventService.emit(EventService.ev.CLICK_BODY, e)
		});

		//当发生点击body事件，判断是否点发生在加号菜单内，如不是则关闭加号菜单
		var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function(angularEvent, domEvent) {

			$timeout(function() {

				// 如果没在加号菜单上也没再加号按钮上
				if(!($(domEvent.target).hasClass('plus-menu')) && !($(domEvent.target).hasClass('plus-button'))) {
					$scope.isPlusMenuShow = false;
				}

				// 如果没在反馈框上也没在反馈按钮上
				if(!($(domEvent.target).hasClass('fb-m')) && !($(domEvent.target).hasClass('feedback-menu-btn'))) {
					$scope.isFeedBackMenuShow = false;
				}

				// 如果没在设置菜单上也没在设置按钮上
				if(!($(domEvent.target).hasClass('setting-menu')) && !($(domEvent.target).hasClass('setting-menu-btn'))) {
					$scope.isSettingMenuShow = false;
				}
				// 如果没在用户菜单上也没在
				if(!($(domEvent.target).hasClass('user-menu')) && !($(domEvent.target).hasClass('user-menu-btn'))) {
					$scope.isUserMenuShow = false;
				}
			})
		});

		$scope.searchInputFocus = function() {
			// $log.debug('searchInputFocus')
			$scope.isSearchInputFocus = true
		};

		$scope.searchInputBlur = function() {
			// $log.debug('searchInputBlur')
			$scope.isSearchInputFocus = false
		};

		$scope.searchContent = {
			content: '',
		}

		// 搜索输入框按键
		$scope.searchKeydown = function(event) {
			// $log.debug(event)
			if(event.keyCode == 13) {
				EventService.emit(EventService.ev.SEARCH_TITLE_BAR, $scope.searchContent.content);
				$scope.searchContent.content = '';
			}
		}

		// 销毁用来关闭加号菜单的监听
		$scope.$on('$destroy', function() {
			clickHandle()
		})

		//测试按钮
		this.testButton = function() {

			// $log.log('catInfo = ',$scope.catInfo)
			// var content = "测试内容"
			// PageService.showSharedToast(content,780)
			model.dialogId = PageService.showDialog($scope.testDialogName)
			$log.log(PageService.dialogList)

		}

		$scope.gotoOfficialSite = function() {
			var url = window.location.href;
			if(url.search('xyweb') > -1) {
				window.open('/index');
			} else {
				window.open('/');
			}
		}

		// 监听全局键盘事件
		$(document).keydown(function(event) {
			// $log.log('keydown:' + event.keyCode);
			EventService.emit(EventService.ev.KEY_DOWN, event);
		});

		var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function(event, domEvent) {
			$timeout(function() {
				if(!domEvent.altKey || PageService.dialogList.length > 0) {
					return;
				}
				switch(domEvent.keyCode) {
					case 49: // 1
						console.log('exec 1');
						EventService.emit(EventService.ev.START_CREATE_ORDER, 1); // 销售开单
						break;
					case 50: // 2
						EventService.emit(EventService.ev.START_CREATE_ORDER, 2); // 销售退货
						break;
					case 51: // 3
						EventService.emit(EventService.ev.START_CREATE_ORDER, 3); // 采购开单
						break;
					case 52: // 4
						EventService.emit(EventService.ev.START_CREATE_ORDER, 4); // 采购退货
						break;
					case 53: // 5
						EventService.emit(EventService.ev.START_STOCK_TAKING); // 盘点单
						break;
					case 54: // 6
						EventService.emit(EventService.ev.START_CREATE_RECEIPT); // 收款单
						break;
					case 55: // 7
						EventService.emit(EventService.ev.START_CREATE_PAYMENT); // 付款单
						break;
					case 56: // 8
						EventService.emit(EventService.ev.START_CREATE_INCOME); // 其他收入单
						break;
					case 57: // 9
						EventService.emit(EventService.ev.START_CREATE_EXPENSE); // 支出费用单
						break;
					case 48: // 0
						EventService.emit(EventService.ev.START_CREATE_COMPANY); // 新建单位
						break;
				}
			});

			if(!PageService.isConfirmDialogShow) {
				return;
			}
			$timeout(function() {
				switch(domEvent.keyCode) {
					case 13: // 回车
						PageService.isConfirmDialogShow = false;
						if(PageService.confirmDialog.callback0) {
							PageService.confirmDialog.callback0();
						}
						break;
					case 27: // ESC
						PageService.isConfirmDialogShow = false;
						if(PageService.confirmDialog.callback2) {
							PageService.confirmDialog.callback2();
						}
						break;
					case 113: // F2
						PageService.isConfirmDialogShow = false;
						if(PageService.confirmDialog.callback1) {
							PageService.confirmDialog.callback1();
						}
						break;
				}
			});
		})

		$scope.$on('$destroy', function() {
			keydownHandle();
		})

		$scope.StockIsShow = true;
		$scope.SheetIsShow = false;
		$scope.OrderIsShow = true;
		$scope.areaIsShow = function(element) {
			switch(element) {
				case 'stock':
					if($scope.StockIsShow) {
						$scope.StockIsShow = false;
					} else {
						$scope.StockIsShow = true;
					}
					break;
				case 'order':
					if($scope.OrderIsShow) {
						$scope.OrderIsShow = false;
					} else {
						$scope.OrderIsShow = true;
					}
					break;
				case 'sheet':
					if($scope.SheetIsShow) {
						$scope.SheetIsShow = false;
					} else {
						$scope.SheetIsShow = true;
					}
					break;
				default:
					break;
			}

		}

		$scope.showTimeBox = function() {
			$scope.timeModeTime = '';
			// 初始化开单显示时间
			var time = new Date();
			var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
			var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
			$scope.timeModeTime = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
			$scope.checkTimeModeBoxIsShow = true;

			$("#datepicker").datepicker({
				onSelect: function(timeStr) {
					//开单 时间为模式时间
					$scope.timeModeTime = timeStr;
				},
				showButtonPanel: true,
			})
		}
		//关闭时间模式设置窗口
		$scope.closeTimeBox = function() {
			//关闭窗口
			$scope.checkTimeModeBoxIsShow = false;
		}
		$scope.startTimeMode = function() {
			//开单 时间为模式时间
			SalesOrderModel.timeModeTime = $scope.timeModeTime;
			//关闭窗口
			$scope.checkTimeModeBoxIsShow = false;
			//关闭提示
			$scope.timeModeTipBtn = true;
			PageService.showSharedToast('您已进入时光模式！！！')
		}
		$scope.quitTimeMode = function() {
			//确保开单时间模式的时间为空
			SalesOrderModel.timeModeTime = '';
			//关闭提示
			$scope.timeModeTipBtn = false;
		}
		//$scope.novice_boot = true; //使用后每次打开都显示新手引导
		$scope.stepIndex = 0; //初始化显示步骤为0
		$scope.finishIndex = 6;
		/**
		 * 点击下一步
		 */
		$scope.stepNext = function() {
			$scope.stepIndex++;
			if(UserService.getLoginStatus().rpg == 1) {
				$scope.finishIndex = 5;
			}

			if($scope.stepIndex == 6) {
				$scope.SheetIsShow = true;
			} else {
				$scope.SheetIsShow = false;
			}

		}
		/**
		 * 点击上一步
		 */
		$scope.preNext = function() {
			if($scope.stepIndex > 0) {
				$scope.stepIndex--;
			}
			if($scope.stepIndex == 6) {
				$scope.SheetIsShow = true;
			} else {
				$scope.SheetIsShow = false;
			}
		}
		/**
		 * 点击结束或者跳过按钮
		 */
		$scope.finish = function() {
			$scope.novice_boot = false;
			//通知服务器用户已经完成了新手引导
			if($scope.option_array == undefined) {
				$scope.option_array = [1];
			} else {
				$scope.option_array[0] = 1;
			}
			var dataToSend = {
				option_array: $scope.option_array
			}

			NetworkService.request('setOptionArray', dataToSend);
		}

		/**
		 * 更新提示
		 */
		//图片路径
		$scope.stepImgUrl = [
			'web/app/img/tipImg/tip2.0.0.png',
			'web/app/img/tipImg/tip2.0.1.png',
			'web/app/img/tipImg/tip2.0.2.png',
			'web/app/img/tipImg/tip2.0.3.png',
			'web/app/img/tipImg/tip2.0.4.png',
			'web/app/img/tipImg/tip2.0.5.png',
			'web/app/img/tipImg/tip2.0.6.png',
			'web/app/img/tipImg/tip2.0.7.png',
			'web/app/img/tipImg/tip2.0.8.png',
		]
		//初始nowStepTip
		$scope.nowStepTip = 0;

		$scope.changeTipStep = function(step) {
			$scope.nowStepTip = step;
		}

		$scope.tipStepNext = function(step) {
			//如果step == -1 就是完成 否则是下一步
             function replacer(key, value) {
                if (typeof value === 'undefined') {
                    return 0;
                }
                return value;
             }
			if(step == -1) {
		        var option_array = [];
				angular.copy($scope.option_array, option_array);
				option_array[50] = $scope.nowVersion;
		        var dataToSend = {
		            option_array: JSON.stringify(option_array,replacer),
		        }
		        NetworkService.request('setOptionArray',dataToSend)
		        $scope.upDateTipIsShow = false;
			} else {
				$scope.nowStepTip++;
			}
		}

	}
]);