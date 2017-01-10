<?php
namespace Admin\Controller;
use Common\Logic\BaseController;

class UserController extends BaseController{

    public function _initialize(){
        $data['isIgnoreAuth'] = ['modal_users','personalInfo'];
        parent::_initialize($data);
        $this->userModel = getModel('ThinkUser');
        $this->roleModel = getModel('ThinkRole');
    }

    //用户列表
    public function users(){
        $params = I('get.');
        $this->initParams($params);
        $list = $this->userModel->getListAndTotal($params);
        $this->page($list['total'],$params['pageSize']);
        $this->assign('list',$list['data']);
        $this->display();
    }

    private function initParams(&$params){
        $this->assign('params',$params);
    }

    //导出excel
    public function excel(){
        $params = I('get.');
        $this->initExcelParams($params);
        $list = $this->userModel->getList($params);
        $this->echoExcel($list,['ID'=>['id','_excelId'],'姓名'=>'fullname','用户名'=>'name','联系方式'=>'mobile','角色'=>'role_name','状态'=>['status','_excelStatus']],'用户列表');
    }

    protected function _excelId($val){
        return str_pad($val,6,'0',STR_PAD_LEFT);
    }

    protected function _excelStatus($val){
        if($val==1){
            return '正常';
        }
        if($val==0){
            return '停用';
        }
    }


    private function initExcelParams(&$params){
        $params['p'] = '';
        $params['pageSize']  = '';
    }

    //添加用户
    public function add(){
        $this->save();
    }

    //编辑用户
    public function edit(){
        $this->save();
    }

    //添加编辑用户
    private function save(){
        $params = I();
        $isSave = $params['isSave'];
        if(!$isSave){
            $this->saveHtml($params);
        }else{
            $this->saveAjax($params);
        }
    }

    private function saveHtml($params){
        $userId = $params['userId'];
        if($userId){
            $info = $this->userModel->getRow($params);
            $this->assign('info',$info);
        }
        $roleList = $this->roleModel->getList();
        $this->assign('roleList',$roleList);
        $this->assign('params',$params);
        $this->assign('ADMIN_USER_ID',ADMIN_USER_ID);
        $this->display('edituser');
    }

    private function saveAjax($params){
        $res = $this->userModel->edit($params);
        $userId = $params['userId'];
        $this->writelog($userId?'编辑':'添加','账户管理',"用户ID:{$res['data']}",$res);
        $this->ajaxReturn($res);
    }

    //删除用户
    public function delete(){
        $params = I();
        $res = $this->userModel->del($params);
        $this->writelog('删除','账户管理',"用户ID:{$params['userId']}",$res);
        $this->ajaxReturn($res);
    }

    //广告系统新建订单模态框列表 默认筛选销售经理
    public function modal_users(){
        $params = I();
        $isAjax = $params['is_ajax'];
        $this->intUsersParams($params);
        $list = $this->userModel->getListAndTotal($params);
        $this->assign('list',$list['data']);
        $this->page($list['total'],$params['pageSize'],2);
        if($isAjax){
            $this->display('modalUsersTable');
        }else{
            $table = $this->fetch('modalUsersTable');
            $this->assign('table',$table);
            $this->display('modalUsers');
        }
    }

     private function intUsersParams(&$params){
        $params['roleId'] = ROlE_MANAGER_ID;
        $params['pageSize'] = 10;
    }

    public function personalInfo(){
        $userId = $_SESSION['user']['userId'];
        $info = $this->userModel->find($userId);
        $info['password'] = '?default?';
        $info['role_name'] = $_SESSION['user']['roleName'];
        $this->assign('info',$info);
//        $roleList = $this->roleModel->getList();
//        $this->assign('roleList',$roleList);
//        $this->assign('params',$params);
//        $this->assign('ADMIN_USER_ID',ADMIN_USER_ID);
        $this->display('');
    }
    public function editPersonalInfo(){  // 修改个人资料 和修改密码最好分开处理 ！！！
        $params = I('post.');

        $full_name = $params['fullname'];
        $mobile = $params['mobile'];
        $password = $params['password'];

        $info = info();
        if(!$full_name){
            $info = info(1,'姓名不能为空');
        }
        if(!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$full_name)){
            $info = info(1,'姓名需为中文');
        }
        if(!$mobile){
            $info = info('1','联系电话不能为空');
        }
        if(!preg_match("/^[0-9]{8,20}$/u",$mobile)){
            $info = info('1','联系电话需8到20位数字');
        }

        if(mb_strlen($password,'UTF-8')<4||mb_strlen($password,'UTF-8')>12){
            $info = info('1','密码4到12个字符');
        }
        if($password!='?default?'){
            /*if(!preg_match("/^[A-Za-z0-9_]+$/u",$password)){
                return info('1','用户名需为数字,字母,下划线');
            }*/
            $params['password'] = md5($password);
        }else{
            unset($params['password']);
        }
        $params['id'] = $_SESSION['user']['userId'];
        $params['update_date'] = date('Y-m-d H:i:s',time());
        M('think_user')->save($params);

        $this->ajaxReturn($info);
    }
}