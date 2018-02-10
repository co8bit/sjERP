'use strict'

xy.factory('CreateOrEditCatModel', ['$log', 'EventService', 'PageService',
    function($log, EventService, PageService) {
		var model = {};
		model.isEdit = false;//是否编辑模式
		model.catInfo = {
            // 优先显示,'1'=优先,'2'=正常
            sku_index : 2,
            // 是否启用,'1'=启用,='0'不启用
            status : 1,
		}
		var init = function (arg){
			if(arg){
				model.catInfo.sku_id = arg.sku_id;
	            // 类别名
	            model.catInfo.spec_name = arg.spec_name;
	            // 优先显示,'1'=优先,'2'=正常
	            model.catInfo.sku_index = arg.sku_index;
	            // 是否启用,'1'=启用,='0'不启用
	            model.catInfo.status = arg.status;
			}else{
	            // 类别名
            	model.catInfo.spec_name = '';
	            // 优先显示,'1'=优先,'2'=正常
	            model.catInfo.sku_index = 2;
	            // 是否启用,'1'=启用,='0'不启用
	            model.catInfo.status = 1;
			}
			model.spec_name = '';//是否有传入spec_name;
		}

		EventService.on(EventService.ev.START_CREATE_CAT, function(event,arg){
			//有arg 则默认spec_name
			model.isEdit = false;
			init();
			if(arg){
				model.spec_name = arg;
			}
			model.dialogId = PageService.showDialog('CreateOrEditCat');
		})

		EventService.on(EventService.ev.START_EDIT_CAT, function(event,arg){
			model.isEdit = true;
			init(arg);
			model.dialogId = PageService.showDialog('CreateOrEditCat');
		})

		return model;
    }
])