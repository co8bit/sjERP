/* 
	创建时间 2016.4.6
	页面状态信息类，用于存储多选项卡的激活状态
 */
'use strict';

xy.factory('PageStateInfoClass', ['$rootScope', '$log', 'StockService', 'CstmrService','ConfigService',
    function($rootScope, $log, StockService, CstmrService,ConfigService) {

        // $log.debug('PageStateInfoClass init')
        var out = {};

        //页面状态信息
        function PageStateInfo() {
        	this.PageStateInfoCtor = function() {

        		this.state = 0;
                this.totTabNum = 1;
                this.activeTab = 0;

        	}

            //设置总标签页数量
            this.setTotTabNum = function(num) {
                this.totTabNum = num
            }

        	//激活标签页
        	this.setActiceTab = function(tabNo) {
                this.activeTab = tabNo;
        	}

            // 上一步
            this.lastStep = function() {
                this.activeTab = (this.activeTab - 1 + this.totTabNum) % this.totTabNum;
            }

            // 下一步
            this.nextStep = function() {
                this.activeTab = (this.activeTab + 1) % this.totTabNum;
            }
            
        }

        //新建状态信息类
        out.newPageStateInfo = function() {
        	var out = new PageStateInfo()
        	out.PageStateInfoCtor()
        	return out
        }
               
        function init() {


        }

        init();


        return out;

    }
]);



