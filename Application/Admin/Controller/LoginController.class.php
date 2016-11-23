<?php
namespace Admin\Controller;
use Common\Logic\BaseController;
use Common\Util\Vcode;
use Common\Logic\User;
use Common\Util\Rbac;

class LoginController extends  BaseController{

    public function _initialize(){
        $data['isIgnoreLogin'] = ['index','signin'];
        $data['isIgnoreAuth'] = ['index','welcome','signin','signout','updatepwd'];
        parent::_initialize($data);
        $this->userModel = getModel('ThinkUser');
    }

    //登录界面
    public function index(){
        $this->display();
    }

    //系统登录后欢迎页
    public function welcome(){
        $this->display();
    }

    //登录接口
    public function signin(){
        $params = I();

//        //校验码校验
//        if(!Vcode::check($params['vcode'])){
//            $this->ajaxReturn(info('1','验证码错误'));
//        }

        //用户密码校验
        $info= $this->userModel->getRow($params);
        if(!$info){
            $this->ajaxReturn(info('1','用户不存在'));
        }
        $this->logicLogin($info);
        if($info['password'] !=md5($params['password'])){
            $this->ajaxReturn(info('1','密码不正确'));
        }
        //写入session
        $sessionUserInfo['userId'] = $info['id'];
        $sessionUserInfo['userName'] = $info['name'];
        $sessionUserInfo['roleId'] = $info['role_id'];
        $sessionUserInfo['roleName'] = $info['role_name'];
        User::sessionUserInfo($sessionUserInfo);
        Rbac::accessUserByUrl();
        Rbac::getAuthMenuList();
        //$this->writelog('登录','用户');
        $this->ajaxReturn(info('0','登录成功'));
    }

    //业务地推人员不允许登录 是否停用状态
    private function logicLogin($info){
        $roleId = $info['role_id'];
        $userId = $info['id'];
        if(ADMIN_USER_ID == $userId){
            return ;
        }
        if($roleId==ROLE_PUSH_ID){
            $this->ajaxReturn(info('1','地推人员不可登录'));
        }
        if($info['status'] !=1){
            $this->ajaxReturn(info('1','帐号已停用'));
        }
    }

    //退出接口
    public function signout(){
        //$this->writelog('退出','用户');
        session('[destroy]');
        $this->ajaxReturn(info('0','成功退出'));
    }

    //更新密码接口
    public function updatepwd(){
        $params = I();
        $res = $this->userModel->edit($params);
        if(!$res){
            $this->ajaxReturn(info('1','编辑失败'));
        }else{
            $this->ajaxReturn(info('0','编辑成功'));
        }
    }
}