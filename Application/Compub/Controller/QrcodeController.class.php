<?php
/**
 * Created by PhpStorm.
 * User: anjubao
 * Date: 2016-10-8
 * Time: 10:48
 */

namespace Compub\Controller;
use Common\Logic\BaseController;

class QrcodeController extends  BaseController{

    /**
     * 生成二维码
     * @param $content 需要生成的二维码内容
     * @param $flag 是否需要保存到本地，1为保存，0为不保存
     */
    public function qrcode(){
        $param=I();
        $content=$param['content'];
        $flag=$param['$flag'];
        if($flag==0){//直接输出图片至浏览器
            header("Content-type: image/jpeg");
            createQRcode($content,$flag=0);
        }else{//返回二维码图片路径，默认路径为Public/QRcode目录下
            return createQRcode($content,1);
        }

    }
}