<?php

namespace Common\Util;
use Common\Logic\User;

/**
 * Class Operation
 * @package Org\Util
 *
 *
 *数据表
 DROP TABLE IF EXISTS `dct_operation_log`;
CREATE TABLE `dct_operation_log` (
`operation_id` int(8) NOT NULL AUTO_INCREMENT,
`operation_user` varchar(30) DEFAULT NULL,
`operation_node` varchar(50) DEFAULT NULL,
`operation_model` varchar(50) DEFAULT NULL,
`operation_ip` varchar(30) DEFAULT NULL,
`operation_content` text,
`operation_time` datetime DEFAULT NULL,
PRIMARY KEY (`operation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

class Operation {

    static $param;

    static public function param(){
        static::$param = I();
    }

    static public function writelog($action,$object,$remark){
        $data['operation_user_id'] =   isset($_SESSION['user']['userId'])? $_SESSION['user']['userId']: '';
        $data['operation_user'] =   isset($_SESSION['user']['userName'])? $_SESSION['user']['userName'] : '';
        $data['operation_module'] =   (MODULE_NAME)? MODULE_NAME : '';
        $data['operation_model'] =   (CONTROLLER_NAME)? CONTROLLER_NAME : '';
        $data['operation_node'] =   (ACTION_NAME)? ACTION_NAME : '';
        $data['operation_ip'] =   self::get_client_ip();
        $data['operation_content'] =   isset(static::$param)? serialize(static::$param) : '';
        $data['operation_time'] =   date('Y-m-d H:i:s');
        $data['operation_action'] = $action;
        $data['operation_object'] = $object;
        $data['operation_remark'] = $remark;
        $operation_mod    =   D('ThinkOperationLog');
        $operation_mod->add($data);
    }

   static private function get_client_ip($type = 0) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实IP
            $ip=$_SERVER['HTTP_X_REAL_IP'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

}