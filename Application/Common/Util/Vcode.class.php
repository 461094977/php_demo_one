<?php

/**
* 验证码
 */

namespace Common\Util;

class Vcode {

    public static function check($captcha){
        $captchaClass = new \Think\Verify();
        return $captchaClass->check($captcha);
    }

    /**
     * 生成验证码
     */
    public static function captcha(){
        $config =    array(
            'length'      =>    2,     // 验证码位数
            'imageW'    =>   100,
            'imageH'     =>   40,
            'fontSize'    =>    20,
            'useNoise'    =>    false,
            'useCurve'    =>    false,
        );
        $captchaClass = new \Think\Verify($config);
        $captchaClass->entry();
    }

}