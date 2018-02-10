// 多选框组件类
'use strict';

angular.module('XY').factory('CheckboxWidgetClass', ['EventService', '$log', 'PageService', 'ClassService',
    function(EventService, $log, PageService, ClassService) {

        var CheckboxWidgetClass = {};

        function CheckboxWidget() {
            this.class_id = 'CheckboxWidget',
                this.flag_none = true, // 一个都没选中
                this.option = [],
                this.filterData = [],
                this.selectedId = 0;
                // 选中项目变化时的回调
                this.filterChangeCallback = undefined;

            this.CheckboxWidgetCtor = function(callback) {
                this.option = [];
                this.flag_none = true;
                this.filterChangeCallback = callback;
            }

            // 设定选项
            this.setOption = function(optionArr) {
                this.option = [];
                for (var key in optionArr) {
                    var tmp = {
                        flag_chosen: false, // 默认是未选中状态
                        optionName: optionArr[key].optionName,
                        id: optionArr[key].id,
                    };
                    this.option.push(tmp);
                }
                this.flag_none = true;
                this.generateFilterData();
            }

            // 只允许单选
            this.selectSingle = function(optionItem) {
                this.selectedId = optionItem.id;
                for (var key in this.option) {
                    if (optionItem.id == this.option[key].id) {
                        optionItem.flag_chosen = !optionItem.flag_chosen;
                    } else {
                        this.option[key].flag_chosen = false;
                    }
                }

                // 检查是不是一个都没有选中
                var tmp_flag_none = true;
                for (var key in this.option) {
                    if (this.option[key].flag_chosen) {
                        tmp_flag_none = false;
                    }
                }
                this.flag_none = tmp_flag_none;
                this.generateFilterData();
                this.filterChangeCallback();
            }

            // 通过选项的引用f选中某个选项
            this.select = function(optionItem) {
                optionItem.flag_chosen = !optionItem.flag_chosen;
                // 检查是不是一个都没有选中
                var tmp_flag_none = true;
                for (var key in this.option) {
                    if (this.option[key].flag_chosen) {
                        tmp_flag_none = false;
                    }
                }
                this.flag_none = tmp_flag_none;
                this.generateFilterData();
                this.filterChangeCallback();
            }

            // 直接通过选项ID选中选项
            this.selectExclusiveById = function(id) {
                for (var key in this.option) {
                    if (this.option[key].id == id) {
                        this.option[key].flag_chosen = true;
                    } else {
                        this.option[key].flag_chosen = false;
                    }
                }
                // 检查是不是一个都没有选中
                var tmp_flag_none = true;
                for (var key in this.option) {
                    if (this.option[key].flag_chosen) {
                        tmp_flag_none = false;
                    }
                }
                this.flag_none = tmp_flag_none;
                this.generateFilterData();
                this.filterChangeCallback();
            }

            // 选中全部
            this.selectAll = function(arg) {
                this.selectedId = 0;
                for (var key in this.option) {
                    this.option[key].flag_chosen = false;
                }
                this.flag_none = true;
                this.generateFilterData(arg);
                this.filterChangeCallback();
            }

            this.generateFilterData = function(arg) {
                var tmp_filter_data = [];
                for (var key in this.option) {
                    if (this.option[key].flag_chosen) {
                        tmp_filter_data.push(this.option[key].id);
                        if(this.option[key].id==91) tmp_filter_data.push(92);
                    }
                }
                if(arg==1){
                    for (var key in this.option){
                        tmp_filter_data.push(this.option[key].id);
                        if(this.option[key].id==91) tmp_filter_data.push(92);
                    }
                }
                this.filterData = tmp_filter_data;
            }
        }

        // 传入当选定项目变化时的回调
        CheckboxWidgetClass.newCheckboxWidget = function(filterChangeCallback) {
            var out = new CheckboxWidget();
            out.CheckboxWidgetCtor(filterChangeCallback);
            return out;
        }

        // 实验
        // var t = CheckboxWidgetClass.newCheckboxWidget();
        // t.setOption([{optionName:'AAA',id:123},{optionName:'BBB',id:456}]);


        // t.select(t.option[0]);


        // t.select(t.option[1]);

        // t.select(t.option[0]);
        // t.select(t.option[1]);

        // $log.log(t);

        return CheckboxWidgetClass;
    }
]);
