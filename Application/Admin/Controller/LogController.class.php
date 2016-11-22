<?php
/**
 *  日志管理
 */
namespace Admin\Controller;
use Common\Logic\BaseController;
use Common\Util\Operation;

class LogController extends  BaseController{

    public function _initialize(){
        parent::_initialize();
        $this->logModel = getModel('ThinkOperationLog');
        //$this->lang = L('ADMIN');
    }

    //日志管理
    public function logs(){
        $params = I();
        $this->initParams($params);
        $list = $this->logModel->getListAndTotal($params);
        //$this->lang($list['data']);
        $this->assign('list',$list['data']);
        $this->page($list['total'],$params['pageSize']);
        $this->display();
    }

    private function initParams(&$params){
        $this->assign('params',$params);
    }

    //语言包处理
    private function lang(&$list){
        foreach($list as $k=>$v){
            $controller = $v['operation_model'];
            $action = $v['operation_node'];
            $list[$k]['operation_model'] = $this->getLangByMCA($controller,$controller,$action).'('.$controller.')';
            $list[$k]['operation_node']  = $this->getLangByMCA($action,$controller,$action).'('.$action.')';
            $list[$k]['operation_content'] =  $this->arraytoString(unserialize($v['operation_content']),$controller,$action,1);
        }
    }

    private function getLangByMCA($key,$controller,$action){
        return $this->lang[$controller][$action][$key];
    }

    private function arraytoString($array,$controller,$action,$level){
        $str = '';
        $keys   =   array_keys($this->lang[$controller][$action]);
        foreach($array as $key=>$val){
            if(is_array($val)){
                $str.=$this->getLangByMCA($key,$controller,$action)."[{$key}]=> ".'<br/> ';
                $str.=$this->arraytoString($val,$controller,$action,$level+1);
            }else{
                if(in_array($key,$keys)){
                    $str.=$this->getLangByMCA($key,$controller,$action)."[{$key}]=> ".$val.'<br/> ';
                }else{
                    $str.="[{$key}]=> ".$val.'<br/> ';
                }
            }
        }
        return $str;
    }

    //删除
    public function delete(){
        $params = I();
        $res = $this->logModel->del($params);
        $this->ajaxReturn($res);
    }

}