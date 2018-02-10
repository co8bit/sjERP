
// * 83.提现单

'use strict';

angular.module('XY').factory('WithdrawClass', ['EventService', '$log', 'PageService',
    function(EventService, $log, PageService) {
		var WithdrawClass = {}

		//提现原型
		function Withdraw (){
			this.WithdrawCtor = function (){
				this._class = 83; //单据类型
				this.remark = "";//备注
				this.cartAgent = newCartAgent();//新建购物车
				this.income = 0;
			}
			this.fillOrderInfo = function (orderInfo){
				this.remark = orderInfo.remark;
				this.income = orderInfo.income;
				this.fid = orderInfo.fid;
				this.reg_time = orderInfo.reg_time;
				this.cartAgent.fillCart(orderInfo.cart.data)
			}
		}

		//购物车
		function CartAgent(){
			this.cartAgentCtor = function (){
                //id
				this.id = 0;
				//购物车条目列表
                this.cartItemList = [];
                //正在编辑的项目
                this.editingCartItem = '';
                //添加一个空的项
                this.addCartItem();
			}

			this.fillCart = function(data){
				var i = 0;
				for (var v of data) {
					if (i > 0) {
						this.addCartItem();
					}
					this.editingCartItem.fillItem(v);
					i++;
				}
			}
			/**
			 * 添加一个条目
			 */
			this.addCartItem = function(){
				// 正在添加的和正在修改的条目
				this.editingCartItem = newCartItem();
				this.editingCartItem.setId(this.id);
				this.cartItemList.push(this.editingCartItem);
				this.id++;
			}
			/**
			 * 编辑选中条目
			 */
			this.setEidtItem = function (item){
				if (item.id != this.editingCartItem.id) {
					this.hiseList(this.editingCartItem);
					this.editingCartItem = item;
				}

			}
			/**
			 * 隐藏下拉列表
			 * 判断
			 */
			this.hiseList = function (index){
				switch (index){
				case 1:
					this.editingCartItem.account_name_show = true;
					this.editingCartItem.account_source_type_show = false;
					break;
				case 2:
					this.editingCartItem.account_name_show = false;
					this.editingCartItem.account_source_type_show = true;
					break;
				default:
					this.editingCartItem.account_name_show = false;
					this.editingCartItem.account_source_type_show = false;
					break;
				}
			}
			/**
			 * 删除选中条目
			 * index 传入的序号
			 * 判断	if 购物车只有一个，删除后再新建一个空的项
			 * 		else 判断当前编辑项是否为删除项
			 */
			this.deleteCartItem = function (index){
				if(this.cartItemList.length == 1){
					this.cartItemList.splice(0,1);	//删除一个
					this.editingCartItem = ''; 		//正在编辑为空
					this.addCartItem();				//添加一个空项
				}else{
					if (this.cartItemList[index] == this.editingCartItem){ //是否是编辑和删除是用一个
						this.cartItemList.splice(index,1);	//删除选中项
						this.editingCartItem = this.cartItemList[this.cartItemList.length-1]; //选取最后一个为编辑项
						this.editingCartItem.isEdit = true;	//选取最后一个为编辑项
					}else{
						this.cartItemList.splice(index,1);	//删除选中项
					}
				}
			}


		}

		//购物车   编辑中的item
		function cartItem(){
			//初始化购物车条目
			this.initCartItem = function () {
				this.id = '';
				this.account_creator = '';//开户人
				this.account_name = '';//账户简称
				this.account_name_show = false;//账户简称选择列表是否显示
				this.account_source_type = ''; //账户类型
				this.account_source_type_show = false; //账户类型选择列表是否显示
				this.account_type = ''//  筛选用 不用传给后台
				this.account_balance = 0;//账户结余

				this.account_operate = {
					account_id : '',//账户ID
					cost: 0,//金额
					account_number: '',//账号
				}
				this.accountList = [];// 账户列表候选 不用传给后台
			}
			this.fillItem = function (data){
				this.account_creator = data.account_creator;//开户人
				this.account_name = data.account_name;//账户简称
				this.account_source_type = data.account_source_type; //账户类型
				this.account_type = data.account_type;//  筛选用 不用传给后台
				this.account_balance = data.account_balance;//账户结余
				this.account_operate = {
					account_id : data.account_operate.account_id,//账户ID
					cost: data.account_operate.cost,//金额
					account_number: data.account_operate.account_number,//账号
				}
			}
			this.setId = function(id){
				this.id = id;
			}
		}

		//新建单据
		WithdrawClass.newWithdraw = function (_class){
			var out = new Withdraw();
			out.WithdrawCtor();
			return out;
		}

		//new 一个购物车
		function newCartAgent () {
			var out = new CartAgent();
			out.cartAgentCtor();
			return out;
		}

		//new 一个购物车条目
		function newCartItem () {
			var out = new cartItem();
			out.initCartItem();
			return out;
		}

		return WithdrawClass;

    }
])