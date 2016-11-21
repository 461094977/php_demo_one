<?php

namespace Common\Logic;

class User {

    public static function sessionUserAccess($data=null){
        if($data === null){
            return $_SESSION['user']['nodeListIndex'];
        }else{
            $_SESSION['user']['nodeListIndex'] = $data;
        }
    }

    public static function sessionMenuAccess($data=null){
        if($data === null){
            return $_SESSION['user']['menuAccessList'];
        }else{
            $_SESSION['user']['menuAccessList']=$data;
        }
    }

    public static function sessionUserId($data=null){
        if($data === null){
            return $_SESSION['user']['userId'];
        }else{
            $_SESSION['user']['userId']=$data;
        }
    }

    public static function sessionUserRodeId($data=null){
        if($data === null){
            return $_SESSION['user']['roleId'];
        }else{
            $_SESSION['user']['roleId'] = $data;
        }
    }

    public static function sessionUserInfo($data=null){
        if($data === null){
            return session('user');
        }else{
            session('user',$data);
        }
    }

    public static function sessionUserName($data=null){
        if($data === null){
            return $_SESSION['user']['userName'];
        }else{
            $_SESSION['user']['userName'] = $data;
        }

    }

}