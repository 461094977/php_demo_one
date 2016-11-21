<?php

namespace Common\Logic;
use Common\Util\Http;

class Upload {

    /**
     *  单文件上传
     *  参数
     *      $config数组 配置
     *      $pixel数字数组 图片所需像素大小 第一个表示宽度 第二个高度
     *      $size 文件大小
     *
     */
    static public function file($config,$pixel,$size){
         if(!$_FILES){
            return info('601','上传文件为空');
         }
         //判断Upload目录是否不存在创建
         $path = './Upload';
         if(!is_dir($path)){
            if(mkdir($path,0777,true)===false){
                return info('605','创建目录异常');
            }
         }
         $info = static::upload($config,$upload);
         if(!$info){
            $errorInfo = $upload->getError();
            if($errorInfo =='上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！'){//这里判读文件过大服务器接受不到，提示友好
                $size =  $size/(1024*1024);
                return info('603',"图片大小需小于{$size}M");
            }else{
                return info('602',$errorInfo);
            }
         }
        foreach($info as $file){
                $data = $file;
                $data['path'] = $upload->rootPath . $file['savepath'] . $file['savename'];
        }
        $res = static::checkPic($pixel,$size,$data);
        if($res['result']){
            return $res;
        }
        return info('0','上传成功',$data);
    }

    //多文件上传暂时不用
    static public function files($config){
        if(!$_FILES){
            return info('601','上传文件为空');
         }
         $info = static::upload($config,$upload);
         if(!$info){
            return info('602',$upload->getError());
         }
        foreach($info as $k=>$file){
                $$info[$k]['path'] = $upload->rootPath . $file['savepath'] . $file['savename'];
        }
        return info('0','上传成功',$info);
    }

    static private function upload($config,&$upload){
         $upload = new \Think\Upload();// 实例化上传类
         $upload->maxSize = 30000000 ;// 设置附件上传大小
        if($config['exts']){
            $upload->exts = $config['exts'];
        }else{
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg','pdf');// 设置附件上传类型
        }
        if($config['mimes']){
            $upload->mimes = $config['mimes'];
        }else{
            $upload->mimes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif','application/pdf');
        }
         $upload->rootPath="Upload/";
         $upload->savePath = 'temp/';// 设置附件上传目录
         $info=$upload->upload();
         return $info;
    }

    static private function checkPic($pixel,$size,$data){
        if($size){
            if($data['size']>$size){
                $size =  $size/(1024*1024);
                return info('603',"图片大小需小于{$size}M");
            }
        }
        $stWidth = $pixel[0];
        $stHeitht = $pixel[1];
        if($stWidth&&$stHeitht){
            list($width,$height) = getimagesize($data['path']);
            if($stWidth!=$width||$stHeitht==$heigh){
                return info('604',"图片像素需{$stWidth}*{$stHeitht}");
            }
        }
        return info('0','图片大小符合');
    }

    //调用API上传接口
    static public function uploadApi($path,$isALLUrl = false){
        $res = Http::fileStream(C('IMG_UPLOAD_DESTINATION'), $path);
        $url = $res['data']['imagePath'];
        if(!$isALLUrl){
            $url = str_replace(C('URL_UPLOAD_PREFIX'), '', $url);
        }
        return $url;
    }
/*********************** 修改上传 验证提示 方式 ******************************/
    static public function lingFile(){
        if(!$_FILES){
            return info('601','上传文件为空',null);
        }
        //判断Upload目录是否不存在创建
        $path = './Upload';
        if(!is_dir($path)){
            if(mkdir($path,0777,true)===false){
                return info('1','创建目录异常',null);
            }
        }
        $info = static::lingUpload($upload);
        if(!$info){
            return info('1',$upload->getError(),null);
        }
        foreach($info as $file){
            $data = $file;
            $data['path'] = $upload->rootPath . $file['savepath'] . $file['savename'];
        }
        return info('0','上传成功',$data);
    }
    //调用API上传接口
    static public function lingUploadApi($path){
        $res = Http::fileStream(C('IMG_UPLOAD_DESTINATION'), $path);
        return $res;
    }
    static private function lingUpload(&$upload){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 40000000 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg','pdf');// 设置附件上传类型
        $upload->mimes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif','application/pdf');
        $upload->rootPath="Upload/";
        $upload->savePath = 'temp/';// 设置附件上传目录
//        $upload->saveName = '';
//        $upload->replace = true;
        $info=$upload->upload();
        return $info;
    }


}