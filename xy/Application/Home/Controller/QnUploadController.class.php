<?php
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/7/18
 * Time: 下午3:26
 */

namespace Home\Controller;
use Think\Controller;

class QnUploadController extends HomeController
{
    public function uploadImgToQiniu()
    {
        try
        {
            $this->jsonReturn(D("QnUpload")->uploadImgToQiniu(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
}