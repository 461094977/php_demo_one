<?php
namespace Common\Model;
use Think\Model;

class ThinkNodeModel extends Model {
    public $trueTableName = "think_node";
    public $pk = "id";

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
        modelArray($con,$filter,'level');
        modelArray($con,$filter,'nodeId','id');
        modelArray($con,$filter,'name');
        modelArray($con,$filter,'pid');
        modelArray($con,$filter,'parentId','pid',array('null'));
        $con['status'] = 1;
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
            $list = $model->getField($field);
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
        }elseif(!$field){
            $row = $model->find();
        }else{
           $row = $model->getField($field);
        }
        return $row;
    }

    //主键
    public function edit($data){
        modelArray($con,$data,'nodeId','id');
        modelArray($dbData,$data,'name');
        modelArray($dbData,$data,'title');
        modelArray($dbData,$data,'remark');
        modelArray($dbData,$data,'pid','pid',array(null));
        modelArray($dbData,$data,'level');
        $dbData['sort'] = 0;
        $dbData['status'] = 1;
        if(!$data['name']){
            return info('1','请输入节点名');
        }
        if(!$data['title']){
            return info('1','请输入名称');
        }
        $is = $this->checkNodeName($data);
        if($is['result']){
            return $is;
        }
        if($data['nodeId']){
            $res = $this->where($con)->save($dbData);
            if($res===false){
                $info = info('1','更新失败');
            }else{
                $info = info('0','更新成功');
            }
        }else{
            $res = $this->add($dbData);
            if(!$res){
                    $info = info('1','添加失败');
            }else{
                $info = info('0','添加成功',$res);
            }
        }
        return $info;
    }

    private function checkNodeName($data){
        $con['name'] = $data['name'];
        $con['pid'] = $data['pid'];
        $con['level'] = $data['level'];
        $nodeId = $data['nodeId'];
        if($nodeId){
            $con['id'] = array('neq',$nodeId);
        }
        $res = $this->where($con)->getField('id');
        if($res){
            return info('1','同级节点名已存在',$res);
        }else{
            return info('0','节点名不存在');
        }
    }

    public function del($filter){
        modelArray($con,$filter,'nodeId','id');
        $info = $this->where($con)->find();
        if($info){
            if($info['level'] == 2){
                if($info['id']){
                    $level3Ids = $this->where(array('pid'=>$info['id'],'level'=>3))->getField('id',true);
                    $ids = array_merge(array(),$level3Ids?$level3Ids:array());
                }
            }
            if($info['level'] == 1){
                if($info['id']){
                    $level2Ids = $this->where(array('pid'=>$info['id'],'level'=>2))->getField('id',true);
                    if($level2Ids){
                        $level3Ids = $this->where(array('pid'=>array('in',$level2Ids),'level'=>3))->getField('id',true);
                    }
                    $ids = array_merge($level2Ids?$level2Ids:array(),$level3Ids?$level3Ids:array());
                }
            }
            $ids[] = $info['id'];
            if($ids){
                $conList['id'] = array('in',$ids);
                $res = $this->where($conList)->delete();
            }
        }

        if($res===false){
            return info('1','删除失败');
        }else{
            return info('0','删除成功');
        }
    }
}