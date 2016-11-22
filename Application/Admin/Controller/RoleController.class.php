<?php
namespace Admin\Controller;
use Common\Logic\BaseController;
use Common\Util\Sort;
class RoleController extends BaseController{

    public function _initialize(){
        $data['isIgnoreAuth'] = ['roleajax'];
        parent::_initialize($data);
        $this->roleModel = getModel('ThinkRole');
        $this->nodeModel = getModel('ThinkNode');
        $this->accessModel = getModel('ThinkAccess');
    }

    //角色管理
    public function roles(){
        $params = I('get.');
        $this->initParams($params);
        $list = $this->roleModel->getListAndTotal($params);
        $this->page($list['total'],$params['pageSize']);
        $this->assign('list',$list['data']);
        $this->assign('ROLE_PUSH_ID',ROLE_PUSH_ID);
        $this->assign('ROlE_MANAGER_ID',ROlE_MANAGER_ID);
        $this->display();
    }

    public function excel(){
        $params = I('get.');
        $params['p'] = '';
        $list = $this->roleModel->getList($params);
        $this->echoExcel($list,['ID'=>['id','_excelId'],'角色名称'=>'name','详述'=>'remark'],'角色列表');
    }

    protected function _excelId($val){
        return str_pad($val,4,'0',STR_PAD_LEFT);
    }

    private function initParams(&$params){
        //$params['pageSize'] = 2;
        $this->assign('params',$params);
    }

    //添加角色
    public function add(){
         $this->save();
    }

   /* public function edit(){
        $this->save();
    }*/

    private function save(){
        $params = I();
        if(!$params['isSave']){
            $this->saveHtml($params);
        }else{
            $this->saveAjax($params);
        }
    }

    private function saveHtml($params){
        $this->display('edit');
    }

    private function saveAjax($params){
        $res = $this->roleModel->edit($params);
        $this->writelog('添加','角色',"角色ID:{$res['data']}",$res);
        $this->ajaxReturn($res);
    }

    //删除角色
    public function delete(){
        $params = I();
        $res = $this->roleModel->del($params);
        $this->writelog('删除','角色',"角色ID:{$res['data']}",$res);
        $this->ajaxReturn($res);
    }

    //角色权限分配
    public function assignnode(){
        $params = I();
        if(!$params['isSave']){
            $this->assignNodeHtml($params);
        }else{
            $this->assignNodeAjax($params);
        }
    }

    private function assignNodeHtml($params){
        $roleId = $params['roleId'];
        $info = $this->roleModel->getRow(array('roleId'=>$roleId,'getNodes'=>true));
        $this->assign('info',$info);
        $nodeList = $this->nodeModel->getList();
        $nodeList = $this->sortNode($nodeList,'0');
        $this->assign('nodeList',$nodeList);
        $this->display('assignnode');
    }

    //特殊情况排序
    private function sortNode($data,$pid='0'){
        static $arr = array();
        foreach($data as $v){
            if($v['level']==2){
                if($v['pid'] == $pid){
                    $arr[] = $v;
                    $arr[] = $this->sortNodeLevel3($data,$v['id']);
                }
            }
            if($v['level']==1){
                if($v['pid'] == $pid){
                    $arr[] = $v;
                    $this->sortNode($data,$v['id']);
                }
            }
        }
        return $arr;
    }

    private function sortNodeLevel3($data,$pid){
        $arr = array();
        foreach($data as $v){
            if($v['pid'] == $pid){
                $arr['childNodes'][] = $v;
                $arr['level'] = 3 ;
            }
        }
        return $arr;
    }


    private function assignNodeAjax($params){
        $res = $this->accessModel->edit($params);
        $this->writelog('编辑','角色权限',"角色ID:{$res['data']['roleId']}",$res);
        $this->ajaxReturn($res);
    }

    //角色下拉框
    public function roleajax(){
        $params= I();
        $res = $this->roleModel->getList();
        $this->ajaxReturn(info('0','角色',$res));
    }

}