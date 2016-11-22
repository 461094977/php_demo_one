<?php
/**
 * BaseModel 复用简单的查询
 * 复杂的查询model需重写
 */
namespace Common\Model;
use Think\Model;

class BaseModel extends Model {

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


    //子类重写
    protected function filter($filter){
        return $this->where($filter);
    }

    public function getTotal($filter){
        $model = $this->filter($filter);
        if(!$model){
            return 0;
        }
        return $model->count();
    }

    public function getList($filter,$field,$isPkIndex=false){
        $model = $this->filter($filter);
        if(!$model){
            return array();
        }
        $model = $this->extendsFilter($model);
        if($filter['p']&&$filter['pageSize']){
            $model = $model->page($filter['p'],$filter['pageSize']);
        }
        if($isPkIndex){
            $list = $model->getField($field);
        }else{
            if($field){
                $model = $model->field($field);
            }
            $list = $model->select();
        }
        if($list){
            $this->extListField($list,$filter);
        }
        return $list;
    }

    //拓展条件查询与查询总数分开
    protected function extendsFilter($model){
        return $model;
    }

    //关联表字段信息
    protected function extListField(&$list,$filter){
    }

    //当与getList不一致的时候可重写
    protected function filterRow($filter){
        return $this->filter($filter);
    }

    /**
    * 1行 主表1对多的关系
    * 1列
    * 1个字段
    */
    public function getRow($filter,$field,$isColumn=false){
        $model = $this->filterRow($filter);
        if($isColumn){
            $row = $model->getField($field,true);
        }elseif(!$field){
            $row = $model->find();
        }else{
           $row = $model->getField($field);
        }
        if($row){
            $this->extRowField($row,$filter);
        }
        return $row;
    }

    //关联表字段信息
    protected function extRowField(&$row,$filter){
    }

    //主键
    public function edit(){
        //1对多编辑
    }

    //1对1 //添加 更新 删除 根据主表pk

    //1对多 添加 更新 删除 根据主表pk

    public function del(){
    }

}