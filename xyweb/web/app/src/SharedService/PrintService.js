/**
 * Created by xingyunbooks on 2016/12/21.
 */
'use sctrit'
/**
 * PrintService 机制
 *
 * 1. 监听 PRINT_ORDER 事件
 * 2. 从而引发运行 PrintService.previewOrder / PrintService.printOrder
 * 3. 从而引发运行 print.print / print.preview
 * 4. 从而引发运行 requestPrintTemplate() 获取打印的样式赋值给 print_template，运行 requestOrder() 获取订单数据
 * 5. requestOrder() 引发运行 requestShopName()
 * 6. 从而引发运行 createPrintPageForA4()
 * 7. 从而引发运行 cutPage() 获取 pageInfo 分页数据，getBill() 返回表格内容 strHtml
 */
xy.factory('PrintService',['$rootScope', '$log', 'NetworkService', '$cookies', 'EventService','PageService',
    function ($rootScope, $log, NetworkService, $cookies, EventService,PageService)
    {
        $rootScope.print_font_size = '20px';
        var PrintService = {};
        var paymentNode = '';
        var Print = {
            newInstance: function () {

                var print = {};

                var LODOP;
                var mOid; // 请求订单所传的参数
                var mDomain; // 服务器地址
                var mOrder; // 订单
                var mShopName; // 商店名称
                var print_template; // 打印模板
                var mType = 0; // 1是打印， 2是预览
                var mTop = 0; // 距顶端距离自增
                var A4OrderConfig = {
                    pageHeight:87, //93 三联
                    pageWidth:241,

                    pageHeaderHeight:35,
                    tableItemWidth:30,

                    small_font_size:3,

                    unit:'mm'
                };

                function cutPage() {
                    var C = A4OrderConfig;
                    // 用于保存页面打印数据，每页列表的首末 index
                    var pageInfo = {
                        pageData : [],

                    };
                    var page = 0;
                    // cartTableMaxHeight：除去页首，给列表留下的高度
                    var cartTableMaxHeight = A4OrderConfig.pageHeight-A4OrderConfig.pageHeaderHeight- 5;
                    console.log(cartTableMaxHeight);
                    var cartTableHeight = C.small_font_size ;
                    for(var i = 0;i<mOrder.cart.length;i++)
                    {
                        // 根据名称和规格字符数计算其宽度, 这与 tableItemWidth 不同
                        // spu_name_width 是字符在一行显示的长度；
                        // tableItemWidth 是格子的宽度内每行字符的宽度，定为 30mm
                        // 后面在 <style> 标签内设置格子的宽度为 40mm
                        // 所以每个格子留了 10mm 宽度的空白
                        var spu_name_width = getLength(mOrder.cart[0].spu_name) * A4OrderConfig.small_font_size;
                        var spec_name_width = getLength(mOrder.cart[0].spec_name) * A4OrderConfig.small_font_size;
                        // 取名称和规格宽度中更大的那个 large_width
                        var large_width = spu_name_width>spec_name_width?spu_name_width:spec_name_width;
                        // 将当前一格的高度添加到列表的高度 cartTableHeight
                        cartTableHeight += ((large_width/C.tableItemWidth + 1)*C.small_font_size);
                        console.log(cartTableHeight);
                        // 当前页快要写满的时候，或者列表已经写完了的时候，在 pageInfo.pageData 写入首末 index
                        if(cartTableHeight/cartTableMaxHeight >= 0.9 || i == (mOrder.cart.length-1)) {
                            // 若 pageInfo.pageDat 已经有数据，则添加的数据的 start index 等于前一页末尾 index + 1
                            if(pageInfo.pageData.length>0) {
                                pageInfo.pageData.push({start:pageInfo.pageData[pageInfo.pageData.length-1].end+1,end:i});
                            }
                            // 若 pageInfo.pageDat 没有数据，则添加的数据的 start index 从 0 开始
                            else{
                                pageInfo.pageData.push({start:0,end:i});
                            }
                            cartTableHeight = C.small_font_size ;
                        }
                    }
                    return pageInfo;

                }

                // var mHtmlStyle = '<style>.bill-container{height:'+A4OrderConfig.pageHeight+'mm;width: 200mm;min-height:86mm;font-size: 3mm;position: relative;margin-top: 5mm;}  .bill-title{text-align: center}  .bill-title h1{margin: 2mm 0}  .bill-info{text-align: left;padding-left: 0mm;}  .bill-info-item{width: 45mm;display: inline-block ;white-space: pre-wrap;word-break:break-all;vertical-align: top}  .bill-finance-info{text-align: center}  .bill-finance-info .item{display: inline-block;width: 30mm;}  .bill-good-table table{margin: 0 auto}  .bill-good-table tr{ border-bottom: 1px solid gainsboro;text-align: center}  .bill-good-table td{ width: 30mm;border-bottom: 2px solid grey;font-size: large}  .bill-footer{position:absolute;left: 5mm;bottom:5mm}</style>';
                var mHtmlStyle = '<style>.bill-good-table table{margin: 0;width: 100%}  .bill-good-table tr{ border-bottom: 1px solid gainsboro;text-align: center}  .bill-good-table td{ width: 40mm;border-bottom: 2px solid grey;font-size: ' + $rootScope.print_font_size + '}</style>';

                var mHtmlPageCartNum = 8;

                print.mPageType = '';


                /**
                 * 打印
                 * @param domain
                 * @param oid
                 */
                print.print = function (domain, oid) {
                    LODOP = getLodop();
                    if(!LODOP){
                        $log.debug('initial lodop failed.');
                        PageService.showConfirmDialog('您没有安装打印服务或者打印服务版本过低，请点击【下载】，下载最新打印安装包。如果您已经安装最新打印服务请自行运行Lodop。',['下载','取消'],function () {
                            //打开某个网站
                            window.open("http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip");
                            // window.location.href = 'http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip'
                            },function () {
                        });
                        // PageService.showSharedToast('您尚未安装Lodop或安装有误！\n参见：http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip');
                        return false;
                    }else{
                        PageService.showSharedToast('打印中，请稍后...');
                        mDomain = domain||NetworkService.getDomain();
                        mOid = oid;
                        mType = 1;
                        // requestOrder() 引发的 cutPage() createPrintPageForA4()，需用到 requestPrintTemplate() 返回的 print_template
                        requestPrintTemplate();
                        requestOrder();
                    }

                };

                /**
                 * 预览
                 * @param domain
                 * @param oid
                 */
                print.preview = function (domain, oid) {
                    LODOP = getLodop();
                    if(!LODOP){
                        $log.debug('initial lodop failed..');
                        // PageService.closeDialog();
                    PageService.showConfirmDialog('您没有安装打印服务或者打印服务版本过低，请点击【下载】，下载最新打印安装包。如果您已经安装最新打印服务请自行运行Lodop。',['下载','取消'],function () {
                            //打开某个网站
                            window.open("http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip");
                            // window.location.href = 'http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip'
                            },function () {
                        });

                        // PageService.showSharedToast('您没有安装打印服务或者打印服务版本过低，请点击【下载】，下载最新打印安装包，如果您已经安装最新打印服务请自行运行。');
                        return false;
                    }else{
                        PageService.showSharedToast('打印预览中，请稍后...');
                        mDomain = domain||NetworkService.getDomain();
                        mOid = oid;
                        mType = 2;
                        // requestOrder() 引发的 cutPage() createPrintPageForA4()，需用到 requestPrintTemplate() 返回的 print_template
                        requestPrintTemplate();
                        requestOrder();
                    }
                }

                /**
                 * 向服务器发送oid并请求订单json
                 */
                function requestOrder() {
                    var param = {'oid': mOid};
                    NetworkService.request('order_get_',param,function(data){
                        if(data.EC > 0) {                            mOrder = data.data;
                            paymentNode = '';
                            if(Number(data.data.cash) > 0){
                                paymentNode += '现金' + ' ';
                            }
                            if(Number(data.data.bank) > 0){
                                paymentNode += '银行' + ' ';
                            }
                            if(Number(data.data.online_pay) > 0){
                                paymentNode += '网络' + ' ';
                            }
                            requestShopName();
                        }
                    });
                }
                /**
                 * 向服务器请求商店名称
                 */
                function requestShopName() {
                    var data = {};
                    NetworkService.request('getUserInfo_shopName',data,function(data){
                        if(data.EC > 0) {
                            mShopName = data.data;
                            if(print.mPageType == 'A4') createPrintPageForA4();
                            else CreatePrintPage();
                        }
                    });
                }

                function checkLodop(){
                    LODOP = getLodop();
                    if(!LODOP){
                        $log.debug('initial lodop failed.');
                        PageService.showConfirmDialog('您没有安装打印服务或者打印服务版本过低，请点击【下载】，下载最新打印安装包。如果您已经安装最新打印服务请自行运行Lodop。',['下载','取消'],function () {
                            //打开某个网站
                            window.open("http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip");
                            // window.location.href = 'http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip'
                            },function () {
                        });
                        return false;
                    }
                    return true;
                }

                /**
                 * 向服务器请求模板，并存在全局变量 print_template 中
                 */
                function requestPrintTemplate() {
                    var data = { 'class': 1 };
                    NetworkService.request('getTemplate',data,function(data){
                        if(data.EC > 0) {
                                print_template = data.data;
                                $rootScope.print_font_size = print_template.font_size + 'px';
                        }
                    });
                }
                /**
                 * 建立打印页
                 * @constructor
                 */
                function CreatePrintPage() {

                    LODOP.PRINT_INIT(""); // 初始化运行环境，清理异常打印遗留的系统资源
                    LODOP.SET_PRINTER_INDEX("GP-58MBIII"); // 按名称或序号指定要进行打印输出的设备，指定后禁止重新选择
                    LODOP.SET_PRINT_PAGESIZE(3, "57mm", "5mm", ""); // 设定打印纸张为固定纸张或自适应内容高，并设定相关大小值或纸张名及打印方向
                    LODOP.SET_PRINT_STYLE("Alignment", 2); // 居中
                    LODOP.SET_PRINT_STYLE("Bold", 1); // 粗体
                    addSingle(mShopName); // 标题--商店名称

                    LODOP.SET_PRINT_STYLE("Alignment", 1); // 左靠齐
                    LODOP.SET_PRINT_STYLE("Bold", 0); // 非粗体
                    addPair("客户名：", mOrder.cid_name);
                    addPair("联系人：", mOrder.contact_name);
                    addPair("电话：", mOrder.mobile);
                    addPair("车牌号：", mOrder.car_license);
                    addPair("停车位置：", mOrder.park_address);
                    addLine();
                    addSingle("名称        规格/       数量/");
                    addSingle("            单价        总价");
                    addCart();

                    if (mType === 1) {
                        LODOP.PRINT();
                    } else if (mType === 2) {
                        LODOP.PREVIEW();
                    }
                };

                /**
                 *A4纸发票的打印页
                 */
                function createPrintPageForA4() {
                    var html = new EJS({url: 'web/app/src/Temp/PrintA4.ejs'}).render({order:mOrder});

                    var pageInfo = cutPage();
                    console.log(pageInfo);
                    LODOP = getLodop();
                    console.log(LODOP);

                    if(!LODOP){
                        PageService.showConfirmDialog('您没有安装打印服务或者打印服务版本过低，请点击【下载】，下载最新打印安装包。如果您已经安装最新打印服务请自行运行Lodop。',['下载','取消'],function () {
                            //打开某个网站
                            window.open("http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip");
                            // window.location.href = 'http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip'
                            },function () {
                        });
                        // PageService.showSharedToast('您尚未安装Lodop或安装有误！\n参见：http://www.lodop.net/download/CLodop_Setup_for_Win32NT_2.102.zip');
                        return false;
                    }

                    // LODOP.PRINT_INIT(""); // 初始化运行环境，清理异常打印遗留的系统资源

                    //本来就不用：
                        //LODOP.SET_PRINTER_INDEX("Fax"); // 按名称或序号指定要进行打印输出的设备，指定后禁止重新选择
                        // LODOP.SET_PRINT_PAGESIZE(3, "0", "0", "A4"); // 设定打印纸张为固定纸张或自适应内容高，并设定相关大小值或纸张名及打印方向

                    // LODOP.SET_PRINT_PAGESIZE(1, "241mm", "93mm", ""); // 设定打印纸张为固定纸张或自适应内容高，并设定相关大小值或纸张名及打印方向

                    /**************************************************************************/
                    LODOP.SET_PRINT_STYLE("Alignment", 2); // 居中
                    LODOP.SET_PRINT_STYLE("Bold", 1); // 粗体

                    // LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);

                    LODOP.SET_PRINT_STYLE("Alignment", 1); // 左靠齐
                    LODOP.SET_PRINT_STYLE("Bold", 0); // 非粗体

                    var total_page = parseInt(mOrder.cart.length/mHtmlPageCartNum + 1);

                    var strHtml = "";
                    // for(var i =1 ;i<=total_page;i++)
                    // {
                    //     strHtml += genHtmlBillByPage(i,total_page);
                    // }





                    // for(var i =0;i<pageInfo.pageData.length;i++)
                    // {
                    //     strHtml += genHtmlBillByPage(i+1,pageInfo.pageData.length,pageInfo.pageData[i].start,pageInfo.pageData[i].end);
                    // }


                    strHtml +=getBill();

                    // 201：是否有边框
                    if (print_template.optionArray[201] == 1 ){
                        strHtml += '<style>.bill-good-table table {margin: 0;border-top:1px solid grey;border-left:1px solid grey;border-collapse:collapse;width: 100%}.bill-good-table tr {margin: 0;padding: 0;text-align: center;}.bill-good-table td {margin: 0;padding: 0;width: 40mm;border-bottom: 1px solid grey;border-right:1px solid grey;font-size: ' + $rootScope.print_font_size + ';}';
                    }else{
                        strHtml += '<style>.bill-good-table table{margin: 0;width: 100%}  .bill-good-table tr{ border-bottom: 1px solid gainsboro;text-align: center;}  .bill-good-table td{ width: 40mm;border-bottom: 2px solid grey;font-size: ' + $rootScope.print_font_size + ';}';
                    }
                    strHtml += '.bill-good-table td.spu_name {width: ' + print_template.optionArray[20] * 240 + 'mm; }';
                    strHtml += '.bill-good-table td.spec_name {width: ' + print_template.optionArray[21] * 240 + 'mm; }';
                    strHtml += '.bill-good-table td.uniPrice {width: ' + print_template.optionArray[22] * 240 + 'mm; }';
                    strHtml += '.bill-good-table td.quantity_title {width: ' + print_template.optionArray[23] * 240 + 'mm; }';
                    if (mOrder.is_calculated==1) {
                        strHtml += '.bill-good-table td.pilePrice_title {width: ' + print_template.optionArray[24] * 120 + 'mm; }';
                        strHtml += '.bill-good-table td.freightCost {width: ' + print_template.optionArray[24] * 120 + 'mm; }';
                    }
                    else strHtml += '.bill-good-table td.pilePrice_title {width: ' + print_template.optionArray[24] * 240 + 'mm; }';
                    strHtml += '.bill-good-table td.comment {width: ' + print_template.optionArray[25] * 240 + 'mm; }';
                    strHtml += '</style>';
                    $log.log("wbx",mOrder);

                    //绑定的变量中需要进行变换的，进行变换：
                    var reg_date = new Date();
                    reg_date.setTime(mOrder.reg_time * 1000);
                    var className = '';
                    if (mOrder.class == 1){
                        className = '销售出货单';
                        $rootScope.text1 = '应收：';
                        $rootScope.text2 = '实收：';
                    }else if (mOrder.class == 2){
                        className = '销售退货单';
                        $rootScope.text1 = '应付：';
                        $rootScope.text2 = '实付：';
                    }else if (mOrder.class == 3){
                        className = '采购入库单';
                        $rootScope.text1 = '应付：';
                        $rootScope.text2 = '实付：';
                    }else if (mOrder.class == 4){
                        className = '采购退货单';
                        $rootScope.text1 = '应收：';
                        $rootScope.text2 = '实收：';
                    }

                    //修改一下文件请对应修改printSettingController.js文件下的对应值
                    var lodop_mShopName     = mShopName+className;
                    var lodop_reg_date      = reg_date.toLocaleString();
                    var lodop_cid_name      = mOrder.cid_name;
                    var lodop_contact_name  = mOrder.contact_name+' '+mOrder.mobile;
                    var lodop_name          = mOrder.contact_name;
                    var lodop_mobile        = mOrder.mobile;
                    var lodop_car_license   = mOrder.car_license;
                    var lodop_park_address  = mOrder.park_address;
                    var lodop_value         = (parseFloat(mOrder.value)).toFixed(2);
                    var lodop_off           = (parseFloat(mOrder.off)).toFixed(2);
                    var lodop_yingshou      = $rootScope.text1 + (parseFloat(mOrder.receivable)).toFixed(2);
                    var lodop_income        = $rootScope.text2 + (parseFloat(mOrder.income)).toFixed(2);
                    var lodop_remain        = (parseFloat(0-mOrder.remain)).toFixed(2);
                    var lodop_sn            = mOrder.sn;
                    var lodop_cash          = (parseFloat(mOrder.cash)).toFixed(2);
                    var lodop_bank          = (parseFloat(mOrder.bank)).toFixed(2);
                    var lodop_onlinepay     = (parseFloat(mOrder.online_pay)).toFixed(2);
                    var lodop_operator_name = mOrder.operator_name;
                    var lodop_data_table    = strHtml;
                    var lodop_num           = 0;
                    var lodop_remark        = mOrder.remark;
                    var lodop_paymentNode   = paymentNode;
                    for(var value in mOrder.cart)
                        lodop_num += mOrder.cart[value].quantity

                    var tmp_height          = (mOrder.cart.length*8+50)*3.78;


                    eval(print_template.content);
                    // tmp_xyjxcLoginStatus.print_template.font_size

                    // LODOP.ADD_PRINT_HTM(0,0,"100%","100%",strHtml);






                    if (mType === 1) {
                        LODOP.PRINT();
                    } else if (mType === 2) {
                        LODOP.PREVIEW();
                    }
                    print.mPageType = '';

                }

                function getBill(){
                    var strHtml = "";

                    // optionArray = print_template.table;
                    //
                    // 表格标题
                    // printOrder：以打印顺序为 key, 以打印标题和内容为 value 的数组
                    var printOrder = new Array(6).fill({});
                    // 给 printOrder 添加打印列表顺序，顺序存在 optionArray 数组的 10~15 区间
                    printOrder = printOrder.map((item, index) => ({
                        isChecked: print_template.optionArray[index],
                        order: print_template.optionArray[index+10]
                    }));

                    // 给 printOrder 添加标题
                    printOrder[0].title = print_template.optionArray[0] == 1 ? "<td class='spu_name'>名称</td>" : '';
                    printOrder[0].key = 'spu_name';
                    printOrder[1].title = print_template.optionArray[1] == 1 ? "<td class='spec_name'>规格</td>" : '';
                    printOrder[1].key = 'spec_name';
                    printOrder[2].title = print_template.optionArray[2] == 1 ? "<td class='uniPrice'>单价</td>" : '';
                    printOrder[2].key = 'unitPrice';

                    if(mOrder.class == 1){
                        printOrder[3].title = print_template.optionArray[3] == 1 ? "<td class='quantity_title'>销售数量</td>" : '';
                    }else if (mOrder.class == 2 || mOrder.class == 4) {
                        printOrder[3].title = print_template.optionArray[3] == 1 ? "<td class='quantity_title'>退货数量</td>" : '';
                    }else if (mOrder.class == 3) {
                        printOrder[3].title = print_template.optionArray[3] == 1 ? "<td class='quantity_title'>采购数量</td>" : '';
                    }
                    printOrder[3].key = 'quantity';
                    
                    printOrder[4].title = print_template.optionArray[4] == 1 ? "<td class='pilePrice_title'>金额</td>" : '';
                    printOrder[4].title += mOrder.is_calculated==1?"<td class='freightCost'>分摊金额</td>":'';
                    printOrder[4].key = 'pilePrice';
                    printOrder[5].title = print_template.optionArray[5]==1?"<td class='comment'>备注</td>":'';
                    printOrder[5].key = 'comment';

                    // 对 printOrder 数组排序
                    printOrder.sort((a,b) => {
                        return a.order - b.order;
                    })
                    // 将表格标题添加到输出的 strHtml 字符串
                    strHtml += '    <div class="bill-good-table">';
                    strHtml += '        <table>';
                    strHtml += '            <tr>';
                    // 将 printOrder 的标题显示出来
                    printOrder.forEach(item => {
                        strHtml += item.title;
                    })
                    strHtml += '            </tr>';

                    // 订单总商品数量和总价格
                    var totalCount = 0, totalPrice = 0;
                    mOrder.cart.forEach(item => {
                        totalCount += item.quantity;
                        totalPrice += item.pilePrice;
                    });
                    // item: 订单表, printOrderItem: 打印模板
                    mOrder.cart.forEach(item => {
                        strHtml += '<tr>';
                        var showItem = '';
                        printOrder.forEach(printOrderItem => {
                            showItem = '';
                            if(printOrderItem.key == 'pilePrice') {
                                showItem += `<td>${item.quantity*item.unitPrice}</td>`
                                if(mOrder.is_calculated == 1) {
                                    // 两种分摊运费的方法：1，2
                                    showItem += `<td>${(item.pilePrice-item.quantity*item.unitPrice).toFixed(2)}</td>`
                                }
                            }else {
                                // 若 item[printOrderItem.key] == undefined, 输出 <td></td>。因为 item.comment 可能是空的。
                                // 如果不这么做，会输出 <td>undefined</td>
                                showItem = item[printOrderItem.key] ? `<td>${item[printOrderItem.key]}</td>` : '<td></td>';
                            }
                            // 被选中的项才会添加到 strHtml 中输出
                            strHtml += printOrderItem.isChecked ? showItem : '';
                        })
                        strHtml += '</tr>';
                    })
                    strHtml += '        </table>';
                    strHtml += '    </div>';
                    return strHtml;

                }




                function genHtmlBillByPage(pageNum,totalPage,start,end) {
                    var reg_date = new Date();
                    reg_date.setTime(mOrder.reg_time * 1000);

                    var start = start||mHtmlPageCartNum*(pageNum -1);
                    var end = end;
                    var strHtml = "";

                    // strHtml += '<div class="bill-container">';
                    // // strHtml += '    <div class="bill-title">';
                    // // strHtml += '       <h1>'+mShopName+'销售出货单</h1>';
                    // // strHtml += '    </div>';
                    // // strHtml += '    <div class="bill-info" style="">';
                    // // strHtml += '        <div class="bill-info-item">客户:'+mOrder.cid_name+'</div>';
                    // // strHtml += '        <div class="bill-info-item" >日期:'+reg_date.toLocaleDateString()+'</div>';
                    // // strHtml += '        <div class="bill-info-item" >单号:'+mOrder.sn+'</div>';
                    // // strHtml += '        <div class="bill-info-item" >页码:第'+pageNum+'页 共'+totalPage+'页</div>';
                    // // strHtml += '        <div class="bill-info-item" >联系人:'+mOrder.contact_name+' '+mOrder.mobile+'</div>';
                    // // strHtml += '        <div class="bill-info-item" >车牌:'+mOrder.car_license+'</div>';
                    // // strHtml += '        <div class="bill-info-item" >送货地址:'+mOrder.park_address+'</div>';
                    // // strHtml += '    </div>';

                    // // strHtml += '    <div class="bill-finance-info">';
                    // // strHtml += '        <div class="item">货物价值:'+mOrder.value+'</div>';
                    // // strHtml += '        <div class="item">优惠金额:'+mOrder.off+'</div>';
                    // // strHtml += '        <div class="item">应收金额:'+(mOrder.value-mOrder.off)+'</div>';
                    // // strHtml += '        <div class="item">实收金额:'+(mOrder.value-mOrder.off-mOrder.remain)+'</div>';
                    // // strHtml += '        <div class="item">本次结余:'+(0-mOrder.remain)+'</div>';
                    // // // strHtml += '        <div class="item">此前结余:'+mOrder.remain+'</div>';
                    // // // strHtml += '        <div class="item">总结余:'+mOrder.remain+'</div>';
                    // // strHtml += '    </div>';
                    // if(pageNum ===1){
                    //     strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // strHtml += '    </br>';
                    // }


                    // strHtml += '    <hr>';
                    // strHtml += '    <div class="bill-good-table">';
                    // strHtml += '        <table>';
                    // strHtml += '            <tr>';
                    // strHtml += '                <td>商品编号</td>';
                    // strHtml += '                <td>名称</td>';
                    // strHtml += '                <td>规格</td>';
                    // strHtml += '                <td>单价</td>';
                    // strHtml += '                <td>销售数量</td>';
                    // strHtml += '                <td>金额</td>';
                    // strHtml += '            </tr>';



                    // for(var i = start;i<=end;i++)
                    // {
                    //     strHtml += '            <tr>';
                    //     strHtml += '                <td>'+mOrder.cart[i].sn+'</td>';
                    //     strHtml += '                <td>'+mOrder.cart[i].spu_name+'</td>';
                    //     strHtml += '                <td>'+mOrder.cart[i].spec_name+'</td>';
                    //     strHtml += '                <td>'+mOrder.cart[i].unitPrice+'</td>';
                    //     strHtml += '                <td>'+mOrder.cart[i].quantity+'</td>';
                    //     strHtml += '                <td>'+mOrder.cart[i].pilePrice+'</td>';
                    //     strHtml += '            </tr>';
                    // }

                    // strHtml += '        </table>';
                    // strHtml += '    </div>';

                    // // strHtml += '    <div class="bill-footer">';
                    // // strHtml += '            <span>经办人：</span>';
                    // // strHtml += '            <span>注：结余为负代表客户欠商家，为正代表商家欠客户</span>';
                    // // strHtml += '    </div>';
                    // strHtml += '</div>';
                    // // strHtml += '';
                    // // strHtml += '';
                    // //strHtml += '<style>.bill-container{width: 200mm;min-height:100mm;border: 1px solid black;font-size: small;position: relative}  .bill-title{text-align: center}  .bill-title h1{margin: 2mm 0}  .bill-info{text-align: left;padding-left: 10mm;}  .bill-info-item{width: 45mm;display: inline-block ;white-space: pre-wrap;word-break:break-all;vertical-align: top}  .bill-finance-info{text-align: center}  .bill-finance-info .item{display: inline-block;width: 25mm;}  .bill-good-table table{margin: 0 auto}  .bill-good-table tr{ border-bottom: 1px solid gainsboro;text-align: center}  .bill-good-table td{ width: 28mm;border-bottom: 2px solid grey;font-size: small}  .bill-footer{position:absolute;left: 5mm;bottom:1mm}</style>';



                    return strHtml;
                }

                /**
                 * 判断{@param c}是否属于ascii码表中的字符
                 * @param c
                 * @returns {boolean}
                 */
                function isLetter(c) {
                    var a = 128;
                    return c / a <= 1 ? true : false;
                };
                /**
                 * 得到一个字符串的长度,显示的长度,一个汉字或日韩文长度为2,英文字符长度为1
                 * @param s
                 * @returns {number}
                 */
                function getLength(s) {
                    if (s == null) {
                        return 0;
                    }
                    var len = 0;
                    for (var i = 0; i < s.length; i++) {
                        len++;
                        if (!isLetter(s.charCodeAt(i))) {
                            len++;
                        }
                    }
                    return len;
                };

                /**
                 * 根据传入的长度生成指定长度的空格
                 * @param length
                 * @returns {*}
                 */
                function getSpace(length) {
                    var space = "";
                    for (var i = 0; i < length; i++) {
                        space += " ";
                    }
                    return space;
                };

                /*
                 * 增加一条水平虚线
                 */
                function addLine() {
                    LODOP.ADD_PRINT_LINE(mTop, 0, mTop, "57mm", 2);
                    mTop += 3;
                }

                /**
                 * 一行内增加一条文本
                 * @param str
                 */
                function addSingle(str) {
                    LODOP.ADD_PRINT_TEXT(mTop, 0, "57mm", 15, str);
                    mTop += 15;
                }

                /*
                 * 一行内增加一对文本
                 * @param key
                 * @param value
                 */
                function addPair(key, value) {
                    var keyLength = getLength(key);
                    var valueLength = getLength(value);
                    var wholeLength = getLength(key + value);
                    var keySpace = getSpace(keyLength);
                    var usableLength = 30 - keyLength;

                    if (wholeLength > 30) {

                        var valueArray = splitToArray(value, usableLength);

                        for (var i = 0; i < valueArray.length; i++) {
                            if (i === 0) {
                                addSingle(key + valueArray[i]);
                            } else {
                                addSingle(keySpace + valueArray[i]);
                            }
                        }
                    } else {
                        addSingle(key + value);
                    }
                }

                /**
                 * 增加订单表格
                 * @param cart
                 */
                function addCart() {
                    var cart = mOrder.cart;

                    var leftSpace = getSpace(12); // 左侧项空白时的空格
                    var midSpace = getSpace(12); // 中间项空白时的空格
                    var rightSpace = getSpace(6); // 右侧项空白时的空格
                    var fillSpace = "";

                    for (var i = 0; i < cart.length; i++) {
                        var spuArray = splitToArray(cart[i].spu_name, 10); // 名称
                        var specArray = splitToArray(cart[i].spec_name + "/", 10); // 规格
                        var quantityArray = splitToArray(cart[i].quantity + "/", 6); // 数量
                        var unitPriceArray = splitToArray(cart[i].unitPrice + "元", 10); // 单价
                        var pilePriceArray = splitToArray(cart[i].pilePrice + "元", 6); // 总价

                        var spuRow = spuArray.length;
                        var specRow = specArray.length;
                        var quantityRow = quantityArray.length;
                        var unitPriceRow = unitPriceArray.length;
                        var pilePriceRow = pilePriceArray.length;

                        addLine();

                        var upperRow = Math.max(spuRow, specRow, quantityRow);
                        var lowerRow = Math.max(unitPriceRow, pilePriceRow);
                        var totalRow = upperRow + lowerRow;
                        for (var j = 0; j < totalRow; j++) {
                            var temp = "";

                            if (j + 1 > spuRow) {
                                temp += leftSpace;
                            } else {
                                fillSpace = getSpace(12 - getLength(spuArray[j]));
                                temp += spuArray[j] + fillSpace;
                            }

                            if (j + 1 <= upperRow) {
                                if (j + 1 > specRow) {
                                    temp += midSpace;
                                } else {
                                    fillSpace = getSpace(12 - getLength(specArray[j]));
                                    temp += specArray[j] + fillSpace;
                                }

                                if (j + 1 > quantityRow) {
                                    temp += rightSpace;
                                } else {
                                    fillSpace = getSpace(6 - getLength(quantityArray[j]));
                                    temp += quantityArray[j] + fillSpace;
                                }
                            } else {
                                if (j + 1 - upperRow > unitPriceRow) {
                                    temp += midSpace;
                                } else {
                                    fillSpace = getSpace(12 - getLength(unitPriceArray[j - upperRow]));
                                    temp += unitPriceArray[j - upperRow] + fillSpace;
                                }

                                if (j + 1 - upperRow > pilePriceRow) {
                                    temp += rightSpace;
                                } else {
                                    fillSpace = getSpace(6 - getLength(pilePriceArray[j - upperRow]));
                                    temp += pilePriceArray[j - upperRow] + fillSpace;
                                }
                            }

                            addSingle(temp);
                        }
                    }

                }

                /**
                 * 将给定的字符串按给定长度（打印长度）分割成数组
                 * @param str
                 */
                function splitToArray(str, length) {
                    console.log(str);
                    var strArray = [];
                    var temp = "";

                    for (var i = 0; i < str.length; i++) {
                        temp += str.substring(i, i + 1);
                        if (getLength(temp) > length) {
                            temp = temp.substring(0, temp.length - 1);
                            strArray.push(temp);
                            temp = str.substring(i, i + 1);
                        } else if (getLength(temp) === length) {
                            strArray.push(temp);
                            temp = "";
                        }
                    }

                    if (temp.length > 0) {
                        strArray.push(temp);
                        temp = "";
                    }

                    return strArray;
                }

                return print;

            }

        }
        var printer = Print.newInstance();
        PrintService.printOrder = function (oid) {
            printer.print('',oid);
        };
        PrintService.previewOrder = function (oid) {
            printer.preview('',oid);
        }
        EventService.on(EventService.ev.PRINT_ORDER,function (event,opt) {
            $log.debug('oid:'+opt.oid);
            $log.debug('isPreview:'+opt.isPreview);
            $rootScope.class = opt.class;
            if(opt&&opt.pageType) {
                printer.mPageType = opt.pageType;
            }
            if(opt.isPreview){
                PrintService.previewOrder(opt.oid);
            }else {
                PrintService.printOrder(opt.oid);
            }
            if($rootScope.class == 1 || $rootScope.class == 4){
                $rootScope.text1 = '应收：';
                $rootScope.text2 = '实收：';
            }else{
                $rootScope.text1 = '应付：';
                $rootScope.text2 = '实付：';
            }
        });
        return PrintService;
    }
])