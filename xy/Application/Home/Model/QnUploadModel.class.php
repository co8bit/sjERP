<?php
namespace Home\Model;
require dirname(__FILE__).'/../../../vendor/autoload.php';
use Think\Model;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/7/18
 * Time: 上午11:58
 */

class QnUploadModel extends BaseadvModel
{
    /**
     * 图片上传接口
     * @param $imgDataBase64 图片用base64加密之后的数据
     * @param $name 唯一识别码(图片名,不带后缀)
     * @return mixed
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function uploadImgToQiniu($imgDataBase64 = null,$name = null)
    {
        $base64_image = str_replace(' ', '+', $imgDataBase64);
        //匹配图片格式,在本地暂存
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result))
            $type = $result[2];
        if (!($type == 'png'||$type == 'jpg'))
            throw new \XYException(__METHOD__,-30003);
        $filePath = APP_PATH."../Public/Uploads/";
        if(!file_exists($filePath))
        {
            mkdir($filePath, 0777);
        }
        $fileName = $filePath.getUid().time().'.'.$type;
        if (!file_put_contents($fileName, base64_decode(str_replace($result[1], '', $base64_image))))
            throw new \XYException(__METHOD__,-30002);
        #TODO 储存成功以后的回调
//        $policy = array(
//            'callbackUrl' => 'http://112.112.112.112/callback.php',
//            'callbackBody' => 'filename=$(fname)&filesize=$(fsize)'
//        );
        //上传到七牛
        $accessKey = C('accessKey');
        $secretKey = C('secretKey');
        log_('accessKey-----',$accessKey);
        log_('secretKey-----',$secretKey);
        $auth = new Auth($accessKey, $secretKey);
        $bucket = 'xingyunbooks';
        $token = $auth->uploadToken($bucket);
        $key = $name.'.'.$type;
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $fileName);
        if ($err !== null)
        {
            log_('七牛存储失败数据',$err);
            throw new \XYException(__METHOD__,-30001);
        }
        //删除无用的对象
        unset($auth);
        unset($uploadMgr);
        //删除暂存文件
        if (file_exists($fileName))
            unlink($fileName);
        return $ret;
    }

    /**
     * 构造图片下载的URL
     * @param $domain
     * @param $key 唯一识别码(图片名,带后缀)
     * @return string $authUrl 完整的url
     * @author DizzyLee<728394036@qq.com>
     */
    public function getImgUrlByKey($domain,$key)
    {
        $accessKey = C('Access_Key');
        $secretKey = C('Secret_Key');
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        //baseUrl构造成私有空间的域名/key的形式
        $baseUrl = 'http://'.$domain.'/'.$key;
        $authUrl = $auth->privateDownloadUrl($baseUrl);
        if (!$authUrl)
            throw new \XYException(__METHOD__,-30004);
        return $authUrl;
    }

    public function qiNiuCallBack()
    {
        return 1;
    }
}