<?php
/**
 *  菜单管理
 */
namespace Admin\Controller;
use Common\Logic\BaseController;
use Common\Util\Sort;

class MenuController extends  BaseController{

    public function _initialize(){
        $data['isIgnoreAuth'] = ['autoConfMenus'];
        parent::_initialize($data);
        $this->menuModel = getModel('ThinkMenu');
        $this->nodeModel = getModel('ThinkNode');
    }

    //配置菜单
    public function autoConfMenus(){
        $this->existMenus = array();
        $menus = C('AutoMenusConf');
        if($menus){
            $this->autoAddMenus($menus,0,1);
        }
        print_r($this->existMenus);
    }

    private function autoAddMenus($menus,$pid,$level){
        foreach($menus as $k=>$v){
            if($level==1){
            $params['title'] = $k;
            $params['pid'] = 0;
            $params['level'] = 1;
        }
            if($level==2){
                $str_arr = explode('/',$v);
                $pop = array_pop($str_arr);
                array_push($str_arr,$pop);
                $v_v  = implode('/',$str_arr);

                $params['title'] = $k;
                $params['pid'] = $pid;
                $params['level'] = 2;
                $params['path'] = $v_v;
            }

            $res = $this->menuModel->edit($params);
            $this->existMenus = array_merge($this->existMenus,array($res['data'].":{$k} ".$res['message']));
            if($level==1){
                 if($v&&$res['data']){
                    $this->autoAddMenus($v,$res['data'],$level+1);
                }
            }
        }
    }

    //菜单管理页
    public function menus(){
        $params = I();
        $this->initParams($params);
        $list = $this->menuModel->getListAndTotal($params);
        $sortList = Sort::unlimit($list['data']);
        $this->assign('list',$sortList);
        $this->display();
    }

    private function initParams(&$params){
        $params['pageSize'] = 9999;
    }

    //添加
    public function add(){
        $this->save();
    }

    //编辑
    public function edit(){
        $this->save();
    }

    private function save(){
        $params = I();
        if(!$params['isSave']){
            $this->saveHtml($params);
        }else{
            $this->saveAjax($params);
        }
    }

    private function saveHtml($params){
        $menuId = $params['menuId'];
        if($menuId){
            $info = $this->menuModel->getRow(array('menuId'=>$menuId));
            $this->assign('info',$info);
            $this->assign('MENU_ID_UNDELETE',C('MENU_ID_UNDELETE'));
        }
        $menuListLevel1 = $this->menuModel->getList(array('level'=>1));
        $this->assign('menuListLevel1',$menuListLevel1);
        $this->assign('params',$params);
        $this->display('edit');
    }

    private function saveAjax($params){
        $parentMenuId = $params['parentMenuId'];
        if($parentMenuId){
            $params['pid'] = $parentMenuId;
            $params['level'] = 2;
        }else{
            $params['pid'] = 0;
            $params['level'] = 1;
        }
        $res = $this->menuModel->edit($params);
        $menuId = $params['menuId'];
        //$this->writelog($menuId?'编辑':'添加','菜单','',$res);
        $this->ajaxReturn($res);
    }

    //删除
    public function delete(){
        $params = I();
        $res = $this->menuModel->del($params);
        //$this->writelog('删除','菜单','',$res);
        $this->ajaxReturn($res);
    }

}