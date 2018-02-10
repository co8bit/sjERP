//类服务，可以注册类和给普通对象安装方法
'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('ClassService', ['EventService', '$log', 'PageService',
    function(EventService, $log, PageService) {


        // 存储classId 到 类proto的映射
        var classIdToProto = {};


        var ClassService = {};

        // 注册一个类,把class_id到obj的映射存储到classIdToProto
        ClassService.registerClass = function(obj) {
            if (obj.class_id) {
                // 设定id到类原型的映射
                classIdToProto[obj.class_id] = obj;
            } else {
                $log.warn('in ClassService.registerClass, obj.class_id not existed! obj:', obj);
            }
        }

        // 给任意对象安装方法,通过对象属性class_id的值来判断对象类型,递归进行
        ClassService.installMethod = function(target) {

            // $log.log('installMethod start, target= ',target);
            // 先深度优先递归
            // 枚举target的属性
            for (var key in target) {
                
                // 如果是他自己的
                if (target.hasOwnProperty(key)) {

                    // 并且是对象类型
                    if (typeof target[key] == 'object') {
                        // $log.log('target,key,value=', target,key, target[key], typeof target[key]);
                        ClassService.installMethod(target[key]);
                    }
                }
            }

            // 然后处理该对象
            // 如果目标有class_id属性
            if (target.class_id) {
                // 且该class_id曾注册过
                if (classIdToProto[target.class_id]) {
                    // 给目标安装对应的方法
                    target.__proto__ = classIdToProto[target.class_id];
                    target[target.class_id + 'Ctor']();
                // 如果该class_id未注册过
                } else {
                    $log.warn('in ClassService.installMethod, class_id is not registed! target:',target);
                }
            }
        }

        // 递归移除本地变量
        ClassService.removeLocalVariable = function(target) {
            // $log.log('removeLocalVariable target:',target);

            // 枚举target的属性
            for (var key in target) {
                // 如果是他自己的
                if (target.hasOwnProperty(key)) {
                    // $log.log('removeLocalVariable key:',key);

                    // 如果双下划线开头说明是api无关变量
                    if (key.indexOf('__') == 0) {
                        // $log.log('__ √');
                        delete target[key];
                    } else {
                        // $log.log('__ ×');
                        // $log.log(typeof target[key]);
                        if (typeof target[key] == 'object') {
                            // $log.log('object √');
                            // $log.log('target,key,value=', target,key, target[key], typeof target[key]);
                            ClassService.removeLocalVariable(target[key]);
                        }
                    }
                }
            }
        }

        ClassService.getClassIdToProto = function() {
            return classIdToProto;
        }

        // 实验
        // function f1() {
        //     this.class_id = 1;
        //     this.fm1 = function(){
        //         $log.log('fm1');
        //     }
        // }

        // var fobj = new f1();

        // ClassService.registerClass(fobj);



        // function f2() {
        //     this.class_id = 2;
        //     this.f2m1 = function() {
        //         $log.log('f2m1');
        //     }
        // }
        // f2.prototype = fobj;

        // var f2obj = new f2();
        // ClassService.registerClass(f2obj);

        // var tt = {
        //     class_id : 1,
        //     a:{
        //         class_id:3,
        //     }
        // }

        // ClassService.installMethod({class_id:5});
        // ClassService.installMethod(tt);
        // $log.log(tt);

        // $log.log('====');
        // tt.fm1();
        // tt.a.f2m1();
        // $log.log('classIdToProto:',classIdToProto);


        // var t = new f2();
        // $log.log(t.class_id);
        // $log.log('test PropertyIsEnumerable',t.PropertyIsEnumerable());

        return ClassService; // or return model

    }
]);
