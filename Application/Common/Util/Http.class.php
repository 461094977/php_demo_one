<?php

/**
* Http利用curl
* get Post 默认get
* json xml text 传参方式默认json
* 文件上传
*/

namespace Common\Util;
use Common\Logic\User;

class Http {

    static public function requestCom($url, $params = array(), $method = 'GET',$header = array(), $multi = false,$dataType='json'){
        record(array('url'=>$url,'params'=>$params,'method'=>$method,'header'=>$header,'multi'=>$multi),'公共请求方法');
        $method = strtoupper($method);
        if($method!='GET'){
            $params = json_encode($params);
        }
        if($dataType=='bin'){
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        }
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
        );
        $userName = User::sessionUserName();
        if(!$userName){
            throw new \Exception("请求接口登录用户名异常");
        }
        $tokenUser = 'AnjubaoLoginAccount: "'.$userName.'"';
        switch ($method) {
            case "POST":
                //POST 是传JSON
                $opts[CURLOPT_POSTFIELDS] = $params;
                $opts[CURLOPT_HTTPHEADER] = array('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($params),$tokenUser);
                break;
            case "GET":
                $params = http_build_query($params);
                if($params){
                    $opts[CURLOPT_URL] .= "?" . $params;
                }
                $opts[CURLOPT_HTTPHEADER] = array($tokenUser);
                break;
            case "PUT":
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            case "DELETE":
                $opts[CURLOPT_HTTPHEADER] = array($tokenUser);
                break;
        }

        //初始化并执行CURL REQUEST
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $errorNo = curl_errno($ch);
        $errorInfo = curl_error($ch);
//        record(curl_getinfo($ch));
        curl_close($ch);
        record(($d = json_decode($data))?$d:$data,'公共请求方法');
        if($dataType=='json'){
            $data = json_decode($data,true);
        }
        if ($errorNo) {
            record($errorNo,'错误号');
            record($errorInfo,'错误信息');
            throw new \Exception("请求发生错误");
        } else {
            return $data;
        }
    }

    static public function request($url, $params = array(), $method = 'GET',$header = array(), $multi = false) {
        return static::requestCom($url, $params,$method,$header,$multi);
    }

    static public function requestBin($url, $params = array(), $method = 'GET',$header = array(), $multi = false){
        return static::requestCom($url, $params,$method,$header,$multi,'bin');
    }

    static public function get(){
    }

    static public function post(){
    }

    static public function fileStream($url, $file){
        record(array('url'=>$url,'file'=>$file),'文件上传调用API请求参数');
        $ch = curl_init();
        // 此处为了兼容PHP5.6以上版本
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        }
        // 加@符号curl就会把它当成是文件上传处理
        //"@/Users/loong/Desktop/IMG_1252.jpg"
        $data = array(
                'img' => '@'.$file,
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error( $ch );
        curl_close($ch);
        if($errno){
            record($error,'文件上传调用API接口异常信息');
            throw new \Exception("请求发生错误:{$error}");
        }else{
            $temp = $result;
            $result = json_decode($result, true);
            if(!$result){
                record($temp,'文件上传调用API接口返回异常');
                return $temp;
            }
            record($result,'文件上传调用API返回结果');
        }
        return $result;
    }


}