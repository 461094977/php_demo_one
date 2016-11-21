<?php
/**
 * Modal层字段类型校验
 */
namespace Common\Util;

class TypeCheck {

    //校验是否是固定电话，如020-12345678
    public function homephone($data){
        if(preg_match('/^(\d){3,4}-(\d){7,8}$/',$data)){
            return true;
        }else{
            return false;
        }
    }

    //校验是否是手机号码，如13612345678
    public function telephone($data){
        if(preg_match('/^(1(3|4|5|7|8|9)(\d){9})$/',$data)){
            return true;
        }else{
            return false;
        }
    }

    //校验是否是电话号码（固话或手机）
    public function phone($data){
        if($this->homephone($data)||$this->telephone($data)){
            return true;
        }else{
            return false;
        }
    }

    //校验数字位数,param1指定最大位数,param2指定小数点后精度位数,param2不填时检验整数
    public function numbersize($data,$param1,$param2=''){
        if(empty($param2)){
            //整数
            if(preg_match('/^(\d){1,'.$param1.'}$/',$data)){
                return true;
            }else{
                return false;
            }
        }else{
            //小数
            if(preg_match('/^(\d){1,'.$param1.'}(\.(\d){1,'.$param2.'})?$/',$data)){
                return true;
            }else{
                return false;
            }
        }
    }

    //校验数字范围，param1指定可填的最小值，param2指定可填的最大值
    public function numberrange($data,$param1,$param2){
        if(!is_numeric($data)){
            return false;
        }else{
            $data_float = floatval($data);
            if($data_float>=$param1&&$data_float<=$param2){
                return true;
            }else{
                return false;
            }
        }
    }

    //校验字符串长度,param1指定最小长度，param2指定最大长度,param2不填时固定为param1位数
    public function length($data,$param1,$param2=''){
        $data_length = mb_strlen($data,'utf8');
        if(empty($param2)){
            if($data_length == $param1){
                return true;
            }else{
                return false;
            }
        }else{
            if($data_length>=$param1&&$data_length<=$param2){
                return true;
            }else{
                return false;
            }
        }
    }

    //校验纯中文字符串长度，param1指定最小长度，param2指定最大长度,param2不填时固定为param1位数
    public function chinese($data,$param1,$param2=''){
        if(empty($param2)){
            if(preg_match('/^[\x{4e00}-\x{9fa5}]{'.$param1.'}$/u',$data)){
                return true;
            }else{
                return false;
            }
        }else{
            if(preg_match('/^[\x{4e00}-\x{9fa5}]{'.$param1.','.$param2.'}$/u',$data)){
                return true;
            }else{
                return false;
            }
        }
    }
}
