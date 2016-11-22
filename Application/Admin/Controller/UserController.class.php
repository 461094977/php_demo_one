<?php
namespace Admin\Controller;
use Common\Logic\BaseController;

class UserController extends BaseController{

    public function _initialize(){
        $data['isIgnoreAuth'] = ['modal_users'];
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


}