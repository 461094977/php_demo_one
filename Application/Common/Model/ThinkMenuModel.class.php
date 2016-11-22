<?php
namespace Common\Model;
use Think\Model;

class ThinkMenuModel extends Model {
    public $trueTableName = "think_menu";
    public $pk = "id";

    public function __construct($name='',$tablePrefix='',$connection='') {
        parent::__construct($name,$tablePrefix,$connection);
        $this->accessModel = D('ThinkAccess');
        $this->nodeModel = getModel('ThinkNode');
    }

    public function getListAndTotal($filter,$field,$isPkIndex=false){
        $result['total'] = $this->getTotal($filter);
        if($result['total']){
             $result['data'] = $this->getList($filter,$field,$isPkIndex);
        }
        return $result;
    }

    private function filter($filter){
        modelArray($con,$filter,'level');
        modelArray($con,$filter,'nodeId','node_id');
        modelArray($con,$filter,'menuId','id');
        modelArray($con,$filter,'isShow','is_show',array(NULL));
        return $this->where($con);
    }

    public function getTotal($filter){
        return $this->filter($filter)->count();
    }

    public function getList($filter,$field,$isPkIndex=false){
        $model = $this->filter($filter);
        $mode = $model->order('sort desc');
        if($filter['p']&&$filter['pageSize']){
            $model = $model->page($filter['p'],$filter['pageSize']);
        }
        if($isPkIndex){
            $list = $model->getField($filed);
        }else{
            $list = $model->select();
        }
        return $list;
    }

    /**
    * 1行 主表1对多的关系
    * 1列
    * 1个字段
    */
    public function getRow($filter,$field,$isColumn=false){
        $model = $this->filter($filter);
        if($isColumn){
            $row = $model->getField($field,true);
        }elseif(!$filed){
            $row = $model->find();
        }else{
           $row = $model->getField($field);
        }
        return $row;
    }

    //主键
    public function edit($data){
        $info = $this->checkMenuTitle($data);
        if($info['result']){
            return $info;
        }
        if($data['level'] == 2){
            $info = $this->getNodeIdByPath($data['path']);
            if($info['result']){
                return $info;
            }
            $data['nodeId'] = $info['data'];
        }else{
            $data['nodeId'] = 0;
            $data['path'] = '';
        }
        modelArray($con,$data,'menuId','id');
        modelArray($dbData,$data,'title');
        modelArray($dbData,$data,'pid');
        modelArray($dbData,$data,'level');
        modelArray($dbData,$data,'path');
        modelArray($dbData,$data,'sort');
        modelArray($dbData,$data,'nodeId','node_id');
        modelArray($dbData,$data,'isShow','is_show',array(NULL));
        if($data['menuId']){
            $res = $this->where($con)->save($dbData);
            if($res===false){
                return info('1','更新失败');
            }
        }else{
            $res = $this->add($dbData);
            if(!$res){
                return info('1','添加失败');
            }
        }
        return info('0','操作成功',$res);
    }

    //过滤同级菜单名称重复
    private function checkMenuTitle($data){
        $menuId = $data['menuId'];
        $title = $data['title'];
        $level = $data['level'];
        $pid = $data['pid'];
        if(!$title){
            return info('1','请输入菜单名称',$menuId);
        }
        if($menuId){
            $menuId = $this->where(array('title'=>$title,'level'=>$level,'pid'=>$pid,'id'=>array('neq',$menuId)))->getField('id');
        }else{
            $menuId = $this->where(array('title'=>$title,'level'=>$level,'pid'=>$pid))->getField('id');
        }
        if($menuId){
                return info('1','同级菜单名已存在',$menuId);
        }
        return info('0','菜单名可使用');
    }

    //根据路径获取节点id
    private function getNodeIdByPath($path){
        $pathArr = explode('/',$path);
        if(count($pathArr)!=4){
            return info('1','路径不全');
        }
        $module = $pathArr[1];
        if(!$module){
            return info('1','路径模块名不能为空');
        }
        $nodeId = $this->nodeModel->getRow(array('name'=>$module,'pid'=>0,'level'=>1),'id');
        if(!$nodeId){
            return info('1','路径模块名在节点中没有');
        }
        $controller = $pathArr[2];
        if(!$controller){
            return info('1','路径控制器名不能为空');
        }
        $nodeId = $this->nodeModel->getRow(array('name'=>$controller,'pid'=>$nodeId,'level'=>2),'id');
        if(!$nodeId){
            return info('1','路径控制器名在节点中没有');
        }

        $action = $pathArr[3];
        if(!$action){
            return info('1','路径方法名不能为空');
        }
        $nodeId = $this->nodeModel->getRow(array('name'=>$action,'pid'=>$nodeId,'level'=>3),'id');
        if(!$nodeId){
            return info('1','路径方法名在节点中没有');
        }
        return info('0','路径节点',$nodeId);
    }

    public function del($filter){
        $menuId = $filter['menuId'];
        if(in_array($menuId,C('MENU_ID_UNDELETE'))){
            return info('1','系统菜单不能删除!');
        }
        modelArray($con,$filter,'menuId','id');
        $info = $this->where($con)->find();
        if($info){
            if($info['level']==1){
                if($info['id']){
                    $level2Ids = $this->where(array('pid'=>$info['id'],'level'=>2))->getField('id',true);
                    $ids = array_merge(array(),$level2Ids?$level2Ids:array());
                }
            }
            $ids[] = $info['id'];
            if($ids){
                $conList['id'] = array('in',$ids);
                $res = $this->where($conList)->delete();
            }
        }
        if($res === false){
            return info('1','删除失败');
        }else{
            return info('0','删除成功');
        }
    }
}