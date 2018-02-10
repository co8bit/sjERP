<?php
namespace Home\Controller;
use Think\Controller;

define('INSTALL_APP_PATH', realpath('./') . '/');

/**
 * 安装程序.
 * 部署需删除
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 * 
 * @note: 这里没有权限控制，任何人都可以访问
 */
class InstallController extends BaseController
{
	/**
	 * 首页，测试用
     * @internal
	 */
    public function index()
    {
        $this->show('<a href="'.U("Install/updateSQL").'">update SQL</a><br>');
        $this->display();
    	echo 'uid:'.session("user_auth.uid")." || username:".session("user_auth.username")." || name:".session("user_auth.name")." || admin_uid:".session("user_auth.admin_uid")."<br>";

        echo '<br><br><br>目录、文件读写检测：<br>';dump($this->check_dirfile());
        echo '函数支持检测：<br>';dump($this->check_func());
        echo 'phpinfo检测：<br>';$this->show('<a href="'.U("Install/phpinfo").'">phpinfo</a><br>');
    }



    /**
     * 更新数据库里的表
     * @internal
     *
     * @param string $sql_file 要加载的sql文件名。默认路径是项目根目录
     */
    public function updateSQL($sql_file)
    {
        if (IS_CLI)
            echo "\n"."\n===================".date('Y-m-d H:i:s')."开始更新数据库SQL==================="."\n";
        else
        {
            echo "<br>===================".date('Y-m-d H:i:s')."开始更新数据库SQL===================<br>";
        }

        $db = array(
                'DB_TYPE' =>C('DB_TYPE'),
                'DB_PORT' =>C('DB_PORT'),
                'DB_HOST' =>C('DB_HOST'),
                'DB_NAME' =>C('DB_NAME'),
                'DB_USER' =>C('DB_USER'),
                'DB_PWD'  =>C('DB_PWD'),
            );
        $link  =  mysql_connect($db['DB_HOST'], $db['DB_USER'], $db['DB_PWD']);
        if (!$link)
        {
            $this->error('数据库连接失败');
        }

        $database = mysql_select_db($db['DB_NAME'], $link);
        if (!$database)
        {
            exit('本程序只能在创建过一次用户及数据库的情况下使用');
        }

        $this->createTables($link,$sql_file);

        if (IS_CLI)
            echo "===================".date('Y-m-d H:i:s')."结束更新数据库SQL===================\n"."\n";
        else
            echo "<br>===================".date('Y-m-d H:i:s')."结束更新数据库SQL===================<br>";
    }


    public function auto_updateSQL_generator()
    {
        $sql_file = 'xy.sql';
        $this->display();
        $this->updateSQL($sql_file);
        A('Generator')->generator();
    }

    public function createRealDataForVisitor()
    {
        $this->display();
        $this->updateSQL();
        A('RealDataGenerator')->generator();
    }

    /**
     * 创建数据表
     * @param  resource $db 数据库连接资源
     */
    protected function createTables($link,$sql_file)
    {
        if (IS_CLI)
            $endString = "\n";
        else
            $endString = '<br>';

        $sql = file_get_contents(APP_PATH .'../'. $sql_file);
        // $sql = mb_convert_encoding($sql, "UTF-8", "utf-8");
        // echo $sql2;
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql);
       
        foreach ($sql as $value)
        {
            $value = trim($value);
            if(empty($value)) continue;
            if(substr($value, 0, 12) == 'CREATE TABLE')
            {
                $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
                $msg  = "更新数据表{$name}";
                if(false !==  mysql_query($value, $link))
                {
                    echo $msg . '.....ok'.$endString;
                }
                else
                {
                    echo $msg . '.....<b><font color=red>false</font></b>'.$endString;
                }
            }
            else
                // echo $value;
                mysql_query($value, $link);
        }
    }



    /**
     * 目录，文件读写检测
     * @internal
     * @return array 检测数据
     */
    protected function check_dirfile(){
        $items = array(
            array('dir',  '可写', 'success', './Uploads'),
            array('dir',  '可写', 'success', './Runtime'),
            array('dir', '可写', 'success', './Application/User/Conf'),
            array('file', '可写', 'success', './Application/Common/Conf'),

        );

        foreach ($items as &$val) {
            $item = INSTALL_APP_PATH . $val[3];
            if('dir' == $val[0]){
                if(!is_writable($item)) {
                    if(is_dir($items)) {
                        $val[1] = '可读';
                        $val[2] = 'error';
                        session('error', true);
                    } else {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                        session('error', true);
                    }
                }
            } else {
                if(file_exists($item)) {
                    if(!is_writable($item)) {
                        $val[1] = '不可写';
                        $val[2] = 'error';
                        session('error', true);
                    }
                } else {
                    if(!is_writable(dirname($item))) {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                        session('error', true);
                    }
                }
            }
        }

        return $items;
    }

    public function phpinfo()
    {
        phpinfo();
    }



    /**
     * 函数检测
     * @internal
     * @return array 检测数据
     */
    protected function check_func(){
        $items = array(
            array('pdo','支持','success','类'),
            array('pdo_mysql','支持','success','模块'),
            array('file_get_contents', '支持', 'success','函数'),
            array('mb_strlen',         '支持', 'success','函数'),
        );

        foreach ($items as &$val) {
            if(('类'==$val[3] && !class_exists($val[0]))
                || ('模块'==$val[3] && !extension_loaded($val[0]))
                || ('函数'==$val[3] && !function_exists($val[0]))
                ){
                $val[1] = '不支持';
                $val[2] = 'error';
                session('error', true);
            }
        }

        return $items;
    }

}