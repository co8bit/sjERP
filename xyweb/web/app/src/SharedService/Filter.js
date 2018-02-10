//
//
//二阶段过滤器
// param  [] input      输入信息,对象数组
// param  {} predicate  过滤条件,如下所示,oneMatch中一个字段匹配即可，allMatch中必须所有字段匹配，oneMatch与allMatch是与的关系
// return []            返回过滤后的数组

// predicate:{
//     oneMatch:{
//         name:'a',
//         age:10,
//     },
//     allMatch:{
//         telephone:'12345',
//         address:'12345',
//     },
// }

xy.filter('twoPhaseFilter', function($log, $filter) {
    return function(input, predicate) {
        //$log.debug('in filter , input,predicate = ', input, predicate);

        var out = [];
        var flag;
        var tested;
        var str;
        var index;
        var tmp;

        var oneMatchPredicate = predicate.oneMatch;
        var allMatchPredicate = predicate.allMatch;

        //$log.log('oneMatchPredicate: ',oneMatchPredicate);

        // 一阶段过滤oneMatch
        for (var key in oneMatchPredicate) {
            oneMatchPredicate[key] = oneMatchPredicate[key].toString();
        }

        for (var i = 0 ; i < input.length ; i++) {
            
            flag = false;
            tested = false;

            for (var key in oneMatchPredicate) {
                tested = true;
                if (input[i][key] != undefined) {
                    str = input[i][key].toString();
                    index = str.indexOf(oneMatchPredicate[key]);
                    if (index >= 0) {
                        flag = true;
                        break;
                    }
                }
            }

            if (flag || !tested) {
                tmp = {};
                angular.copy(input[i],tmp);
                out.push(tmp);
            }

        }

        // $log.log("out before 'filter': ",out,allMatchPredicate);

        // 二阶段allMatch调用内置filter
        out = $filter('filter')(out,allMatchPredicate);

        // $log.log("final: ",out);

        return out;
    }
});