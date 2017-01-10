<?php

/**加载常量文件**/
//require_once APP_PATH.'Common/Common/constant.php';

/**
 * 二维数组以某列最为索引键值 -->array_column
 * 注意：该列不能有重复,常见model层二维数组id索引
 */
function arrayIndex($array,$column){
    $arrayIndex = array();
    foreach($array as $v){
        $arrayIndex[$v[$column]] = $v;
    }
    return $arrayIndex;
}

//获取二维数组某一列值 -->array_column
function get_array_column($array,$column){
    $temp = array();
    foreach($array as $v){
        if(!$v)continue;
        $temp[] = $v[$column];
    }
    return $temp;
}

//统一输出提示格式
function info($result=0,$message='操作成功!',$data=null){
    $format['result'] = $result;
    $format['message'] = $message;
    $format['data'] = $data;
    return $format;
}

//model层条件或数据增加
function modelArray(&$condition = array(),$param,$key,$conKey = '',$mode = NULL){
        if(!$conKey){
            $conKey = $key;
        }
        $value = $param[$key];

        if( is_array($mode) ){
            if( $value===NULL || in_array($value,$mode,true)  ){
                return ;
            }
        }else{
            if(!$value){
                return ;
            }
        }

        if(is_array($value)){
            $condition[$conKey] = array('in',$value);
        }else{
            $condition[$conKey] = $value;
        }
}

//tp日志记录
function record($data,$name='define'){
    $module = MODULE_NAME;
    $controller = CONTROLLER_NAME;
    $action = ACTION_NAME;
    \Think\Log::record("<{$name}>:<{$module}><{$controller}><$action>:".date('Y-m-d H:i:s').':'.var_export($data,true),'INFO',true);
}

//从Common模块获取model
function getModel($name){
    return D("Common/{$name}");
}

/* typeCheckRegEx
 * Modal层类型校验（正则表达式）
 * type:
 * 'phone'：固话或手机
 * 'number':数字,param1指定最大位数,param2指定小数点后精度位数,param2不填时检验整数
 * 'length':字符串长度,param1指定最小长度，param2指定最大长度,param2不填时固定为param1位数
 * "chinese':纯中文字符串长度，param1指定最小长度，param2指定最大长度
 *
 * return true 类型匹配 false 类型不匹配
 */
function typeCheckRegEx($origin,$type,$param1='',$param2='',$param3=''){

    if($type == 'phone'){
        if(preg_match('/^((\d){3,4}-(\d){7,8})$|^(1(3|4|5|7|8|9)(\d){9})$/',$origin)){
            return true;
        }else{
            return false;
        }
    }

    if($type == 'number'){
        if(empty($param2)){
            //整数
            if(preg_match('/^(\d){1,'.$param1.'}$/',$origin)){
                return true;
            }else{
                return false;
            }
        }else{
            //小数
            if(preg_match('/^(\d){1,'.$param1.'}(\.(\d){1,'.$param2.'})?$/',$origin)){
                if(!empty($param3)){
                    if($origin == '0.00')return false;
                }
                return true;
            }else{
                return false;
            }
        }
    }

    if($type == 'length'){
        $origin_length = mb_strlen($origin,'utf8');
        if(empty($param2)){
            if($origin_length == $param1){
                return true;
            }else{
                return false;
            }
        }else{
            if($origin_length>=$param1&&$origin_length<=$param2){
                return true;
            }else{
                return false;
            }
        }

    }

    if($type == 'chinese'){
        if(empty($param2)){
            if(preg_match('/^[\x{4e00}-\x{9fa5}]{'.$param1.'}$/u',$origin)){
                return true;
            }else{
                return false;
            }
        }else{
            if(preg_match('/^[\x{4e00}-\x{9fa5}]{'.$param1.','.$param2.'}$/u',$origin)){
                return true;
            }else{
                return false;
            }
        }

    }

    if($type == 'url'){
        if (preg_match('@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@', $origin)) {
            if(strrpos($origin,'https://')===false && strrpos($origin,'http://')=== false){return false;}
            return true;
        } else {
            return false;
        }
    }

    return false;
}

/*
 * dateStartToEnd
 * Modal层中查询条件中起始日期到终止日期
 * start -- 起始日期字段名
 * end -- 结束日期字段名
 * dateName 存储在$condition的字段名
 */
function dateStartToEnd(&$condition = array(),$param,$start,$end,$dateName){
    $startDate = $param[$start];
    $endDate = $param[$end];
    if($startDate&&$endDate){
        $condition[$dateName] = array('between',array("{$startDate} 00:00:00","{$endDate} 23:59:59"));
    }
    if(!$startDate&&$endDate){
        $condition[$dateName] = array('elt',"{$endDate} 23:59:59");
    }
    if($startDate&&!$endDate){
        $condition[$dateName] = array('egt',"{$startDate} 00:00:00");
    }
}

/**
 * 打印数据，用于调试
 * @param var 打印对象
 */
function p($var){
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}
/**
 *
 * @param url 要生成的二维码的内容
 * @param flag 是否生成图片保存起来，1是，0否（只是动态生成）
 */
function createQRcode($url,$flag=0){
    vendor("phpqrcode.phpqrcode");
    // 纠错级别：L、M、Q、H
    $level = 'H';
    // 点的大小：1到10,用于手机端4就可以了
    $size = 7;
    // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
    if($flag){
        $path = "Public/QRcode/";
        if(!file_exists($path)){
            mkdir($path, 0700);
        }
        // 生成的文件名
        $fileName = $path.time().'.png';
        QRcode::png($url, $fileName, $level, $size);
        return $fileName;
    }else{
        QRcode::png($url, false, $level, $size);
        exit;
    }
}
/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组`引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
function true_or_false($obj){
    if($obj ==1){
        return true;
    }else{
        return false;
    }
};
function ascii_to_int($obj){
    switch((int)ord($obj)){
        case 0:
            $obj=0;
            break;
        case 1:
            $obj=1;
            break;
        case 49:
            $obj=1;
            break;
        case 48:
            $obj=0;
            break;
    }
    return $obj;
};

?>