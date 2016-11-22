<?php
namespace Common\Model;

class ThinkOperationLogModel extends BaseModel {

    public $trueTableName = "think_operation_log";

    protected function filter($filter){
        modelArray($con,$filter,'id','operation_id');
        modelArray($con,$filter,'userId','operation_user_id');
        //modelArray($con,$filter,'userName','operation_user');
        $name = $filter['userName'];
        if($name){
            $con['operation_user'] = array('like',"%{$name}%");
        }
        $createTime = $filter['createTime'];
        if($createTime){
            $con['operation_time'] = array('between',array("{$createTime} 00:00:00","{$createTime} 23:59:59"));
        }
        modelArray($con,$filter,'action','operation_node');
        modelArray($con,$filter,'model','operation_model');
        $con['status'] = 1;
        return $this->where($con);
    }

    protected function extendsFilter($model){
        return $model->order('operation_id desc');
    }

    //继承BaseModel方法getField和getRow

    public function del($filter){
        modelArray($con,$filter,'operationId','operation_id');
        $dbData['status'] = 0;
        $res = $this->where($con)->save($dbData);
        if(!$res){
            return info('1','删除日志失败');
        }
        return info('0','删除成功');
    }

}