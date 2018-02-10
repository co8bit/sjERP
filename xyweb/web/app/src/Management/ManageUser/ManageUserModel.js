//xxxxModel or Service or Class
'use strict';

angular.module('XY').factory('ManageUserModel', ['EventService', '$log', 'PageService', 'UserService', 'LockService', 'NetworkService',
    function(EventService, $log, PageService, UserService, LockService, NetworkService) {

        var model = {};
        model.departmentList = [];//部门列表
        model.userObj = undefined;
        model.dialogId = undefined;
        model.userList = undefined;

        EventService.on(EventService.ev.START_CREATE_EDIT_USER, function(event, arg) {
            LockService.getLockShopStatus(0, function () {
                // arg即是uid
                model.uid = arg;
                // uid说明是编辑
                if (model.uid) {
                    model.isEdit = true;
                    model.userObj = UserService.findUserById(model.uid);
                    // 没有是新建
                } else {
                    model.isEdit = false;
                    model.userObj = {
                        username: "",
                        password: "",
                        rpg: '',
                        name: "",
                        mobile: "",
                        email: "",
                        qq: "",
                        status: '1',
                    };
                }
                model.getDepartment(function(){
                    model.dialogId = PageService.showDialog('CreateEditUser');
                })
            });
        });

        // 新建成功关对话框
        EventService.on(EventService.ev.USER_REGISTER_SUCCESS, function(event, arg) {
            if (arg > 0) PageService.closeDialog(model.dialogId);
        });

        // 编辑成功管对话框
        EventService.on(EventService.ev.user_editUserInfo,function(event,arg){
        	if (arg > 0) PageService.closeDialog();
        });
        //成员管理列表
        EventService.on(EventService.ev.START_MANAGE_USER, function (event,arg) {
            if (arg) {
                model.isYQJ = true;
            }
            UserService.user.getList(1);
            model.userList = UserService.getUserList();
            PageService.setNgViewPage('ManageUser');
        })

        model.getDepartment = function(callback){
            NetworkService.request('getDepartment',{},function (data){
                angular.copy(data.data,model.departmentList)
                if (callback) {
                    callback();
                }
            })
        }
        return model;
    }

]);
