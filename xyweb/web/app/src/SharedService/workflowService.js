'use strict';

xy.factory('WorkflowService', ['$rootScope','$log','$window',
    function($rootScope,$log,$window) {

        var WorkflowService = {};

        function workflowProto() {

        }

        function workflow(){}
        workflow.prototype = workflow

        function workflowAgentProto() {
            this.init = function() {
                this.id = 0
                this.workflowList = {}
                this.activeWorkflowID = 0
            }

            this.switchToWorkflow = function(id) {
                // if (id == undefined)
            }

            this.getActiveWorkflow = function() {
                return 1
            }
        }

        function workflowAgent() {}
        workflowAgent.prototype = workflowAgentProto

        var wfaIns = new workflowAgent()
        wfaIns.init() 


        WorkflowService.switchToWorkflow = function(id) {
            wfaIns.switchToWorkflow(id)
        }

        WorkflowService.getActiveWorkflow = function(){
            return wfaIns.getActiveWorkflow()
        }



        //设置当前页面（实现页面跳转），同时改变标签页, 标签页暂时停用
        WorkflowService.setCurrentPage = function(pageName) {

            $window.location.href = '#/' + pageName;

            return currentPageNum;


        }

        WorkflowService.getActivePages = function() {
            return activePages;
        }

        function init() {

        }

        init()


        return WorkflowService;
    }
]);
