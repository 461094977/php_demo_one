<?php
namespace Compub\Controller;
use Common\Logic\BaseController;
use Common\Util\Vcode;
class VcodeController extends BaseController{

    public function _initialize(){
        $data['isIgnoreLogin'] = ['captcha'];
        parent::_initialize($data);
    }

    //一个网站有多处验证码
    public function captcha(){
        Vcode::captcha();
    }
}