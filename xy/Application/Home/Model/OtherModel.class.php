<?php
namespace Home\Model;
use Think\Model;

/**
 * 其他类Model.要求权限。
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class OtherModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		// //MODEL_SINGLE_SMS
		// array('type', 'check_UtilModel_MODEL_SINGLE_SMS_type', -12001, 
		// 	self::MUST_VALIDATE, 'callback',self::MODEL_SINGLE_SMS),//type不合法
		// array('mobile', 'checkMobile', -12002,
		// 	self::MUST_VALIDATE, 'callback',self::MODEL_SINGLE_SMS), //手机格式不正确
		// array('mobile', '11,11', -12002,
		// 	self::MUST_VALIDATE, 'length',self::MODEL_SINGLE_SMS), //手机格式不正确

		// //MODEL_VERIFY_CODE_CHECK
		// array('type', 'check_UtilModel_MODEL_SINGLE_SMS_type', -12001, 
		// 	self::MUST_VALIDATE, 'callback',self::MODEL_VERIFY_CODE_CHECK),//type不合法
		// array('verify_code', '4,4', -12003,
		// 	self::MUST_VALIDATE, 'length',self::MODEL_VERIFY_CODE_CHECK), //验证码不合法
  //       array('mobile', '11,11', -12002,
  //           self::MUST_VALIDATE, 'length',self::MODEL_VERIFY_CODE_CHECK), //手机格式不正确
	);



	/* 自动完成 */
	protected $_auto = array(
	);

    /**生成备份EXCEL文档;
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function backupByExcelFile()
    {
        //初始化查询参数
        $reg_st_time = (string)strtotime(date('Ymd'));
        $reg_end_time =(string)time();
        $filter = array();
        $_POST['filter'] = '';
        $_POST['reg_st_time'] = '1000000000';
        $_POST['reg_end_time'] = $reg_end_time;
        $_POST['pline'] = '999999';
        $_POST['page'] = '1';

        //查询数据
        $query = new QueryModel();
        $querydata = $query->query_(I('param.'));
        $orderdata = $querydata['data'];
        $query = new CompanyModel();
        $querycontact = $query->get_All();
        $query = new ParkaddressModel();
        $querypark = $query->queryList();
        $query = new SkuModel();
        $data= array('type'=>1);
        $querysku = $query->querySkU($data);
        log_('skudata',$querysku);

//        log_('联系人数据',$querycontact);

        //生成EXCEL文档之前的数据处理
        $nowtime = date('Y-m-d', time());
        $name = $nowtime.'存销报告';
        Vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("星云存销")
            ->setLastModifiedBy("星云存销")
            ->setTitle("存销报告")
            ->setSubject()
            ->setDescription("Test document for Office 2007 XLSX")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        //设置单据表表
        $this->excelBt_Order($objPHPExcel);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('单据表');
        $this->orderDataOperate($orderdata,$objPHPExcel);

        //设置联系人表
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setTitle('往来单位联系人表');
        $this->excelBt_Contact($objPHPExcel);
        $this->contactDataOperate($querycontact,$objPHPExcel);

        //设置停车位置表
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(2);
        $objPHPExcel->getActiveSheet()->setTitle('停车位置表');
        $this->excelBt_ParkAddress($objPHPExcel);
        $this->parkAddDataOperate($querypark,$objPHPExcel);

        //设置商品库存表
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(3);
        $objPHPExcel->getActiveSheet()->setTitle('商品库存表');
        $this->excelBt_Sku($objPHPExcel);
        $this->skuDataOperate($querysku,$objPHPExcel);


        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$name.'.xls');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        //输出
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**生成单据表头
     * @param $objPHPExcel //excel操作对象
     * @throws PHPExcelError
     * @author DizzyLee<728394036@qq.com>
     */
    protected function excelBt_Order($objPHPExcel) {
            $objPHPExcel->getActiveSheet(0)
                ->setCellValue('A1', '订单编号')
                ->setCellValue('B1', '类别')
                ->setCellValue('C1', '货物价值')
                ->setCellValue('D1', '备注')
                ->setCellValue('E1', '开单人姓名')
                ->setCellValue('F1', '订单状态')
                ->setCellValue('G1', '创建时间')
                ->setCellValue('H1', '最后更新时间')
                ->setCellValue('I1', '单位名称')
                ->setCellValue('J1', '优惠')
                ->setCellValue('K1', '现金')
                ->setCellValue('L1', '银行')
                ->setCellValue('M1', '在线支付')
                ->setCellValue('N1', '实收（现金+银行+在线支付）')
                ->setCellValue('O1', '收入来源')
                ->setCellValue('P1', '联系人名')
                ->setCellValue('Q1', '电话')
                ->setCellValue('R1', '送货信息备注')
                ->setCellValue('S1', '应收（货物价值-优惠）')
                ->setCellValue('T1', '盘点人姓名')
                ->setCellValue('U1', '变更数量')
                ->setCellValue('V1', '出库时间')
                ->setCellValue('W1', '此前结余')
                ->setCellValue('X1', '总结余（此前结余+本单结余）')
                ->setCellValue('Y1', '结余快照（实收-应收）')
                ->setCellValue('Z1', '商品名称')
                ->setCellValue('AA1', '规格名称')
                ->setCellValue('AB1', '数量')
                ->setCellValue('AC1', '消费')
                ->setCellValue('AD1', '成本单价')
                ->setCellValue('AE1', '成本总价')
                ->setCellValue('AF1', '商品编号');

    }
    /**生成联系人表头
     * @param $objPHPExcel
     * @throws \PHPExcel_Exception
     * @author DizzyLee<728394036@qq.com>
     */
    protected function excelBt_Contact($objPHPExcel)
    {
        $objPHPExcel->getActiveSheet(1)
            ->setCellValue('A1','单位编号')
            ->setCellValue('B1','单位名称')
            ->setCellValue('C1','地址')
            ->setCellValue('D1','备注')
            ->setCellValue('E1','结余（应付-应收）')
            ->setCellValue('F1','登记时间')
            ->setCellValue('G1','最后更新时间')
            ->setCellValue('H1','联系人姓名')
            ->setCellValue('I1','联系人电话')
            ->setCellValue('J1','车牌号');
    }

    /**处理联系人数据
     * @param $data
     * @param $objPHPExcel
     * @author DizzyLee<728394036@qq.com>
     */
    protected function contactDataOperate($data,$objPHPExcel)
    {
        $num = 0;
        $contactnum = 1;
        //添加联系人信息
        foreach ($data as $k => $v)
        {
            if ($v['contact']){
                $contact = $v['contact'];
                foreach ($contact as $ck => $cv)
                {
                    $contactnum++;
                    $mobile = '';
                    $car_license = '';
                    $phonenum = $cv['phonenum'];
                    $car = $cv['car_license'];
                    foreach ($phonenum as $pk =>$pv){
                       $mobile = $mobile.$pv['mobile'].'/';
                    }

                    foreach ($car as $pk =>$pv){
                        $car_license = $car_license.$pv['car_license'].'/';
                    }
//                    log_('车牌号',$car_license);
                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue('H'.$contactnum,$cv['contact_name'])
                        ->setCellValue('I'.$contactnum,$mobile)
                        ->setCellValue('J'.$contactnum,$car_license);
                    $num = $ck;
                }
            }
            $tmp = $contactnum-$num;

            //合并单元格
            if ($num>0){
                $objPHPExcel->getActiveSheet(1)
                    ->mergeCells('A' . $tmp . ':A' . $contactnum)
                    ->mergeCells('B' . $tmp . ':B' . $contactnum)
                    ->mergeCells('C' . $tmp . ':C' . $contactnum)
                    ->mergeCells('D' . $tmp . ':D' . $contactnum)
                    ->mergeCells('E' . $tmp . ':E' . $contactnum)
                    ->mergeCells('F' . $tmp . ':F' . $contactnum)
                    ->mergeCells('G' . $tmp . ':G' . $contactnum);
            }

            $v['reg_time'] = date('Y-m-d H:i:s',$v['reg_time']);
            $v['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
            //添加单位信息
            $objPHPExcel->getActiveSheet(1)
                ->setCellValue('A'.$tmp,$v['sn'])
                ->setCellValue('B'.$tmp,$v['name'])
                ->setCellValue('C'.$tmp,$v['address'])
                ->setCellValue('D'.$tmp,$v['remark'])
                ->setCellValue('E'.$tmp,$v['balance'])
                ->setCellValue('F'.$tmp,$v['reg_time'])
                ->setCellValue('G'.$tmp,$v['update_time']);
        }
    }

    /**停车位置表头设置
     * @param $objPHPExcel
     * @author DizzyLee<728394036@qq.com>
     */
    protected function excelBt_ParkAddress($objPHPExcel)
    {
        $objPHPExcel->getActiveSheet(2)
            ->setCellValue('A1','编号')
            ->setCellValue('B1','停车位置');
    }

    /**停车位置数据处理
     * @param $data
     * @param $objPHPExcel
     * @author DizzyLee<728394036@qq.com>
     */
    protected function parkAddDataOperate($data,$objPHPExcel)
    {
        foreach ($data as $k => $v)
        {
            $num = $k+2;
            $objPHPExcel->getActiveSheet(2)
                ->setCellValue('A'.$num,$v['sn'])
                ->setCellValue('B'.$num,$v['park_address']);
        }
    }

    /**SKU表头设置方法
     * @param $objPHPExcel
     * @author DizzyLee<728394036@qq.com>
     */
    protected function excelBt_Sku($objPHPExcel)
    {
        $objPHPExcel->getActiveSheet(3)
            ->setCellValue('A1','商品名称')
            ->setCellValue('B1','商品显示优先级')
            ->setCellValue('C1','速查码')
            ->setCellValue('D1','商品状态')
            ->setCellValue('E1','品类名（类型）')
            ->setCellValue('F1','品类显示优先级')
            ->setCellValue('G1','品类状态')
            ->setCellValue('H1','规格编码')
            ->setCellValue('I1','规格名称')
            ->setCellValue('J1','单价')
            ->setCellValue('K1','最后售价')
            ->setCellValue('L1','库存数量')
            ->setCellValue('M1','创建时间')
            ->setCellValue('N1','更新时间')
            ->setCellValue('O1','规格显示优先级')
            ->setCellValue('P1','状态码');
    }
    protected function skuDataOperate($data,$objPHPExcel)
    {
        foreach ($data as $k => $v)
        {
            $num = $k+2;

            $objPHPExcel->getActiveSheet(3)
                ->setCellValue('A'.$num,$v['spu_name'])
                ->setCellValue('B'.$num,$v['spu_index'])
                ->setCellValue('C'.$num,$v['qcode'])
                ->setCellValue('D'.$num,$v['spu_status'])
                ->setCellValue('E'.$num,$v['cat_name'])
                ->setCellValue('F'.$num,$v['cat_index'])
                ->setCellValue('G'.$num,$v['cat_status'])
                ->setCellValue('H'.$num,$v['sn'])
                ->setCellValue('I'.$num,$v['spec_name'])
                ->setCellValue('J'.$num,$v['unit_price'])
                ->setCellValue('K'.$num,$v['last_selling_price'])
                ->setCellValue('L'.$num,$v['stock'])
                ->setCellValue('M'.$num,date('y-m-d h:m:s',$v['reg_time']))
                ->setCellValue('N'.$num,date('y-m-d h:m:s',$v['update_time']))
                ->setCellValue('O'.$num,$v['sku_index'])
                ->setCellValue('P'.$num,$v['status']);
        }
    }
    /**
     * 将订单数据写入excel对象.
     * @param $data 待处理数据
     * @param $objPHPExcel phpEXCEL处理对象
     * @author DizzyLee<728394036@qq.com>
     */
    protected function orderDataOperate($data,$objPHPExcel)
    {
        //数据处理
        $num = 0;
        $cartnum = 1;
        foreach($data as $k => $v){
            //添加商品
            if ($v['cart']){
                $cart = $v['cart'];
                foreach ($cart as $cartk => $cartv)
                {
                    $cartnum++;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('Z'.$cartnum,$cartv['spu_name'])
                        ->setCellValue('AA'.$cartnum,$cartv['spec_name'])
                        ->setCellValue('AB'.$cartnum,$cartv['quantity'])
                        ->setCellValue('AC'.$cartnum,$cartv['cost'])
                        ->setCellValue('AD'.$cartnum,$cartv['unit_price'])
                        ->setCellValue('AE'.$cartnum,$cartv['pilePrice'])
                        ->setCellValue('AF'.$cartnum,$cartv['sn']);
                    $num = $cartk;

                }
            }
            $tmp = $cartnum-$num;
            //合并单元格
            if ($num > 0) {
                $objPHPExcel->getActiveSheet(0)
                    ->mergeCells('A' . $tmp . ':A' . $cartnum)
                    ->mergeCells('B' . $tmp . ':B' . $cartnum)
                    ->mergeCells('C' . $tmp . ':C' . $cartnum)
                    ->mergeCells('D' . $tmp . ':D' . $cartnum)
                    ->mergeCells('E' . $tmp . ':E' . $cartnum)
                    ->mergeCells('F' . $tmp . ':F' . $cartnum)
                    ->mergeCells('G' . $tmp . ':G' . $cartnum)
                    ->mergeCells('H' . $tmp . ':H' . $cartnum)
                    ->mergeCells('I' . $tmp . ':I' . $cartnum)
                    ->mergeCells('J' . $tmp . ':J' . $cartnum)
                    ->mergeCells('K' . $tmp . ':K' . $cartnum)
                    ->mergeCells('L' . $tmp . ':L' . $cartnum)
                    ->mergeCells('M' . $tmp . ':M' . $cartnum)
                    ->mergeCells('N' . $tmp . ':N' . $cartnum)
                    ->mergeCells('O' . $tmp . ':O' . $cartnum)
                    ->mergeCells('P' . $tmp . ':P' . $cartnum)
                    ->mergeCells('Q' . $tmp . ':Q' . $cartnum)
                    ->mergeCells('R' . $tmp . ':R' . $cartnum)
                    ->mergeCells('S' . $tmp . ':S' . $cartnum)
                    ->mergeCells('T' . $tmp . ':T' . $cartnum)
                    ->mergeCells('U' . $tmp . ':U' . $cartnum)
                    ->mergeCells('V' . $tmp . ':V' . $cartnum)
                    ->mergeCells('W' . $tmp . ':W' . $cartnum)
                    ->mergeCells('X' . $tmp . ':X' . $cartnum)
                    ->mergeCells('Y' . $tmp . ':Y' . $cartnum);
            }
            //数据库字段翻译
            $v['class'] = $this->classTranslate($v['class']);
            $v['status'] = $this->statusTranslate($v['status']);
            $v['reg_time'] = date('Y-m-d H:i:s',$v['reg_time']);
            $v['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
            //添加订单汇总信息
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$tmp, $v['sn'])
                ->setCellValue('B'.$tmp, $v['class'])
                ->setCellValue('C'.$tmp, $v['value'])
                ->setCellValue('D'.$tmp, $v['remark'])
                ->setCellValue('E'.$tmp, $v['operator_name'])
                ->setCellValue('F'.$tmp, $v['status'])
                ->setCellValue('G'.$tmp, $v['reg_time'])
                ->setCellValue('H'.$tmp, $v['update_time'])
                ->setCellValue('I'.$tmp, $v['cid_name'])
                ->setCellValue('J'.$tmp, $v['off'])
                ->setCellValue('K'.$tmp, $v['cash'])
                ->setCellValue('L'.$tmp, $v['bank'])
                ->setCellValue('M'.$tmp, $v['online_pay'])
                ->setCellValue('N'.$tmp, $v['income'])
                ->setCellValue('O'.$tmp, $v['name'])
                ->setCellValue('P'.$tmp, $v['contact_name'])
                ->setCellValue('Q'.$tmp, $v['mobile'])
                ->setCellValue('R'.$tmp, $v['warehouse_remark'])
                ->setCellValue('S'.$tmp, $v['receivable'])
                ->setCellValue('T'.$tmp, $v['check_name'])
                ->setCellValue('U'.$tmp, $v['num'])
                ->setCellValue('V'.$tmp, $v['leave_time'])
                ->setCellValue('W'.$tmp, $v['history_balance'])
                ->setCellValue('X'.$tmp, $v['total_balance'])
                ->setCellValue('Y'.$tmp, $v['balance']);

        }
    }

    /**
     * @param $status 订单状态
     * @return $statusname 返回具体中文状态
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    protected function statusTranslate($status)
    {
        if ($status == 0)
        {
            $statusname = '未知，该状态为错误状态';
        }
        if ($status == 1)
        {
            $statusname = '已完成';
        }
        elseif ($status == 2)
        {
            $statusname = '异常';
        }
        elseif ($status == 3)
        {
            $statusname = '被删除单据';
        }
        elseif ($status == 4)
        {
            $statusname = '立即处理';
        }
        elseif ($status == 5)
        {
            $statusname = '暂缓发货';
        }
        elseif ($status == 6)
        {
            $statusname = '正在通知库管';
        }
        elseif ($status == 7)
        {
            $statusname = '已通知库管，库管未确认';
        }
        elseif ($status == 8)
        {
            $statusname = '库管已打印单据，但未出库';
        }
        elseif ($status == 9)
        {
            $statusname = '已出库但未送达';
        }
        elseif ($status == 10)
        {
            $statusname = '库管已打印单据，但未入库';
        }
        elseif ($status == 11)
        {
            $statusname = '待审核-暂缓发货';
        }
        elseif ($status == 12)
        {
            $statusname = '待审核-立即处理';
        }
        elseif ($status == 90)
        {
            $statusname = '未完成';
        }
        elseif ($status == 91)
        {
            $statusname = '红冲单据';
        }
        elseif ($status == 92)
        {
            $statusname = '红冲附属单';
        }
        elseif ($status == 99)
        {
            $statusname = '期初应收、期初应付';
        }
        elseif ($status == 100)
        {
            $statusname = '草稿单';
        }
        else
        {
            throw new \XYException(__METHOD__,-20006);
        }
        return $statusname;
    }

    /**类别翻译方法
     * @param $class
     * @return $classname 每个订单类别数字对应的中文名称
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    protected function classTranslate($class)
    {
        if ($class == 1)
        {
            $classname = '销售单';
        }
        elseif ($class == 2)
        {
            $classname = '销售退货单';
        }
        elseif ($class == 3)
        {
            $classname = '采购单';
        }
        elseif ($class == 4)
        {
            $classname = '采购退货单';
        }
        elseif ($class == 5)
        {
            $classname = '应收款调整';
        }
        elseif ($class == 6)
        {
            $classname = '应付款调整';
        }
        elseif ($class == 51)
        {
            $classname = '报溢单';
        }
        elseif ($class == 52)
        {
            $classname = '报损单';
        }
        elseif ($class == 53)
        {
            $classname = '盘点单';
        }
        elseif ($class == 71)
        {
            $classname = '收款单';
        }
        elseif ($class == 72)
        {
            $classname = '付款单';
        }
        elseif ($class == 73)
        {
            $classname = '其他收入单';
        }
        elseif ($class == 74)
        {
            $classname = '费用单';
        }
        else
        {
            throw new \XYException(__METHOD__,-20005);
        }
        return $classname;
    }

    /**
     * 上传文件函数，获得文件的存储地址
     *
     * @param file $exceldata excel文件，input的字段名为exceldata
     * 
     * @return string 文件的存储地址（包括文件名）
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.5
     * @date    2017-03-10
     */
    public function upload()
    {
        //上传文件的配置数组
        $UPLOADCONFIG = array(
            'maxSize'    =>    10145728,
            'rootPath'   =>    C('UploadRootPath'),
            'savePath'   =>    '/importExcel/',
            'saveName'   =>    array('uniqid',''),
            'exts'       =>    array('xls', 'xlsx'),
            'autoSub'    =>    true,
            'subName'    =>    array('date','Ymd'),
        );
        $upload   = new \Think\Upload($UPLOADCONFIG);// 实例化上传类
        $fileInfo =   $upload->upload();
        if(!$fileInfo)// 上传错误提示错误信息
        {
            log_("upload->getError()",$upload->getError(),$this);
            throw new \XYException(__METHOD__,-20002);
        }
        else
        {// 上传成功获取上传文件信息
            // log_("fileInfo",$fileInfo,$this);
            $fileAddress = $fileInfo["exceldata"]['savepath'].$fileInfo["exceldata"]['savename'];
            // log_("exceldata",$exceldata,$this);
        }
        return C('UploadRootPath').$fileAddress;
    }



    /**
     * 导入exclel数据到往来单位
     *
     * @param mixed|null $data POST的数据
     *
     * @param file $exceldata excel文件，input的字段名为exceldata
     *
     * @return unsigned_int 成功导入了多少条数据
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.5
     * @date    2017-03-10
     */
    public function loadExcelFrom_ForCompany(array $data = null)
    {
        $fileAddress = $this->upload();
         log_("fileAddress",$fileAddress,$this);

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        /** Include PHPExcel */
        Vendor('PHPExcel.PHPExcel');
        // require_once APP_PATH.'../ThinkPHP/Library/Vendor/PHPExcel/PHPExcel/IOFactory.php';

        if (!file_exists($fileAddress))
            throw new \XYException(__METHOD__,-20003);

        $objPHPExcel  = \PHPExcel_IOFactory::load($fileAddress);
        $currentSheet = $objPHPExcel->getSheet(0); /* * 读取excel文件中的第一个工作表 */
        $allColumn    = $currentSheet->getHighestColumn();/**取得最大的列号*/
        $allRow       = $currentSheet->getHighestRow(); /* * 取得一共有多少行 */
        // log_("allRow",$allRow,$this);
        // log_("allColumn",$allColumn,$this);
        \PHPExcel_Cell::columnIndexFromString();//字母列转换为数字列 如:AA变为27
        $successTotal = 0;
        $pinYin       = new PinYin();
        for ($i = 2; $i <= $allRow; $i++)
        {
            $companyName = null;
            $ar          = 0;
            $ap          = 0;
            $balance     = 0;

            $companyName = (string)$currentSheet->getCellByColumnAndRow(0,$i)->getValue();
            $ar          = $currentSheet->getCellByColumnAndRow(1,$i)->getValue();//应收
            $ap          = $currentSheet->getCellByColumnAndRow(2,$i)->getValue();//应付
            if (empty($ar))
                $ar = 0;
            if (empty($ap))
                $ap = 0;
            $balance = $ap - $ar;

            D('Company')->create_(array(
                    'name'         => $companyName,
                    'qcode'        => $pinYin->getPY($companyName),
                    'address'      => '',
                    'remark'       => '',
                    'init_payable' => $balance,
                    'status'       => 1,
                    'contact'      => array(
                                        0 => array(
                                                'contact_name'  => $companyName,
                                                'phonenum'      => array(
                                                        // array('mobile' => ''),
                                                    ),
                                                'car_license' => array(
                                                        // array('car_license' => ''),
                                                    ),
                                            )
                        ),
                ),false);
            $successTotal++;
        }
        return $successTotal;
    }




    /**
     * 导入exclel数据到sku
     *
     * @param mixed|null $data POST的数据
     *
     * @param file $exceldata excel文件，input的字段名为exceldata
     *
     * @return unsigned_int 成功导入了多少条数据
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.5
     * @date    2017-03-10
     */
    public function loadExcelFrom_ForSku(array $data = null)
    {
        $fileAddress = $this->upload();
        // log_("fileAddress",$fileAddress,$this);

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        /** Include PHPExcel */
        Vendor('PHPExcel.PHPExcel');
        // require_once APP_PATH.'../ThinkPHP/Library/Vendor/PHPExcel/PHPExcel/IOFactory.php';

        if (!file_exists($fileAddress))
            throw new \XYException(__METHOD__,-20003);

        $objPHPExcel  = \PHPExcel_IOFactory::load($fileAddress);
        $currentSheet = $objPHPExcel->getSheet(0); /* * 读取excel文件中的第一个工作表 */
        $allColumn    = $currentSheet->getHighestColumn();/**取得最大的列号*/
        $allRow       = $currentSheet->getHighestRow(); /* * 取得一共有多少行 */
        // log_("allRow",$allRow,$this);
        // log_("allColumn",$allColumn,$this);
        \PHPExcel_Cell::columnIndexFromString();//字母列转换为数字列 如:AA变为27
        
        $successTotal = 0;
        $pinYin       = new PinYin();

        $cat_id = D('Cat')->createCat(array(
                    'cat_name'  => '默认',
                    'cat_index' => 2,
                    'status'    => 1,
                ),false);

        for ($i = 2; $i <= $allRow; $i++)
        {
            $skuName = '';
            $stock   = 0;
            $cost    = 0;

            $skuName = (string)$currentSheet->getCellByColumnAndRow(0,$i)->getValue();
            $stock   = $currentSheet->getCellByColumnAndRow(2,$i)->getValue();//库存数量
            $cost    = $currentSheet->getCellByColumnAndRow(3,$i)->getValue();//成本单价
            if (empty($stock))
                $stock = 0;
            if (empty($cost))
                $cost = 0;

            D('Sku')->createSKU(array(
                    'cat_id'     => $cat_id,
                    'spu_name'   => $skuName,
                    'spu_index'  => 2,
                    'qcode'      => $pinYin->getPY($skuName),
                    'spu_status' => 1,
                    'skuData'    => array(
                                        'data' => array(
                                                    0 => array(
                                                            'spec_name'  => '件',
                                                            'stock'      => $stock,
                                                            'unit_price' => $cost,
                                                            'sku_index'  => 2,
                                                            'status'     => 1,
                                                        ),
                                            )
                                    ),
                ),false);
            $successTotal++;
        }
        return $successTotal;
    }



    /**
     * 得到字符串的拼音首字母缩写（去掉中间的空格）
     *
     * @param string $str 要查询拼音的字符串
     *
     * @return string 首字母拼音缩写
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.5
     * @date    2017-03-20
     */
    public function getPinYin($str)
    {
        $pinYin = new PinYin();
        return $pinYin->getPY($str);
    }
}
















class PinYin
{
    protected $strChineseFirstPY = "ydyqsxmwzssxjbymgcczqpssqbycdscdqldylybssjgyzzjjfkcclzdhwdwzjljpfyynwjjtmyhzwzhflzppqhgscyyynjqyxxgjhhsdsjnkktmomlcrxypsnqseccqzggllyjlmyzzsecykyyhqwjssggyxyzyjwwkdjhychmyxjtlxjyqbyxzldwrdjrwysrldzjpcbzjjbrcftleczstzfxxzhtrqhybdlyczssymmrfmyqzpwwjjyfcrwfdfzqpyddwyxkyjawjffxypsftzyhhyzyswcjyxsclcxxwzzxnbgnnxbxlzszsbsgpysyzdhmdzbqbzcwdzzyytzhbtsyybzgntnxqywqskbphhlxgybfmjebjhhgqtjcysxstkzhlyckglysmzxyalmeldccxgzyrjxsdltyzcqkcnnjwhjtzzcqljststbnxbtyxceqxgkwjyflzqlyhyxspsfxlmpbysxxxydjczylllsjxfhjxpjbtffyabyxbhzzbjyzlwlczggbtssmdtjzxpthyqtgljscqfzkjzjqnlzwlslhdzbwjncjzyzsqqycqyrzcjjwybrtwpyftwexcskdzctbzhyzzyyjxzcffzzmjyxxsdzzottbzlqwfckszsxfyrlnyjmbdthjxsqqccsbxyytsyfbxdztgbcnslcyzzpsazyzzscjcshzqydxlbpjllmqxtydzxsqjtzpxlcglqtzwjbhctsyjsfxyejjtlbgxsxjmyjqqpfzasyjntydjxkjcdjszcbartdclyjqmwnqnclllkbybzzsyhqqltwlccxtxllzntylnewyzyxczxxgrkrmtcndnjtsyyssdqdghsdbjghrwrqlybglxhlgtgxbqjdzpyjsjyjctmrnymgrzjczgjmzmgxmpryxkjnymsgmzjymkmfxmldtgfbhcjhkylpfmdxlqjjsmtqgzsjlqdldgjycalcmzcsdjllnxdjffffjczfmzffpfkhkgdpsxktacjdhhzddcrrcfqyjkqccwjdxhwjlyllzgcfcqdsmlzpbjjplsbcjggdckkdezsqcckjgcgkdjtjdlzycxklqscgjcltfpcqczgwpjdqyzjjbyjhsjdzwgfsjgzkqcczllpspkjgqjhzzljplgjgjjthjjyjzczmlzlyqbgjwmljkxzdznjqsyzmljlljkywxmkjlhskjgbmclyymkxjqlbmllkmdxxkwyxyslmlpsjqqjqxyxfjtjdxmxxllcxqbsyjbgwymbggbcyxpjygpepfgdjgbhbnsqjyzjkjkhxqfgqzkfhygkhdkllsdjqxpqykybnqsxqnszswhbsxwhxwbzzxdmnsjbsbkbbzklylxgwxdrwyqzmywsjqlcjxxjxkjeqxscyetlzhlyyysdzpaqyzcmtlshtzcfyzyxyljsdcjqagyslcqlyyyshmrqqkldxzscsssydycjysfsjbfrsszqsbxxpxjysdrckgjlgdkzjzbdktcsyqpyhstcldjdhmxmcgxyzhjddtmhltxzxylymohyjcltyfbqqxpfbdfhhtksqhzyywcnxxcrwhowgyjlegwdqcwgfjycsntmytolbygwqwesjpwnmlrydzsztxyqpzgcwxhngpyxshmyqjxztdppbfyhzhtjyfdzwkgkzbldntsxhqeegzzylzmmzyjzgxzxkhkstxnxxwylyapsthxdwhzympxagkydxbhnhxkdpjnmyhylpmgocslnzhkxxlpzzlbmlsfbhhgygyyggbhscyaqtywlxtzqcezydqdqmmhtkllszhlsjzwfyhqswscwlqazynytlsxthaznkzzszzlaxxzwwctgqqtddyztcchyqzflxpslzygpzsznglndqtbdlxgtctajdkywnsyzljhhzzcwnyyzywmhychhyxhjkzwsxhzyxlyskqyspslyzwmyppkbyglkzhtyxaxqsyshxasmchkdscrswjpwxsgzjlwwschsjhsqnhcsegndaqtbaalzzmsstdqjcjktscjaxplggxhhgxxzcxpdmmhldgtybysjmxhmrcpxxjzckzxshmlqxxtthxwzfkhcczdytcjyxqhlxdhypjqxylsyydzozjnyxqezysqyayxwypdgxddxsppyzndltwrhxydxzzjhtcxmczlhpyyyymhzllhnxmylllmdcppxhmxdkycyrdltxjchhzzxzlcclylnzshzjzzlnnrlwhyqsnjhxyntttkyjpychhyegkcttwlgqrlggtgtygyhpyhylqyqgcwyqkpyyyttttlhyhlltyttsplkyzxgzwgpydsszzdqxskcqnmjjzzbxyqmjrtffbtkhzkbxljjkdxjtlbwfzpptkqtztgpdgntpjyfalqmkgxbdclzfhzclllladpmxdjhlcclgyhdzfgyddgcyyfgydxkssebdhykdkdkhnaxxybpbyyhxzqgaffqyjxdmljcsqzllpchbsxgjyndybyqspzwjlzksddtactbxzdyzypjzqsjnkktknjdjgyypgtlfyqkasdntcyhblwdzhbbydwjrygkzyheyyfjmsdtyfzjjhgcxplxhldwxxjkytcyksssmtwcttqzlpbszdzwzxgzagyktywxlhlspbclloqmmzsslcmbjcszzkydczjgqqdsmcytzqqlwzqzxssfpttfqmddzdshdtdwfhtdyzjyqjqkypbdjyyxtljhdrqxxxhaydhrjlklytwhllrllrcxylbwsrszzsymkzzhhkyhxksmdsydycjpbzbsqlfcxxxnxkxwywsdzyqoggqmmyhcdzttfjyybgstttybykjdhkyxbelhtypjqnfxfdykzhqkzbyjtzbxhfdxkdaswtawajldyjsfhbldnntnqjtjnchxfjsrfwhzfmdryjyjwzpdjkzyjympcyznynxfbytfyfwygdbnzzzdnytxzemmqbsqehxfzmbmflzzsrxymjgsxwzjsprydjsjgxhjjgljjynzzjxhgxkymlpyyycxytwqzswhwlyrjlpxslsxmfswwklctnxnynpsjszhdzeptxmyywxyysywlxjqzqxzdcleeelmcpjpclwbxsqhfwwtffjtnqjhjqdxhwlbyznfjlalkyyjldxhhycstyywnrjyxywtrmdrqhwqcmfjdyzmhmyyxjwmyzqzxtlmrspwwchaqbxygzypxyyrrclmpymgksjszysrmyjsnxtplnbappypylxyyzkynldzyjzcznnlmzhharqmpgwqtzmxxmllhgdzxyhxkyxycjmffyyhjfsbssqlxxndycannmtcjcyprrnytyqnyymbmsxndlylysljrlxysxqmllyzlzjjjkyzzcsfbzxxmstbjgnxyzhlxnmcwscyzyfzlxbrnnnylbnrtgzqysatswryhyjzmzdhzgzdwybsscskxsyhytxxgcqgxzzshyxjscrhmkkbxczjyjymkqhzjfnbhmqhysnjnzybknqmclgqhwlznzswxkhljhyybqlbfcdsxdldspfzpskjyzwzxzddxjsmmegjscssmgclxxkyyylnypwwwgydkzjgggzggsycknjwnjpcxbjjtqtjwdsspjxzxnzxumelpxfsxtllxcljxjjljzxctpswxlydhlyqrwhsycsqyybyaywjjjqfwqcqqcjqgxaldbzzyjgkgxpltzyfxjltpadkyqhpmatlcpdckbmtxybhklenxdleegqdymsawhzmljtwygxlyqzljeeyybqqffnlyxrdsctgjgxyynkllyqkcctlhjlqmkkzgcyygllljdzgydhzwxpysjbzkdzgyzzhywyfqytyzszyezzlymhjjhtsmqwyzlkyywzcsrkqytltdxwctyjklwsqzwbdcqyncjsrszjlkcdcdtlzzzacqqzzddxyplxzbqjylzlllqddzqjyjyjzyxnyyynyjxkxdazwyrdljyyyrjlxlldyxjcywywnqcclddnyyynyckczhxxcclgzqjgkwppcqqjysbzzxyjsqpxjpzbsbdsfnsfpzxhdwztdwpptflzzbzdmyypqjrsdzsqzsqxbdgcpzswdwcsqzgmdhzxmwwfybpdgphtmjthzsmmbgzmbzjcfzwfzbbzmqcfmbdmcjxlgpnjbbxgyhyyjgptzgzmqbqtcgyxjxlwzkydpdymgcftpfxyztzxdzxtgkmtybbclbjaskytssqyymszxfjewlxllszbqjjjaklylxlycctsxmcwfkkkbsxlllljyxtyltjyytdpjhnhnnkbyqnfqyyzbyyessessgdyhfhwtcjbsdzztfdmxhcnjzymqwsryjdzjqpdqbbstjggfbkjbxtgqhngwjxjgdllthzhhyyyyyysxwtyyyccbdbpypzycczyjpzywcbdlfwzcwjdxxhyhlhwzzxjtczlcdpxujczzzlyxjjtxphfxwpywxzptdzzbdzcyhjhmlxbqxsbylrdtgjrrcttthytczwmxfytwwzcwjwxjywcskybzscctzqnhxnwxxkhkfhtswoccjybcmpzzykbnnzpbzhhzdlsyddytyfjpxyngfxbyqxcbhxcpsxtyzdmkysnxsxlhkmzxlyhdhkwhxxsskqyhhcjyxglhzxcsnhekdtgzxqypkdhextykcnymyyypkqyyykxzlthjqtbyqhxbmyhsqckwwyllhcyylnneqxqwmcfbdccmljggxdqktlxkgnqcdgzjwyjjlyhhqtttnwchmxcxwhwszjydjccdbqcdgdnyxzthcqrxcbhztqcbxwgqwyybxhmbymyqtyexmqkyaqyrgyzslfykkqhyssqyshjgjcnxkzycxsbxyxhyylstycxqthysmgscpmmgcccccmtztasmgqzjhklosqylswtmxsyqkdzljqqyplsycztcqqpbbqjzclpkhqzyyxxdtddtsjcxffllchqxmjlwcjcxtspycxndtjshjwxdqqjskxyamylsjhmlalykxcyydmnmdqmxmcznncybzkkyflmchcmlhxrcjjhsylnmtjzgzgywjxsrxcwjgjqhqzdqjdcjjzkjkgdzqgjjyjylxzxxcdqhhheytmhlfsbdjsyyshfystczqlpbdrfrztzykywhszyqkwdqzrkmsynbcrxqbjyfazpzzedzcjywbcjwhyjbqszywryszptdkzpfpbnztklqyhbbzpnpptyzzybqnydcpjmmcycqmcyfzzdcmnlfpbplngqjtbttnjzpzbbznjkljqylnbzqhksjznggqszzkyxshpzsnbcgzkddzqanzhjkdrtlzlswjljzlywtjndjzjhxyayncbgtzcssqmnjpjytyswxzfkwjqtkhtzplbhsnjzsyzbwzzzzlsylsbjhdwwqpslmmfbjdwaqyztcjtbnnwzxqxcdslqgdsdpdzhjtqqpswlyyjzlgyxyzlctcbjtktyczjtqkbsjlgmgzdmcsgpynjzyqyyknxrpwszxmtncszzyxybyhyzaxywqcjtllckjjtjhgdxdxyqyzzbywdlwqcglzgjgqrqzczssbcrpcskydznxjsqgxssjmydnstztpbdltkzwxqwqtzexnqczgwezkssbybrtssslccgbpszqszlccglllzxhzqthczmqgyzqznmcocszjmmzsqpjygqljyjppldxrgzyxccsxhshgtznlzwzkjcxtcfcjxlbmqbczzwpqdnhxljcthyzlgylnlszzpcxdscqqhjqksxzpbajyemsmjtzdxlcjyryynwjbngzztmjxltbslyrzpylsscnxphllhyllqqzqlxymrsycxzlmmczltzsdwtjjllnzggqxpfskygyghbfzpdkmwghcxmsgdxjmcjzdycabxjdlnbcdqygskydqtxdjjyxmszqazdzfslqxyjsjzylbtxxwxqqzbjzufbblylwdsljhxjyzjwtdjczfqzqzzdzsxzzqlzcdzfjhyspympqzmlpplffxjjnzzylsjeyqzfpfzksywjjjhrdjzzxtxxglghydxcskyswmmzcwybazbjkshfhjcxmhfqhyxxyzftsjyzfxyxpzlchmzmbxhzzsxyfymncwdabazlxktcshhxkxjjzjsthygxsxyyhhhjwxkzxssbzzwhhhcwtzzzpjxsnxqqjgzyzywllcwxzfxxyxyhxmkyyswsqmnlnaycyspmjkhwcqhylajjmzxhmmcnzhbhxclxtjpltxyjhdyylttxfszhyxxsjbjyayrsmxyplckduyhlxrlnllstyzyyqygyhhsccsmzctzqxkyqfpyyrpfflkquntszllzmwwtcqqyzwtllmlmpwmbzsstzrbpddtlqjjbxzcsrzqqygwcsxfwzlxccrszdzmcyggdzqsgtjswljmymmzyhfbjdgyxccpshxnzcsbsjyjgjmppwaffyfnxhyzxzylremzgzcyzsszdlljcsqfnxzkptxzgxjjgfmyyysnbtylbnlhpfzdcyfbmgqrrssszxysgtzrnydzzcdgpjafjfzknzblczszpsgcycjszlmlrszbzzldlsllysxsqzqlyxzlskkbrxbrbzcycxzzzeeyfgklzlyyhgzsgzlfjhgtgwkraajyzkzqtsshjjxdcyzuyjlzyrzdqqhgjzxsszbykjpbfrtjxllfqwjhylqtymblpzdxtzygbdhzzrbgxhwnjtjxlkscfsmwlsdqysjtxkzscfwjlbxftzlljzllqblsqmqqcgczfpbphzczjlpyyggdtgwdcfczqyyyqyssclxzsklzzzgffcqnwglhqyzjjczlqzzyjpjzzbpdccmhjgxdqdgdlzqmfgpsytsdyfwwdjzjysxyyczcyhzwpbykxrylybhkjksfxtzjmmckhlltnyymsyxyzpyjqycsycwmtjjkqyrhllqxpsgtlyycljscpxjyzfnmlrgjjtyzbxyzmsjyjhhfzqmsyxrszcwtlrtqzsstkxgqkgsptgcznjsjcqcxhmxggztqydjkzdlbzsxjlhyqgggthqszpyhjhhgyygkggcwjzzylczlxqsftgzslllmljskctbllzzszmmnytpzsxqhjcjyqxyzxzqzcpshkzzysxcdfgmwqrllqxrfztlystctmjcxjjxhjnxtnrztzfqyhqgllgcxszsjdjljcydsjtlnyxhszxcgjzyqpylfhdjsbpcczhjjjqzjqdybssllcmyttmqtbhjqnnygkyrqyqmzgcjkpdcgmyzhqllsllclmholzgdyyfzsljcqzlylzqjeshnylljxgjxlysyyyxnbzljsszcqqcjyllzltjyllzllbnylgqchxyyxoxcxqkyjxxxyklxsxxyqxcykqxqcsgyxxyqxygytqohxhxpyxxxulcyeychzzcbwqbbwjqzscszsslzylkdesjzwmymcytsdsxxscjpqqsqylyyzycmdjdzywcbtjsydjkcyddjlbdjjsodzysyxqqyxdhhgqqyqhdyxwgmmmajdybbbppbcmuupljzsmtxerxjmhqnutpjdcbssmssstkjtssmmtrcplzszmlqdsdmjmqpnqdxcfynbfsdqxyxhyaykqyddlqyyysszbydslntfqtzqpzmchdhczcwfdxtmyqsphqyyxsrgjcwtjtzzqmgwjjtjhtqjbbhwzpxxhyqfxxqywyyhyscdydhhqmnmtmwcpbszppzzglmzfollcfwhmmsjzttdhzzyffytzzgzyskyjxqyjzqbhmbzzlyghgfmshpzfzsnclpbqsnjxzslxxfpmtyjygbxlldlxpzjyzjyhhzcywhjylsjexfszzywxkzjluydtmlymqjpwxyhxsktqjezrpxxzhhmhwqpwqlyjjqjjzszcphjlchhnxjlqwzjhbmzyxbdhhypzlhlhlgfwlchyytlhjxcjmscpxstkpnhqxsrtyxxtesyjctlsslstdlllwwyhdhrjzsfgxtsyczynyhtdhwjslhtzdqdjzxxqhgyltzphcsqfclnjtclzpfstpdynylgmjllycqhysshchylhqyqtmzypbywrfqykqsyslzdqjmpxyyssrhzjnywtqdfzbwwtwwrxcwhgyhxmkmyyyqmsmzhngcepmlqqmtcwctmmpxjpjjhfxyyzsxzhtybmstsyjttqqqyylhynpyqzlcyzhzwsmylkfjxlwgxypjytysyxymzckttwlksmzsylmpwlzwxwqzssaqsyxyrhssntsrapxcpwcmgdxhxzdzyfjhgzttsbjhgyzszysmyclllxbtyxhbbzjkssdmalxhycfygmqypjycqxjllljgslzgqlycjcczotyxmtmttllwtgpxymzmklpszzzxhkqysxctyjzyhxshyxzkxlzwpsqpyhjwpjpwxqqylxsdhmrslzzyzwttcyxyszzshbsccstplwsscjchnlcgchssphylhfhhxjsxyllnylszdhzxylsxlwzykcldyaxzcmddyspjtqjzlnwqpssswctstszlblnxsmnyymjqbqhrzwtyydchqlxkpzwbgqybkfcmzwpzllyylszydwhxpsbcmljbscgbhxlqhyrljxyswxwxzsldfhlslynjlzyflyjycdrjlfsyzfsllcqyqfgjyhyxzlylmstdjcyhbzllnwlxxygyyhsmgdhxxhhlzzjzxczzzcyqzfngwpylcpkpyypmclqkdgxzggwqbdxzzkzfbxxlzxjtpjpttbytszzdwslchzhsltyxhqlhyxxxyyzyswtxzkhlxzxzpyhgchkcfsyhutjrlxfjxptztwhplyxfcrhxshxkyxxyhzqdxqwulhyhmjtbflkhtxcwhjfwjcfpqryqxcyyyqygrpywsgsungwchkzdxyflxxhjjbyzwtsxxncyjjymswzjqrmhxzwfqsylzjzgbhynslbgttcsybyxxwxyhxyyxnsqyxmqywrgyqlxbbzljsylpsytjzyhyzawlrorjmksczjxxxyxchdyxryxxjdtsqfxlyltsffyxlmtyjmjuyyyxltzcsxqzqhzxlyyxzhdnbrxxxjctyhlbrlmbrllaxkyllljlyxxlycrylcjtgjcmtlzllcyzzpzpcyawhjjfybdyyzsmpckzdqyqpbpcjpdcyzmdpbcyydycnnplmtmlrmfmmgwyzbsjgygsmzqqqztxmkqwgxllpjgzbqcdjjjfpkjkcxbljmswmdtqjxldlppbxcwrcqfbfqjczahzgmykphyyhzykndkzmbpjyxpxyhlfpnyygxjdbkxnxhjmzjxstrstldxskzysybzxjlxyslbzyslhxjpfxpqnbylljqkygzmcyzzymccslclhzfwfwyxzmwsxtynxjhpyymcyspmhysmydyshqyzchmjjmzcaagcfjbbhplyzylxxsdjgxdhkxxtxxnbhrmlyjsltxmrhnlxqjxyzllyswqgdlbjhdcgjyqycmhwfmjybmbyjyjwymdpwhxqldygpdfxxbcgjspckrssyzjmslbzzjfljjjlgxzgyxyxlszqyxbexyxhgcxbpldyhwettwwcjmbtxchxyqxllxflyxlljlssfwdpzsmyjclmwytczpchqekcqbwlcqydplqppqzqfjqdjhymmcxtxdrmjwrhxcjzylqxdyynhyyhrslsrsywwzjymtltllgtqcjzyabtckzcjyccqljzqxalmzyhywlwdxzxqdllqshgpjfjljhjabcqzdjgtkhsstcyjlpswzlxzxrwgldlzrlzxtgsllllzlyxxwgdzygbdphzpbrlwsxqbpfdwofmwhlypcbjccldmbzpbzzlcyqxldomzblzwpdwyygdstthcsqsccrsssyslfybfntyjszdfndpdhdzzmbblslcmyffgtjjqwftmtpjwfnlbzcmmjtgbdzlqlpyfhyymjylsdchdzjwjcctljcldtljjcpddsqdsszybndbjlggjzxsxnlycybjxqycbylzcfzppgkcxzdzfztjjfjsjxzbnzyjqttyjyhtyczhymdjxttmpxsplzcdwslshxypzgtfmlcjtycbpmgdkwycyzcdszzyhflyctygwhkjyylsjcxgywjcbllcsnddbtzbsclyzczzssqdllmqyyhfslqllxftyhabxgwnywyypllsdldllbjcyxjzmlhljdxyyqytdlllbugbfdfbbqjzzmdpjhgclgmjjpgaehhbwcqxaxhhhzchxyphjaxhlphjpgpzjqcqzgjjzzuzdmqyybzzphyhybwhazyjhykfgdpfqsdlzmljxkxgalxzdaglmdgxmwzqyxxdxxpfdmmssympfmdmmkxksyzyshdzkxsysmmzzzmsydnzzczxfplstmzdnmxckjmztyymzmzzmsxhhdczjemxxkljstlwlsqlyjzllzjssdppmhnlzjczyhmxxhgzcjmdhxtkgrmxfwmcgmwkdtksxqmmmfzzydkmsclcmpcgmhspxqpzdsslcxkyxtwlwjyahzjgzqmcsnxyymmpmlkjxmhlmlqmxctkzmjqyszjsyszhsyjzjcdajzybsdqjzgwzqqxfkdmsdjlfwehkzqkjpeypzyszcdwyjffmzzylttdzzefmzlbnpplplpepszalltylkckqzkgenqlwagyxydpxlhsxqqwqcqxqclhyxxmlyccwlymqyskgchlcjnszkpyzkcqzqljpdmdzhlasxlbydwqlwdnbqcryddztjybkbwszdxdtnpjdtctqdfxqqmgnxeclttbkpwslctyqlpwyzzklpygzcqqpllkccylpqmzczqcljslqzdjxlddhpzqdljjxzqdxyzqkzljcyqdyjppypqykjyrmpcbymcxkllzllfqpylllmbsglcysslrsysqtmxyxzqzfdzuysyztffmzzsmzqhzssccmlyxwtpzgxzjgzgsjsgkddhtqggzllbjdzlcbchyxyzhzfywxyzymsdbzzyjgtsmtfxqyxqstdgslnxdlryzzlryylxqhtxsrtzngzxbnqqzfmykmzjbzymkbpnlyzpblmcnqyzzzsjzhjctzkhyzzjrdyzhnpxglfztlkgjtctssyllgzrzbbqzzklpklczyssuyxbjfpnjzzxcdwxzyjxzzdjjkggrsrjkmsmzjlsjywqskyhqjsxpjzzzlsnshrnypztwchklpsrzlzxyjqxqkysjycztlqzybbybwzpqdwwyzcytjcjxckcwdkkzxsgkdzxwwyyjqyytcytdllxwkczkklcclzcqqdzlqlcsfqchqhsfsmqzzlnbjjzbsjhtszdysjqjpdlzcdcwjkjzzlpycgmzwdjjbsjqzsyzyhhxjpbjydssxdzncglqmbtsfsbpdzdlznfgfjgfsmpxjqlmblgqcyyxbqkdjjqyrfkztjdhczklbsdzcfjtplljgxhyxzcsszzxstjygkgckgyoqxjplzpbpgtgyjzghzqzzlbjlsqfzgkqqjzgyczbzqtldxrjxbsxxpzxhyzyclwdxjjhxmfdzpfzhqhqmqgkslyhtycgfrzgnqxclpdlbzcsczqlljblhbzcypzzppdymzzsgyhckcpzjgsljlnscdsldlxbmstlddfjmkdjdhzlzxlszqpqpgjllybdszgqlbzlslkyyhzttntjyqtzzpszqztlljtyyllqllqyzqlbdzlslyyzymdfszsnhlxznczqzpbwskrfbsyzmthblgjpmczzlstlxshtcsyzlzblfeqhlxflcjlyljqcbzlzjhhsstbrmhxzhjzclxfnbgxgtqjcztmsfzkjmssnxljkbhsjxntnlzdntlmsjxgzjyjczxyjyjwrwwqnztnfjszpzshzjfyrdjsfszjzbjfzqzzhzlxfysbzqlzsgyftzdcszxzjbqmszkjrhyjzckmjkhchgtxkxqglxpxfxtrtylxjxhdtsjxhjzjxzwzlcqsbtxwxgxtxxhxftsdkfjhzyjfjxrzsdllltqsqqzqwzxsyqtwgwbzcgzllyzbclmqqtzhzxzxljfrmyzflxysqxxjkxrmqdzdmmyybsqbhgzmwfwxgmxlzpyytgzyccdxyzxywgsyjyznbhpzjsqsyxsxrtfyzgrhztxszzthcbfclsyxzlzqmzlmplmxzjxsflbyzmyqhxjsxrxsqzzzsslyfrczjrcrxhhzxqydyhxsjjhzcxzbtynsysxjbqlpxzqpymlxzkyxlxcjlcysxxzzlxdllljjyhzxgyjwkjrwyhcpsgnrzlfzwfzznsxgxflzsxzzzbfcsyjdbrjkrdhhgxjljjtgxjxxstjtjxlyxqfcsgswmsbctlqzzwlzzkxjmltmjyhsddbxgzhdlbmyjfrzfsgclyjbpmlysmsxlszjqqhjzfxgfqfqbpxzgyyqxgztcqwyltlgwsgwhrlfsfgzjmgmgbgtjfsyzzgzyzaflsspmlpflcwbjzcljjmzlpjjlymqdmyyyfbgygyzmlyzdxqyxrqqqhsyyyqxyljtyxfsfsllgnqcyhycwfhcccfxpylypllzyxxxxxkqhhxshjzcfzsczjxcpzwhhhhhapylqalpqafyhxdylukmzqgggddesrnnzltzgchyppysqjjhclljtolnjpzljlhymheydydsqycddhgzundzclzyzllzntnyzgslhslpjjbdgwxpcdutjcklkclwkllcasstkzzdnqnttlyyzssysszzryljqkcqdhhcrxrzydgrgcwcgzqfffppjfzynakrgywyqpqxxfkjtszzxswzddfbbxtbgtzkznpzzpzxzpjszbmqhkcyxyldkljnypkyghgdzjxxeahpnzkztzcmxcxmmjxnkszqnmnlwbwwxjkyhcpstmcsqtzjyxtpctpdtnnpglllzsjlspblplqhdtnjnlyyrszffjfqwdphzdwmrzcclodaxnssnyzrestyjwjyjdbcfxnmwttbylwstszgybljpxglboclhpcbjltmxzljylzxcltpnclckxtpzjswcyxsfyszdkntlbyjcyjllstgqcbxryzxbxklylhzlqzlnzcxwjzljzjncjhxmnzzgjzzxtzjxycyycxxjyyxjjxsssjstssttppgqtcsxwzdcsyfptfbfhfbblzjclzzdbxgcxlqpxkfzflsyltuwbmqjhszbmddbcysccldxycddqlyjjwmqllcsgljjsyfpyyccyltjantjjpwycmmgqyysxdxqmzhszxpftwwzqswqrfkjlzjqqyfbrxjhhfwjjzyqazmyfrhcyybyqwlpexcczstyrlttdmqlykmbbgmyyjprkznpbsxyxbhyzdjdnghpmfsgmwfzmfqmmbcmzzcjjlcnuxyqlmlrygqzcyxzlwjgcjcggmcjnfyzzjhycprrcmtzqzxhfqgtjxccjeaqcrjyhplqlszdjrbcqhqdyrhylyxjsymhzydwldfryhbpydtsscnwbxglpzmlzztqsscpjmxxycsjytycghycjwyrxxlfemwjnmkllswtxhyyyncmmcwjdqdjzglljwjrkhpzggflccsczmcbltbhbqjxqdspdjzzgkglfqywbzyzjltstdhqhctcbchflqmpwdshyytqwcnzzjtlbymbpdyyyxsqkxwyyflxxncwcxypmaelykkjmzzzbrxyyqjfljpfhhhytzzxsgqqmhspgdzqwbwpjhzjdyscqwzktxxsqlzyymysdzgrxckkujlwpysyscsyzlrmlqsyljxbcxtlwdqzpcycykpppnsxfyzjjrcemhszmsxlxglrwgcstlrsxbzgbzgztcplujlslylymtxmtzpalzxpxjtjwtcyyzlblxbzlqmylxpghdslssdmxmbdzzsxwhamlczcpjmcnhjysnsygchskqmzzqdllkablwjxsfmocdxjrrlyqzkjmybyqlyhetfjzfrfksryxfjtwdsxxsysqjyslyxwjhsnlxyyxhbhawhhjzxwmyljcsslkydztxbzsyfdxgxzjkhsxxybssxdpynzwrptqzczenygcxqfjykjbzmljcmqqxuoxslyxxlylljdzbtymhpfsttqqwlhokyblzzalzxqlhzwrrqhlstmypyxjjxmqsjfnbxyxyjxxyqylthylqyfmlkljtmllhszwkzhljmlhljkljstlqxylmbhhlnlzxqjhxcfxxlhyhjjgbyzzkbxscqdjqdsujzyyhzhhmgsxcsymxfebcqwwrbpyyjqtyzcyqyqqzyhmwffhgzfrjfcdpxntqyzpdykhjlfrzxppxzdbbgzqstlgdgylcqmlchhmfywlzyxkjlypqhsywmqqgqzmlzjnsqxjqsyjycbehsxfszpxzwfllbcyyjdytdthwzsfjmqqyjlmqxxlldttkhhybfpwtyysqqwnqwlgwdebzwcmygculkjxtmxmyjsxhybrwfymwfrxyqmxysztzztfykmldhqdxwyynlcryjblpsxcxywlsprrjwxhqyphtydnxhhmmywytzcsqmtssccdalwztcpqpyjllqzyjswxmzzmmylmxclmxczmxmzsqtzppqqblpgxqzhfljjhytjsrxwzxsccdlxtyjdcqjxslqyclzxlzzxmxqrjmhrhzjbhmfljlmlclqnldxzlllpypsyjysxcqqdcmqjzzxhnpnxzmekmxhykyqlxsxtxjyyhwdcwdzhqyybgybcyscfgpsjnzdyzzjzxrzrqjjymcanyrjtldppyzbstjkxxzypfdwfgzzrpymtngxzqbyxnbufnqkrjqzmjegrzgyclkxzdskknsxkcljspjyyzlqqjybzssqlllkjxtbktylccddblsppfylgydtzjyqggkqttfzxbdktyyhybbfytyybclpdytgdhryrnjsptcsnyjqhklllzslydxxwbcjqspxbpjzjcjdzffxxbrmlazhcsndlbjdszblprztswsbxbcllxxlzdjzsjpylyxxyftfffbhjjxgbyxjpmmmpssjzjmtlyzjxswxtyledqpjmygqzjgdjlqjwjqllsjgjgygmscljjxdtygjqjqjcjzcjgdzzsxqgsjggcxhqxsnqlzzbxhsgzxcxyljxyxyydfqqjhjfxdhctxjyrxysqtjxyefyyssyyjxncyzxfxmsyszxyyschshxzzzgzzzgfjdltylnpzgyjyzyyqzpbxqbdztzczyxxyhhsqxshdhgqhjhgywsztmzmlhyxgebtylzkqwytjzrclekystdbcykqqsayxcjxwwgsbhjyzydhcsjkqcxswxfltynyzpzcczjqtzwjqdzzzqzljjxlsbhpyxxpsxshheztxfptlqyzzxhytxncfzyyhxgnxmywxtzsjpthhgymxmxqzxtsbczyjyxxtyyzypcqlmmszmjzzllzxgxzaajzyxjmzxwdxzsxzdzxleyjjzqbhzwzzzqtzpsxztdsxjjjznyazphxyysrnqdthzhyykyjhdzxzlswclybzyecwcycrylcxnhzydzydyjdfrjjhtrsqtxyxjrjhojynxelxsfsfjzghpzsxzszdzcqzbyyklsgsjhczshdgqgxyzgxchxzjwyqwgyhksseqzzndzfkwysstclzstsymcdhjxxyweyxczaydmpxmdsxybsqmjmzjmtzqlpjyqzcgqhxjhhlxxhlhdldjqcldwbsxfzzyyschtytyybhecxhykgjpxhhyzjfxhwhbdzfyzbcapnpgnydmsxhmmmmamynbyjtmpxyymcthjbzyfcgtyhwphftwzzezsbzegpfmtskftycmhfllhgpzjxzjgzjyxzsbbqsczzlzccstpgxmjsftcczjzdjxcybzlfcjsyzfgszlybcwzzbyzdzypswyjzxzbdsyuxlzzbzfygczxbzhzftpbgzgejbstgkdmfhyzzjhzllzzgjqzlsfdjsscbzgpdlfzfzszyzyzsygcxsnxxchczxtzzljfzgqsqyxzjqdccztqcdxzjyqjqchxztdlgscxzsyqjqtzwlqdqztqchqqjzyezzzpbwkdjfcjpztypqyqttynlmbdktjzpqzqzzfpzsbnjlgyjdxjdzzkzgqkxdlpzjtcjdqbxdjqjstcknxbxzmslyjcqmtjqwwcjqnjnlllhjcwqtbzqydzczpzzdzyddcyzzzccjttjfzdprrtztjdcqtqzdtjnplzbcllctzsxkjzqzpzlbzrbtjdcxfczdbccjjltqqpldcgzdbbzjcqdcjwynllzyzccdwllxwzlxrxntqqczxkqlsgdfqtddglrlajjtkuymkqlltzytdyyczgjwyxdxfrskstqtenqmrkqzhhqkdldazfkypbggpzrebzzykzzspegjxgykqzzzslysyyyzwfqzylzzlzhwchkypqgnpgblplrrjyxccsyyhsfzfybzyytgzxylxczwxxzjzblfflgskhyjzeyjhlpllllczgxdrzelrhgklzzyhzlyqszzjzqljzflnbhgwlczcfjyspyxzlzlxgccpzbllcybbbbubbcbpcrnnzczyrbfsrldcgqyyqxygmqzwtzytyjxyfwtehzzjywlccntzyjjzdedpzdztsyqjhdymbjnyjzlxtsstphndjxxbyxqtzqddtjtdyytgwscszqflshlglbczphdlyzjyckwtytylbnytsdsycctyszyyebhexhqdtwnygyclxtszystqmygzazccszzdslzclzrqxyyeljsbymxsxztembbllyyllytdqyshymrqwkfkbfxnxsbychxbwjyhtqbpbsbwdzylkgzskyhxqzjxhxjxgnljkzlyycdxlfyfghljgjybxqlybxqpqgztzplncypxdjyqydymrbesjyyhkxxstmxrczzywxyqybmcllyzhqyzwqxdbxbzwzmslpdmyskfmzklzcyqyczlqxfzzydqzpzygyjyzmzxdzfyfyttqtzhgspczmlccytzxjcytjmkslpzhysnzllytpzctzzcktxdhxxtqcyfksmqccyyazhtjpcylzlyjbjxtpnyljyynrxsylmmnxjsmybcsysylzylxjjqyldzlpqbfzzblfndxqkczfywhgqmrdsxycytxnqqjzyypfzxdyzfprxejdgyqbxrcnfyyqpghyjdyzxgrhtkylnwdzntsmpklbthbpyszbztjzszzjtyyxzphsszzbzczptqfzmyflypybbjqxzmxxdjmtsyskkbjzxhjcklpsmkyjzcxtmljyxrzzqslxxqpyzxmkyxxxjcljprmyygadyskqlsndhyzkqxzyztcghztlmlwzybwsyctbhjhjfcwztxwytkzlxqshlyjzjxtmplpycgltbzztlzjcyjgdtclklpllqpjmzpapxyzlkktkdzczzbnzdydyqzjyjgmctxltgxszlmlhbglkfwnwzhdxuhlfmkyslgxdtwwfrjejztzhydxykshwfzcqshktmqqhtzhymjdjskhxzjzbzzxympagqmstpxlsklzynwrtsqlszbpspsgzwyhtlkssswhzzlyytnxjgmjszsufwnlsoztxgxlsammlbwldszylakqcqctmycfjbslxclzzclxxksbzqclhjpsqplsxxckslnhpsfqqytxyjzlqldxzqjzdyydjnzptuzdskjfsljhylzsqzlbtxydgtqfdbyazxdzhzjnhhqbyknxjjqczmlljzkspldyclbblxklelxjlbqycxjxgcnlcqplzlzyjtzljgyzdzpltqcsxfdmnycxgbtjdcznbgbqyqjwgkfhtnpyqzqgbkpbbyzmtjdytblsqmpsxtbnpdxklemyycjynzctldykzzxddxhqshdgmzsjycctayrzlpyltlkxslzcggexclfxlkjrtlqjaqzncmbydkkcxglczjzxjhptdjjmzqykqsecqzdshhadmlzfmmzbgntjnnlgbyjbrbtmlbyjdzxlcjlpldlpcqdhlxzlycblcxzzjadjlnzmmsssmybhbsqkbhrsxxjmxsdznzpxlgbrhwggfcxgmsklltsjyycqltskywyyhywxbxqywpywykqlsqptntkhqcwdqktwpxxhcpthtwumssyhbwcrwxhjmkmzngwtmlkfghkjylsyycxwhyeclqhkqhttqkhfzldxqwyzyydesbpkyrzpjfyyzjceqdzzdlatzbbfjllcxdlmjssxegygsjqxcwbxsszpdyzcxdnyxppzydlyjczpltxlsxyzyrxcyyydylwwnzsahjsyqyhgywwaxtjzdaxysrltdpssyyfnejdxyzhlxlllzqzsjnyqyqqxyjghzgzcyjchzlycdshwshjzyjxcllnxzjjyyxnfxmwfpylcyllabwddhwdxjmcxztzpmlqzhsfhzynztlldywlslxhymmylmbwwkyxyadtxylldjpybpwuxjmwmllsafdllyflbhhhbqqltzjcqjldjtffkmmmbythygdcqrddwrqjxnbysnwzdbyytbjhpybyttjxaahgqdqtmystqxkbtzpkjlzrbeqqssmjjbdjotgtbxpgbktlhqxjjjcthxqdwjlwrfwqgwshckryswgftgygbxsdwdwrfhwytjjxxxjyzyslpyyypayxhydqkxshxyxgskqhywfdddpplcjlqqeewxksyykdypltjthkjltcyyhhjttpltzzcdlthqkzxqysteeywyyzyxxyysttjkllpzmcyhqgxyhsrmbxpllnqydqhxsxxwgdqbshyllpjjjthyjkyppthyyktyezyenmdshlcrpqfdgfxzpsftljxxjbswyysksflxlpplbbblbsfxfyzbsjssylpbbffffsscjdstzsxzryysyffsyzyzbjtbctsbsdhrtjjbytcxyjeylxcbnebjdsyxykgsjzbxbytfzwgenyhhthzhhxfwgcstbgxklsxywmtmbyxjstzscdyqrcytwxzfhmymcxlznsdjtttxrycfyjsbsdyerxjljxbbdeynjghxgckgscymblxjmsznskgxfbnbpthfjaafxyxfpxmypqdtzcxzzpxrsywzdlybbktyqpqjpzypzjznjpzjlzzfysbttslmptzrtdxqsjehbzylzdhljsqmlhtxtjecxslzzspktlzkqqyfsygywpcpqfhqhytqxzkrsgttsqczlptxcdyyzxsqzslxlzmycpcqbzyxhbsxlzdltcdxtylzjyyzpzyzltxjsjxhlpmytxcqrblzssfjzztnjytxmyjhlhpplcyxqjqqkzzscpzkswalqsblcczjsxgwwwygyktjbbztdkhxhkgtgpbkqyslpxpjckbmllxdzstbklggqkqlsbkktfxrmdkbftpzfrtbbrferqgxyjpzsstlbztpszqzsjdhljqlzbpmsmmsxlqqnhknblrddnxxdhddjcyygylxgzlxsygmqqgkhbpmxyxlytqwlwgcpbmqxcyzydrjbhtdjyhqshtmjsbyplwhlzffnypmhxxhpltbqpfbjwqdbygpnztpfzjgsddtqshzeawzzylltyybwjkxxghlfkxdjtmszsqynzggswqsphtlsskmclzxyszqzxncjdqgzdlfnykljcjllzlmzznhydsshthzzlzzbbhqzwwycrzhlyqqjbeyfxxxwhsrxwqhwpslmsskzttygyqqwrslalhmjtqjsmxqbjjzjxzyzkxbyqxbjxshztsfjlxmxzxfghkzszggylclsarjyhslllmzxelglxydjytlfbhbpnlyzfbbhptgjkwetzhkjjxzxxglljlstgshjjyqlqzfkcgnndjsszfdbctwwseqfhqjbsaqtgypqlbxbmmywxgslzhglzgqyflzbyfzjfrysfmbyzhqgfwzsyfyjjphzbyyzffwodgrlmftwlbzgycqxcdjygzyyyytytydwegazyhxjlzyyhlrmgrxxzclhneljjtjtpwjybjjbxjjtjteekhwsljplpsfyzpqqbdlqjjtyyqlyzkdksqjyyqzldqtgjqyzjsucmryqthtejmfctyhypkmhyzwjdqfhyyxwshctxrljhqxhccyyyjltkttytmxgtcjtzayyoczlylbszywjytsjyhbyshfjlygjxxtmzyyltxxypzlxyjzyzyypnhmymdyylblhlsyyqqllnjjymsoyqbzgdlyxylcqyxtszegxhzglhwbljheyxtwqmakbpqcgyshhegqcmwyywljyjhyyzlljjylhzyhmgsljljxcjjyclycjpcpzjzjmmylcqlnqljqjsxyjmlszljqlycmmhcfmmfpqqmfylqmcffqmmmmhmznfhhjgtthhkhslnchhyqdxtmmqdcyzyxyqmyqyltdcyyyzazzcymzydlzfffmmycqzwzzmabtbyztdmnzzggdftypcgqyttssffwfdtzqssystwxjhxytsxxylbyqhwwkxhzxwznnzzjzjjqjccchyyxbzxzcyztllcqxynjycyycynzzqyyyewyczdcjycchyjlbtzyycqwmpwpymlgkdldlgkqqbgychjxy";
    //此处收录了375个多音字,数据来自于http://www.51window.net/page/pinyin


    protected $oMultiDiff = array(
        "19969" => "dz",
        "19975" => "wm",
        "19988" => "qj",
        "20048" => "yl",
        "20056" => "sc",
        "20060" => "nm",
        "20094" => "qg",
        "20127" => "jq",
        "20167" => "qc",
        "20193" => "yg",
        "20250" => "hk",
        "20256" => "zc",
        "20282" => "sc",
        "20285" => "qjg",
        "20291" => "td",
        "20314" => "yd",
        "20340" => "ne",
        "20375" => "td",
        "20389" => "jy",
        "20391" => "cz",
        "20415" => "bp",
        "20446" => "ys",
        "20447" => "sq",
        "20504" => "tc",
        "20608" => "kg",
        "20854" => "qj",
        "20911" => "pf",
        "20857" => "zc",
        "20985" => "aw",
        "21032" => "pb",
        "21048" => "xq",
        "21049" => "sc",
        "21089" => "ys",
        "21119" => "jc",
        "21242" => "sb",
        "21273" => "sc",
        "21305" => "yp",
        "21306" => "qo",
        "21330" => "zc",
        "21333" => "dsc",
        "21345" => "kq",
        "21378" => "ca",
        "21397" => "sc",
        "21414" => "xs",
        "21442" => "sc",
        "21477" => "jg",
        "21480" => "td",
        "21484" => "zs",
        "21494" => "yx",
        "21505" => "yx",
        "21512" => "hg",
        "21523" => "xh",
        "21537" => "pb",
        "21542" => "fp",
        "21549" => "kh",
        "21571" => "e",
        "21574" => "da",
        "21588" => "td",
        "21589" => "o",
        "21618" => "zc",
        "21621" => "kha",
        "21632" => "zj",
        "21654" => "kg",
        "21679" => "lkg",
        "21683" => "kh",
        "21710" => "a",
        "21719" => "yh",
        "21734" => "woe",
        "21769" => "a",
        "21780" => "wn",
        "21804" => "xh",
        "21834" => "a",
        "21899" => "zd",
        "21903" => "rn",
        "21908" => "wo",
        "21939" => "zc",
        "21956" => "sa",
        "21964" => "ya",
        "21970" => "td",
        "22003" => "a",
        "22031" => "jg",
        "22040" => "xs",
        "22060" => "zc",
        "22066" => "zc",
        "22079" => "mh",
        "22129" => "xj",
        "22179" => "xa",
        "22237" => "nj",
        "22244" => "td",
        "22280" => "qj",
        "22300" => "yh",
        "22313" => "xw",
        "22331" => "yq",
        "22343" => "yj",
        "22351" => "ph",
        "22395" => "dc",
        "22412" => "td",
        "22484" => "pb",
        "22500" => "pb",
        "22534" => "zd",
        "22549" => "dh",
        "22561" => "pb",
        "22612" => "td",
        "22771" => "kq",
        "22831" => "hb",
        "22841" => "jg",
        "22855" => "qj",
        "22865" => "xq",
        "23013" => "ml",
        "23081" => "wm",
        "23487" => "sx",
        "23558" => "qj",
        "23561" => "wy",
        "23586" => "yw",
        "23614" => "wy",
        "23615" => "sn",
        "23631" => "pb",
        "23646" => "sz",
        "23663" => "zt",
        "23673" => "yg",
        "23762" => "td",
        "23769" => "zs",
        "23780" => "qj",
        "23884" => "qk",
        "24055" => "xh",
        "24113" => "dc",
        "24162" => "zc",
        "24191" => "ga",
        "24273" => "qj",
        "24324" => "nl",
        "24377" => "td",
        "24378" => "qj",
        "24439" => "pf",
        "24554" => "sz",
        "24683" => "td",
        "24694" => "we",
        "24733" => "lk",
        "24925" => "tn",
        "25094" => "zg",
        "25100" => "xq",
        "25103" => "xh",
        "25153" => "pb",
        "25170" => "pb",
        "25179" => "kg",
        "25203" => "pb",
        "25240" => "zs",
        "25282" => "fb",
        "25303" => "na",
        "25324" => "kg",
        "25341" => "zy",
        "25373" => "wz",
        "25375" => "xj",
        "25384" => "a",
        "25457" => "a",
        "25528" => "sd",
        "25530" => "sc",
        "25552" => "td",
        "25774" => "zc",
        "25874" => "zc",
        "26044" => "yw",
        "26080" => "wm",
        "26292" => "pb",
        "26333" => "bp",
        "26355" => "zy",
        "26366" => "cz",
        "26397" => "zc",
        "26399" => "qj",
        "26415" => "zs",
        "26451" => "sb",
        "26526" => "zc",
        "26552" => "jg",
        "26561" => "td",
        "26588" => "jg",
        "26597" => "cz",
        "26629" => "zs",
        "26638" => "yl",
        "26646" => "qx",
        "26653" => "kg",
        "26657" => "xj",
        "26727" => "hg",
        "26894" => "zc",
        "26937" => "zs",
        "26946" => "zc",
        "26999" => "kj",
        "27099" => "kj",
        "27449" => "yq",
        "27481" => "xs",
        "27542" => "zs",
        "27663" => "zs",
        "27748" => "ts",
        "27784" => "sc",
        "27788" => "zd",
        "27795" => "td",
        "27812" => "o",
        "27850" => "pb",
        "27852" => "mb",
        "27895" => "sl",
        "27898" => "pl",
        "27973" => "qj",
        "27981" => "kh",
        "27986" => "hx",
        "27994" => "xj",
        "28044" => "yc",
        "28065" => "wg",
        "28177" => "sm",
        "28267" => "qj",
        "28291" => "kh",
        "28337" => "zq",
        "28463" => "tl",
        "28548" => "dc",
        "28601" => "td",
        "28689" => "pb",
        "28805" => "jg",
        "28820" => "qg",
        "28846" => "pb",
        "28952" => "td",
        "28975" => "zc",
        "29100" => "a",
        "29325" => "qj",
        "29575" => "sl",
        "29602" => "fb",
        "30010" => "td",
        "30044" => "cx",
        "30058" => "pf",
        "30091" => "ysp",
        "30111" => "yn",
        "30229" => "xj",
        "30427" => "sc",
        "30465" => "sx",
        "30631" => "yq",
        "30655" => "qj",
        "30684" => "jqg",
        "30707" => "sd",
        "30729" => "xh",
        "30796" => "lg",
        "30917" => "pb",
        "31074" => "nm",
        "31085" => "jz",
        "31109" => "sc",
        "31181" => "zc",
        "31192" => "mlb",
        "31293" => "jq",
        "31400" => "yx",
        "31584" => "yj",
        "31896" => "zn",
        "31909" => "zy",
        "31995" => "xj",
        "32321" => "pf",
        "32327" => "zy",
        "32418" => "hg",
        "32420" => "xq",
        "32421" => "hg",
        "32438" => "lg",
        "32473" => "gj",
        "32488" => "td",
        "32521" => "jq",
        "32527" => "pb",
        "32562" => "zsq",
        "32564" => "jz",
        "32735" => "zd",
        "32793" => "pb",
        "33071" => "pf",
        "33098" => "xl",
        "33100" => "ya",
        "33152" => "pb",
        "33261" => "cx",
        "33324" => "bp",
        "33333" => "td",
        "33406" => "ay",
        "33426" => "wm",
        "33432" => "pb",
        "33445" => "jg",
        "33486" => "zn",
        "33493" => "ts",
        "33507" => "qj",
        "33540" => "qj",
        "33544" => "zc",
        "33564" => "xq",
        "33617" => "yt",
        "33632" => "qj",
        "33636" => "xh",
        "33637" => "yx",
        "33694" => "gw",
        "33705" => "pf",
        "33728" => "yw",
        "33882" => "sr",
        "34067" => "mw",
        "34074" => "yw",
        "34121" => "qj",
        "34255" => "zc",
        "34259" => "xl",
        "34425" => "hj",
        "34430" => "xh",
        "34485" => "kh",
        "34503" => "ys",
        "34532" => "hg",
        "34552" => "xs",
        "34558" => "ye",
        "34593" => "zl",
        "34660" => "yq",
        "34892" => "xh",
        "34928" => "sc",
        "34999" => "qj",
        "35048" => "pb",
        "35059" => "sc",
        "35098" => "zc",
        "35203" => "tq",
        "35265" => "jx",
        "35299" => "jx",
        "35782" => "sz",
        "35828" => "ys",
        "35830" => "e",
        "35843" => "td",
        "35895" => "gy",
        "35977" => "mh",
        "36158" => "jg",
        "36228" => "qj",
        "36426" => "xq",
        "36466" => "dc",
        "36710" => "cj",
        "36711" => "zyg",
        "36767" => "pb",
        "36866" => "sk",
        "36951" => "yw",
        "37034" => "yx",
        "37063" => "xh",
        "37218" => "zc",
        "37325" => "zc",
        "38063" => "pb",
        "38079" => "td",
        "38085" => "qy",
        "38107" => "dc",
        "38116" => "td",
        "38123" => "yd",
        "38224" => "hg",
        "38241" => "xtc",
        "38271" => "zc",
        "38415" => "ye",
        "38426" => "kh",
        "38461" => "yd",
        "38463" => "ae",
        "38466" => "pb",
        "38477" => "xj",
        "38518" => "yt",
        "38551" => "wk",
        "38585" => "zc",
        "38704" => "xs",
        "38739" => "lj",
        "38761" => "gj",
        "38808" => "sq",
        "39048" => "jg",
        "39049" => "xj",
        "39052" => "hg",
        "39076" => "cz",
        "39271" => "xt",
        "39534" => "td",
        "39552" => "td",
        "39584" => "pb",
        "39647" => "sb",
        "39730" => "lg",
        "39748" => "tpb",
        "40109" => "zq",
        "40479" => "nd",
        "40516" => "hg",
        "40536" => "hg",
        "40583" => "qj",
        "40765" => "yq",
        "40784" => "qj",
        "40840" => "yk",
        "40863" => "gqj"
    );

    public function getPY($str)
    {
        $str = (string)$str;
        if (!is_string($str))
            throw new \XYException(__METHOD__,-20004);
        // log_("str_getPY",$str,$this);

        $str = $this->trimAll($str);

        $arrResult = '';//保存中间结果的数组
        $strlen    = mb_strlen($str,'utf-8');
        for ($i = 0; $i < $strlen; $i++)
        {
            $ch = mb_substr($str,$i,1,'utf-8');
            $arrResult .= $this->checkCh($ch);//检查该unicode码是否在处理范围之内,在则返回该码对映汉字的拼音首字母,不在则调用其它函数处理
        }

        return $arrResult;
    }

    protected function checkCh($ch)
    {
        $uni = $this->charCodeAt($ch);
        //如果不在汉字处理范围之内,返回原字符,也可以调用自己的处理函数
         if ($uni > 40869 || $uni < 19968)
            return $ch;
        //检查是否是多音字,是按多音字处理,不是就直接在$strChineseFirstPY字符串中找对应的首字母
        //这里多音字统一返回第一个字母
        if (isset($this->oMultiDiff[$uni]))
        {
            // log_("duoyinzi",$uni,$this);
            // log_("this->oMultiDiff[$uni]",$this->oMultiDiff[$uni],$this);
            // log_("substr1",substr($this->oMultiDiff[$uni],0,1),$this);
            // log_("substr2",mb_substr($this->oMultiDiff[$uni],0,1,'UTF-8'),$this);
            return mb_substr($this->oMultiDiff[$uni],0,1,'UTF-8');
        }
        else
        {
            // log_("danyinzi",$uni,$this);
            // log_("substr1",substr($this->strChineseFirstPY,$uni-19968,1),$this);
            // log_("substr2",mb_substr($this->strChineseFirstPY,$uni-19968,1,'UTF-8'),$this);
            return mb_substr($this->strChineseFirstPY,$uni-19968,1,'UTF-8');
        }
        // return ($this->oMultiDiff[$uni] ? $this->oMultiDiff[$uni][0] : ($this->strChineseFirstPY[$uni - 19968]));
    }

    //返回字符的 Unicode 编码。这个返回值是 0 - 65535 之间的整数。
    //js中方法 charCodeAt() 与 charAt() 方法执行的操作相似，只不过前者返回的是位于指定位置的字符的编码，而后者返回的是字符子串。
    protected function charCodeAt($char)
    {
        $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
        return hexdec(bin2hex($ret));
    }


    // 去掉所有的空格
    protected function trimAll($str)
    {
        $search = array(" ","　","\n","\r","\t");
        $replace = array("","","","","");
        return str_replace($search, $replace, $str);
    }
}