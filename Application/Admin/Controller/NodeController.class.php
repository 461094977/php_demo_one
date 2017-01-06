<?php
namespace Admin\Controller;
use Common\Logic\BaseController;
use Common\Util\Sort;
class NodeController extends BaseController{

    public function _initialize(){
        $data['isIgnoreAuth'] = ['nodesajax','autoconfnodes'];
        parent::_initialize($data);
        $this->nodeModel = getModel('ThinkNode');
    }

    //配置节点
    public function autoConfNodes(){
        $this->existNodes = array();
        $nodes = C('AutoNodesConf');
        if($nodes){
            $this->autoAddNodes($nodes,0,1);
        }
        print_r($this->existNodes);
    }

    private function autoAddNodes($nodes,$pid,$level){
        foreach($nodes as $k=>$v){
            if($level==1||$level==2){
                list($name,$title) = explode('-',$k);
            }
            if($level==3){
                list($name,$title) = explode('-',$v);
            }
            $params['name'] = $name;
            $params['title'] = $title;
            $params['pid'] = $pid;
            $params['level'] = $level;
            $res = $this->nodeModel->edit($params);
            $this->existNodes = array_merge($this->existNodes,array($res['data'].":{$name}-{$title} ".$res['message']));
            if($level==1||$level==2){
                if($v&&$res['data']){
                    $this->autoAddNodes($v,$res['data'],$level+1);
                }
            }
        }
    }

    //节点列表
    public function nodes(){
        $params = I();
        $this->initParams($params);
        $list = $this->nodeModel->getListAndTotal($params);
        $sortList = Sort::unlimit($list['data']);
        $this->assign('list',$sortList);
        $this->display();
    }

    private function initParams(&$params){
        $params['pageSize'] = 9999;
    }

    //添加节点
    public function add(){
        $this->save();
    }

    //编辑节点
    public function edit(){
        $this->save();
    }


    //添加编辑节点
    private function save(){
        $params = I();
        if(!$params['isSave']){
            $this->saveHtml($params);
        }else{
            $this->saveAjax($params);
        }
    }

    private function saveHtml($params){
        $nodeId = $params['nodeId'];
        if($nodeId){
            $info = $this->nodeModel->getRow(array('nodeId'=>$nodeId));
            if($info['pid']){
                $pInfo = $this->nodeModel->getRow(array('nodeId'=>$info['pid']));
                if($pInfo['pid']){
                    $info['parentNode1'] = $pInfo['pid'];
                    $info['parentNode2'] = $info['pid'];
                }else{
                    $info['parentNode1'] = $info['pid'];
                }
            }
            $this->assign('info',$info);
        }
        $this->assign('params',$params);
        $this->display('edit');
    }

    private function saveAjax($params){
        $node1 = $params['parentNode1'];
        $node2 = $params['parentNode2'];
        if(!$node1&&!$node2){
            $params['pid'] = 0;
            $params['level'] = 1;
        }else{
            if($node1&&$node2){
                $params['pid'] = $node2;
                $params['level'] = 3;
            }
            if($node1&&!$node2){
                $params['pid'] = $node1;
                $params['level'] = 2;
            }
        }
        $res = $this->nodeModel->edit($params);
        $nodeId = $params['nodeId'];
        //$this->writelog($nodeId?'编辑':'添加','节点','',$res);
        $this->ajaxReturn($res);
    }

    //删除节点
    public function delete(){
        $params = I();
        $res = $this->nodeModel->del($params);
        //$this->writelog('删除','节点','',$res);
        $this->ajaxReturn($res);
    }

    //ajax节点数据
    public function nodesajax(){
       $params = I();
       $list = $this->nodeModel->getList($params);
       $this->ajaxReturn(info('0','查询成功',$list));
    }


}