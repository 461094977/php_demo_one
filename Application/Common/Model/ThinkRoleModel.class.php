<?php
namespace Common\Model;
use Think\Model;

class ThinkRoleModel extends Model {
    public $trueTableName = "think_role";
    public $pk = "id";

    private $accessModel;
    public function __construct($name='',$tablePrefix='',$connection='') {
        parent::__construct($name,$tablePrefix,$connection);
        $this->accessModel = D('ThinkAccess');
    }

    public function getListAndTotal($filter,$field,$isPkIndex=false){
        $result['total'] = $this->getTotal($filter);
        if($result['total']){
             $result['data'] = $this->getList($filter,$field,$isPkIndex);
        }
        return $result;
    }

    private function filter($filter){
        modelArray($con,$filter,'roleId','id');
        modelArray($con,$filter,'name');
        $con['status'] = 1;
        return $this->where($con);
    }

    public function getTotal($filter){
        return $this->filter($filter)->count();
    }

    public function getList($filter,$field,$isPkIndex=false){
        $model = $this->filter($filter)->order('id desc');
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
        if($filter['getNodes']){
            $nodes = $this->accessModel->getRow(array('roleId'=>$filter['roleId']),'node_id',true);
            $row['nodes'] = $nodes;
        }
        return $row;
    }

    //主键
    public function edit($data){

        if(!$data['name']){
            return info('1','数据不符合要求,请重新填写');
        }
        if(mb_strlen($data['name'],'UTF-8')<3||mb_strlen($data['name'],'UTF-8')>10){
            return info('1','角色名称3到10个字符');
        }
        if(!$data['remark']){
            return info('1','数据不符合要求,请重新填写');
        }
        if(mb_strlen($data['remark'],'UTF-8')<3||mb_strlen($data['name'],'UTF-8')>20){
            return info('1','详述3到20个字符');
        }

        modelArray($con,$data,'id');
        modelArray($dbData,$data,'name');
        modelArray($dbData,$data,'remark');
        $dbData['pid'] = 0;
        $this->startTrans();
        if($data['id']){
            $data['roleId'] = $data['id'];
           if(
                ($res = $this->where($con)->save($dbData))===false
                ||($res =$this->accessModel->edit($data))===false){
                $this->rollback();
                return info('1','更新更新失败');
           }
        }else{
            if(!$res = $this->add($dbData)){
                $this->rollback();
                return info('1','角色添加失败');
            }
            $data['roleId'] = $res;
            if(!$res = $this->accessModel->edit($data)){
                $this->rollback();
                return info('1','角色关联权限失败');
            }
        }
        $this->commit();
        return info('0','操作成功',$data['roleId']);
    }

    public function del($filter){
        if($filter['roleId']==ROlE_MANAGER_ID||$filter['roleId']==ROLE_ADMIN_ID){
            return info('1','该角色不可删除');
        }
        modelArray($con,$filter,'roleId','id');
        $res = $this->where($con)->delete();
        if($res===false){
            return info('1','删除失败');
        }else{
            return info('0','删除成功',$filter['roleId']);
        }
    }
}