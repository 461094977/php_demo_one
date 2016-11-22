<?php
namespace Common\Model;
use Think\Model;

class ThinkAccessModel extends Model {

    public $trueTableName = "think_access";

    public function __construct($name='',$tablePrefix='',$connection='') {
        parent::__construct($name,$tablePrefix,$connection);
    }

    public function getListAndTotal($filter,$field,$isPkIndex=false){
        $result['total'] = $this->getTotal($filter);
        if($result['total']){
             $result['data'] = $this->getList($filter,$field,$isPkIndex);
        }
        return $result;
    }

    private function filter($filter){
        modelArray($con,$filter,'roleId','role_id');
        return $this->where($con);
    }

    public function getTotal($filter){
        return $this->filter($filter)->count();
    }

    public function getList($filter,$field,$isPkIndex=false){
        $model = $this->filter($filter);
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
        $roleId = $data['roleId'];
        $nodeId = $data['nodeId'];
        if(!$nodeId){
            return info('1','请先选择权限节点');
        }
        $this->startTrans();
        $info = $this->del($data);
        if($info['result']){
            return $info;
        }
        if(!is_array($nodeId)){
            $nodeId = explode(',',$nodeId);
        }
        foreach($nodeId as $v){
            $isExitCon['role_id'] = $roleId;
            $isExitCon['node_id'] = $v;
            if($this->where($isExitCon)->getField('role_id')){
                continue;
            }
            $temp['role_id'] = $roleId;
            $temp['node_id'] = $v;
            $dbData[] = $temp;
        }
        if($dbData){
            $res = $this->addALL($dbData);
            if(!$res){
                $this->rollback();
                return info('1','权限分配失败');
            }
        }
        $this->commit();
        return info('0','权限分配成功',$data);
    }

    //删除角色权限
    public function del($filter){
        modelArray($con,$filter,'roleId','role_id');
        if(!$con){
            return info('1','删除异常');
        }
        $res = $this->where($con)->delete();
        if($res===false){
            return info('1','删除失败');
        }
        return info('0','删除成功');
    }
}