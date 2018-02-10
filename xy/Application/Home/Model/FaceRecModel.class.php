<?php
namespace Home\Model;
use Think\Model;

/**
 * 向我们付钱时的类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class FaceRecModel extends BaseadvModel
{
	protected $api_key = 'JYgGw79lmONlOeSwiTzmzHVW8WjkxJ-W';
	protected $api_secret = 'bLW0zOIrGCBFZwXVFDHr12OTI7zGoBFS';
	/* 自动验证 */
	protected $_validate = array(
		//FaceRecModel_upload_pic
		array('cid', 'isUnsignedInt',-15002,
			self::MUST_VALIDATE, 'function',self::FaceRecModel_upload_pic), //cid不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		//FaceRecModel
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
		array('reg_time', NOW_TIME, self::MODEL_BOTH),
	);


    /**
     * 上传用户图片
     *
     * @api 
     *
     * @param mix|null $data POST的数据
     * @param string $pic_load 文件路径 
     * @param string $name 公司名称
     * @param unsigned_int $cid 公司id
     * 
     * @author wtt <wtt@xingyunbooks.com>
     * @version 1.11
     * @date    2017-07-17    
     */
    public  function upload_pic(array $data,$isAlreadyStartTrans = false)
    {
    	try
    	{
    		if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
	    	if(!$this->field('cid,name')->create($data,self::FaceRecModel_upload_pic))
	    		throw new \XYException(__METHOD__,$this->getError());
	    	// //上传图片
	    	// $upload = new \Think\Upload();// 实例化上传类
		    // $upload->maxSize   =     3145728 ;// 设置附件上传大小
		    // $upload->exts      =     array('jpg', 'png', 'jpeg');// 设置附件上传类型
		    // $upload->rootPath  =     './Public/Uploads/'; // 设置附件上传根目录
		    // $upload->savePath  =     'photo/'; // 设置附件上传（子）目录
		    // // 上传文件 
		    // $info   =   $upload->uploadOne($_FILES['photo']);
		    // if(!$info) {// 上传错误提示错误信息
		    //     $this->error($upload->getError());
		    // }
		    // else
		    // {
		        // //更新数据库数据
		        // //拼接文件路径
		        // log_("info",$info,$this);
		        // $path = './Public/Uploads/'.$info['savepath'].$info['savename'];
		        // log_("path",$path,$this);
		        // $this->photo = $path;
		        // log_("this->data",$this->data,$this);
		        // //插入数据
		        // $this->add();
		        $cid = $this->cid;
	    		$path = $data['fileName'];
	    		// log_('path',$path,$this);

		        //创建facetoken
		       	$re = $this->detect($path);
	        	// log_("re",$re,$this);
	       		$face_token = $re['faces'][0]['face_token'];

	       		//设置face_token 对应的user_id
	       		// log_("face_token",$face_token,$this);
	       		// log_("cid",$cid,$this);
	       		$re = $this->setUserId($face_token,$cid);
	       		// log_("re",$re,$this);
		        //判断客户是否已有faceset
		        $tmp = M('User')->where(array('admin_uid'=>getAdminUid()))->find();
		        // log_("tmp",$tmp,$this);
		        if( empty($tmp['outer_id']) )
		        {
		        	//创建faceset
		        	$res = $this->createFaceSet(array(
		        	'face_token' => $face_token
		        	));

		        	//更新user表中的faceset标识
					$tmp = M('User')->where('admin_uid = '.getAdminUid()) ->setField('outer_uid',$res['outer_id']);
		        }
		        else
		        {
		        	//在已有的faceset中添加facetoken 
		        	$this->addFace(array(
		        	'face_token' => $face_token,
		        	'outer_id'   => $tmp['outer_id']
		        	));
		        }
		    // }

		   	if (!$isAlreadyStartTrans) $this->commit(__METHOD__);

    	}
    	catch(\XYException $e)
    	{
    		if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
    		$e->rethrows();
    	}
    	catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
    	{
    		if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
    		throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
    	}
    	
    }

    /**
     * 创建face_token
     *
     * @internal 
     *
     * @param mix|null $data POST的数据
     * @param string $pic_load 文件路径 
     * 
     * @author wtt <wtt@xingyunbooks.com>
     * @version 1.11
     * @date    2017-07-17    
     */
    protected function detect( $path)
    {
    	$image = $path;
    	$fp = fopen($image,'rb');
    	$content = fread($fp,filesize($image));
    	$url = 'https://api-cn.faceplusplus.com/facepp/v3/detect';
    	$data = array(
    		'image_file";filename="image'=>"$content",
    		// 'image_base64' =>  $image_base64,
    		'api_key'=> $this->api_key,
    		'api_secret'=> $this->api_secret
    		);
    	$res = $this->http_request($url,$data);
		// log_("res1",$res,$this);
		return $res;
    }

	 /**
	 * 根据admin_uid创建人脸集合
	 *
	 * @internal 
	 *
	 * @param mix|null $data POST的数据
	 * @param string $face_token  需要添加的face_token
	 * 
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-07-17    
	 */
	protected function createFaceSet(array $data)
	{
		$face_token = $data['face_token'];
		$url = "https://api-cn.faceplusplus.com/facepp/v3/faceset/create";
		$data = array(
	        	'face_tokens'=>"$face_token", 
	        	'outer_id' => getAdminUid(),
	        	'api_key'=> $this->api_key,
	        	'api_secret'=> $this->api_secret //输入api_key和api_secret
	        	);
		$res = $this->http_request($url,$data);
		// log_("res2",$res,$this);
		return $res;
	}

	/**
	 * 设置user_id
	 * @internal
	 *
	 * string $face_token 客户需要识别的照片生成的face_token
	 * string $cid 客户对应的cid
	 * 
	 * @return array $res
	 * @author wtt <wtt@xingyunbooks.com>
	 * @date 2017-07-18
	 */
	protected function setUserId($face_token,$cid)
	{
		$url = "https://api-cn.faceplusplus.com/facepp/v3/face/setuserid";
		// log_("cid",$cid,$this);
		$data = array(
			'face_token' => $face_token,
    		'api_key'=> $this->api_key,
	        'api_secret'=> $this->api_secret, //输入api_key和api_secret
    		'user_id' => $cid
			);
		$res = $this->http_request($url, $data);
		// log_("res5",$res,$this);
	}


	/**
	 * 添加人脸集合
	 *
	 * @internal 
	 *
	 * @param mix|null $data POST的数据
	 * @param string $outer_id 商家faceset 对应的outer_id
	 * @param string $face_token  需要添加的face_token
	 * 
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-07-17    
	 */
	protected function addFace(array $data)
	{
		//查看所有的faceset
		$url1 = 'https://api-cn.faceplusplus.com/facepp/v3/faceset/getfacesets';
		$data1 = array(
			'api_key'=> $this->api_key,
	        'api_secret'=> $this->api_secret //输入api_key和api_secret
			);
		$redata = $this->http_request($url1, $data1);
		// log_("redata",$redata,$this);
		$face_token = $data['face_token'];
		$outer_id = $data['outer_id'];
		$url = "https://api-cn.faceplusplus.com/facepp/v3/faceset/addface";
		$data = array(
	        	'face_tokens'=>"$face_token", 
	        	'outer_id' => $outer_id,
	        	'api_key'=> $this->api_key,
	        	'api_secret'=> $this->api_secret //输入api_key和api_secret
	        	);
		$res = $this->http_request($url,$data);
		// log_("res3",$res,$this);
	}

	/**
	 * curl 请求
	 * $url 请求的url地址
	 * $data 需要传的数据
	 * @internal 
	 */
	protected function http_request($url,$data)
	{
		//创建curl 对象
		$ch = curl_init();
		//设置参数
		curl_setopt_array($ch,array(
			CURLOPT_URL => $url,  
			CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_ENCODING => "",
	        CURLOPT_MAXREDIRS => 10,
	        CURLOPT_TIMEOUT => 30,
	        CURLOPT_SSL_VERIFYPEER => false,
	        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	        CURLOPT_CUSTOMREQUEST => "POST",
	        CURLOPT_POSTFIELDS => $data,  //要通过post传递的参数
	        CURLOPT_HTTPHEADER => array("cache-control: no-cache",)
			));
		//执行curl
		$output = curl_exec($ch);
		//获取错误信息
		$errno = curl_errno($ch); 
		$err = curl_error($ch);
		//关闭curl
		curl_close($ch);
		if($errno)
		{
			echo "cURL Error #:" . $err;
	        throw new \XYException(__METHOD__,$errno);
		}
		else
		{
			// log_("output",$output,$this);
			$response = json_decode($output,true);
			// log_("response",$response,$this);

			return $response;
		}

	}

	/**
	 * 对比照片,生成对应的访客记录
	 * 
	 * @api
	 */
	public function comparePhoto(array $data,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			// log_("data",$data,$this);
			//获取当前用户的outer_id
			$tmp = M('User')->where(array('admin_uid'=>getAdminUid()))->find();
			$outer_id = $tmp['outer_id'];
			// $image = $_FILES['photo']['tmp_name'];
			$base64_image = str_replace(' ', '+', $data['image_base64']);
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
			$response = $this->detect($fileName);
			if(!empty($response['faces']))
			{
				$face_token = $response['faces'][0]['face_token'];
				$res = $this->searchFace($face_token,$outer_id);
				// log_("compareres",$res,$this);
				//更新数据
				
				//根据相似度判断是否插入新数据
		    	if($res['results'][0]['confidence']> 70 )//相似度小于70%,默认为没有找到
		    	{
		    		$insertData = null;
		    		$insertData['cid'] = $res['results'][0]['user_id'];
		    		$insertData['company_name'] = D('Company')->getCompanyName($res['results'][0]['user_id'],0);
		    		$insertData['photo'] = D('Company')->where(array('cid'=>$res['results'][0]['user_id']))->getField('image_url');
		    		//查询当前客户在访客列表中是否已有记录，如果有删除原记录
		    		$tmpdata = M('CusList')-> where(array('cid'=>$res['results'][0]['user_id']))->lock(true)->select();
		    		if(!empty($tmpdata))
		    		{
		    			$delRes =  M('CusList')-> where(array('cid'=>$res['results'][0]['user_id']))->delete();	
		    			if(empty($delRes))
		    				throw new \XYException(__METHOD__,-26000);
		    		}
		    		$insertRes = D('CusList')->createRecord($insertData,true);
		    		//company表中的visit_times 加1
		    		$updateRes = M('Company')->where(array('cid'=>$res['results'][0]['user_id']))->setInc('visit_times');
		    		if(empty($updateRes))
		    				throw new \XYException(__METHOD__,-26000);
		    	}

		    	if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			}
			//删除暂存文件
		        if (file_exists($fileName))
            		unlink($fileName);
	    	return 1;

		}catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}
		catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
		

	}

	/**
	 * @internal 
	 * 在faceset中查询对应的user_id
	 * string $face_token 客户需要识别的照片生成的face_token
	 * string $outer_id 该商家所有的face_token 对应的outer_id
	 * @return array $res
	 * @author wtt <wtt@xingyunbooks.com>
	 * @date 2017-07-18
	 */
	protected function searchFace( $face_token,$outer_id )
	{
		
    	$url = 'https://api-cn.faceplusplus.com/facepp/v3/search';
    	$data = array(
    		// 'image_file";filename="image'=>"$content", 
    		'face_token' => $face_token,
    		'api_key'=> $this->api_key,
	        'api_secret'=> $this->api_secret, //输入api_key和api_secret
    		'outer_id' => $outer_id
    		);
    	$res = $this->http_request($url, $data);
		// log_("searchresult",$res,$this);
		return $res;
    
	}
}