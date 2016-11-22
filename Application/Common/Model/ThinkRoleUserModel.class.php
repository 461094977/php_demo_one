<?php
namespace Common\Model;
use Think\Model;

class ThinkRoleUserModel extends Model {
    public $trueTableName = "think_role_user";

    public function edit($data){
        modelArray($con,$data,'userId','user_id');
        modelArray($dbData,$data,'roleId','role_id');
        if(!$data['roleId']){
            $dbData['role_id'] = '0';
        }
        if(!$con){
            return true;
        }
        if($data['save']===true&&$this->where($con)->getField('user_id')){
           $res = $this->where($con)->save($dbData);
        }else{
            modelArray($dbData,$data,'userId','user_id');
            $res = $this->add($dbData);
        }
        return $res;
    }

}