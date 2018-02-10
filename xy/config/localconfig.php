<?php
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/6/6
 * Time: 下午5:50
 */
return array(
    //每个后台开发者需要修改的参数
    'HOST_NAME'                      => '杭州跃迁科技有限公司',//发送日志、发送邮件的时候带的主机标识符，一般填[自己的名字-设备】
    'LOG_RECORD_METHOD'              => 'File',//Log日志写入磁盘的方法，可选值：,File,JsonFile （把文件写成json）
    'LOG_RECORD_FORMAT'              => 'html',//Log日志记录的格式，可选值：txt（单行输出）,html
    'INSTANCE_GROUP'                 => 'dev',//实例组，具体如下:
    //dev-开发环境，日常开发可以用此。
    //online-线上【只有正式服务器才可以用此名】
    //test-测试【只有测试服务器才可以用此名】
    //release-x.x【预发布分支，例如release-1.4】
    //hotfix【热修复】
    'INSTANCE_ID'    => '',//实例id
    'log_path'       => APP_PATH . '../Runtime/Logs/Home/',
    'UploadRootPath' => APP_PATH.'../Public/Uploads',

    'ROOT_PATH'=>'http://www.xingyunbooks.com', //域名地址
);