/**
 * 用户角色过滤器
 */

xy.filter('rpg', function () {
    return function (input) {

        var rpg = '';

        switch (input) {
            case '1':
                rpg = '创建者';
                break;
            case '2':
                rpg = '销售';
                break;
            case '3':
                rpg = '库管';
                break;
            case '4':
                rpg = '股东';
                break;
            case '7':
                rpg = '管理员';
                break;
            case '8':
                rpg = '创建者';
                break;
            case '9':
                rpg = '老板';
                break;
            case '10':
                rpg = '财务';
                break;
            case '11':
                rpg = '员工';
                break;
            case '12':
                rpg = '管理员';
                break;
        }

        return rpg;
    }
});