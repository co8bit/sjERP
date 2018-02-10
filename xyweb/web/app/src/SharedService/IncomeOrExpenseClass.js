
// * 81.收入单
// * 82.支出单

'use strict';

angular.module('XY').factory('IncomeOrExpenseClass', ['EventService', '$log', 'PageService',
    function(EventService, $log, PageService) {
		var IncomeOrExpenseClass = {}

		//收入支出单原型
		function IncomeOrExpense (){
			this.IncomeOrExpenseCtor = function (){
				this._class = ""; //单据类型
				this.cid_name = "";//收入来源
				this.cid = '';//申领人
				this.remark = "";//备注
				this.cartAgent = newCartAgent();//新建购物车
				this.income = 0;//总金额
			}
			this.setClass = function (_class){
				this._class =  _class;
			};
			this.setName = function (name){
				this.name = name;
			}
			this.company = function (company){
				this.company = company;
			}
			/**
			 * 填充单据
			 */
			this.fillOrderInfo = function (orderInfo){
				this._class = orderInfo.class;
				this.cid_name  = orderInfo.cid_name ;
				this.remark = orderInfo.remark;
				this.cartAgent.fillCart(orderInfo.cart);
				this.income = Number(orderInfo.income);
				this.cid = orderInfo.cid;
				this.fid = orderInfo.fid;//草稿单会有fid
			}
		}

		//购物车
		function CartAgent(){
			this.cartAgentCtor = function (){
				//购物车条目列表
                this.cartItemList = [];
                // 总数
                this.cartItemListLength = 0;
                //正在编辑的项目
                this.editingCartItem = '';
                //添加一个空的项
                this.addCartItem();
			}
			this.fillCart = function (cart){
				/**
				 * 填入购物车思路，通过便利一个一个填入
				 * 有记录则新建购物车项再填入
				 */
				var  i = 0;
				for (var v of cart.data) {
					if (i > 0) {
						this.addCartItem();//不是第一项就先新建一项
					}
					this.editingCartItem.fillCartItem(v);
					i++
				}
			}
			/**
			 * 添加一个条目
			 */
			this.addCartItem = function(){
				// 正在添加的和正在修改的条目
				this.editingCartItem = newCartItem();
				this.cartItemList.push(this.editingCartItem);
			}
			/**
			 * 编辑选中条目
			 */
			this.setEditCartItem = function (item){
				this.editingCartItem.catListShow = false;
				this.editingCartItem = item;

			}
			/**
			 * 隐藏下拉列表
			 */
			this.hideList = function (){
				this.editingCartItem.catListShow = false;
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
				this.sku_id = ''; //分类id
				this.spec_name = '';//分类名
				this.item_name = '';//项目名字
				this.cost_remark = '';//备注
				this.is_invoice = false;//是否有发票
				this.catListShow = false;//类型列表默认不显示
				this.total_price = 0;//总金额
				this.account_operate = {};//总金额来源列表
				this.isEdit = true;//新建条目默认处于编辑状态
			}
			this.fillCartItem = function (item){
				this.sku_id = item.sku_id; //分类id
				this.spec_name = item.spec_name;//分类名
				this.item_name = item.item_name;//项目名字
				this.cost_remark = item.cost_remark;//备注
				this.is_invoice = item.is_invoice;//是否有发票
				this.catListShow = item.catListShow;//类型列表默认不显示
				this.total_price = Number(item.total_price);//总金额
				this.account_operate = item.account_operate;//总金额来源列表
				this.isEdit = item.isEdit;//新建条目默认处于编辑状态
			}
		}

		//新建单据
		IncomeOrExpenseClass.newIncomeOrExpense = function (_class){
			var out = new IncomeOrExpense();
			out.IncomeOrExpenseCtor();
			out.setClass(_class);
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

		return IncomeOrExpenseClass;

    }
])