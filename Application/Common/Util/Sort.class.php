<?php

/**
 * 排序
 */
namespace Common\Util;

class Sort {
    //无限级分类 输出二维数组
   static public function unlimit($data,$pid='0'){
        $arr = array();
        foreach($data as $v){
            if($v['pid'] == $pid){
                $arr[] = $v;
                $arr = array_merge($arr, static::unlimit($data,$v['id']));
            }

        }
        return $arr;
    }

    //无限极分类 多维数组输出
    static public function unlimitMulArr($data,$pid='0'){
        $arr = array();
        foreach($data as $v){
            if($v['pid'] == $pid){
                $v['child'] = static::unlimitMulArr($data,$v['id']);
                $arr[] = $v;
            }

        }
        return $arr;
    }
}