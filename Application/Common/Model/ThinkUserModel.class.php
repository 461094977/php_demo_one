<?php
namespace Common\Model;
use Think\Model;

class ThinkUserModel extends Model {
    public $trueTableName = "think_user";
    public $pk = "id";

     public function __construct($name='',$tablePrefix='',$connection='') {
        parent::__construct($name,$tablePrefix,$connection);
        $this->userRoleModel = getModel('ThinkRoleUser');
    }

    public function getListAndTotal($filter,$field=''){
        $result['total'] = $this->getTotal($filter);
        if($result['total']){
            $result['data'] = $this->getList($filter,$field);
        }
        return $result;
    }

    private function filter($filter,$field=''){
        modelArray($con,$filter,'userId','a.id');
        modelArray($con,$filter,'nameLogin','a.name');
        $name = $filter['name'];
        if($name){
            $con['a.name'] = array('like',"%{$name}%");
        }
        $fullname = $filter['fullname'];
        if($fullname){
            $con['a.fullname'] = array('like',"%{$fullname}%");
        }
        modelArray($con,$filter,'roleId','c.id');
        modelArray($con,$filter,'status','a.status',array(null));
        return $this->join('as a left join think_role_user as b on a.id = b.user_id')
                    ->join('left join think_role as c on c.id= b.role_id')
                    ->where($con)
                    ->field('a.*,b.role_id,c.name as role_name');
    }

    public function getTotal($filter){
        return $this->filter($filter)->count();
    }

    public function getList($filter,$field=''){
        $model = $this->filter($filter)->order('a.id desc');
        if($filter['p']&&$filter['pageSize']){
            $model = $model->page($filter['p'],$filter['pageSize']);
        }
        return $model->select();
    }

    public function getRow($filter,$field=''){
        return $this->filter($filter)->find();
    }

    public function edit($data){
        if(!$data['fullname']){
            return info('1','姓名不能为空');
        }
        if(!$data['name']){
            return info('1','用户名不能为空');
        }
        if(!$data['mobile']){
            return info('1','联系电话不能为空');
        }
        if(!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$data['fullname'])){
            return info('1','姓名需为中文',$data['fullname']);
        }
        if(!preg_match("/^[A-Za-z0-9_]+$/u",$data['name'])){
            return info('1','用户名只允许值为英文、数字和下划线');
        }
        if(!preg_match("/^[0-9]{8,20}$/u",$data['mobile'])){
            return info('1','联系电话需8到20位数字');
        }
        if(mb_strlen($data['fullname'],'UTF-8')<2||mb_strlen($data['fullname'],'UTF-8')>5){
            return info('1','姓名4到10个字符');
        }

        if(mb_strlen($data['name'],'UTF-8')<4||mb_strlen($data['name'],'UTF-8')>12){
            return info('1','用户名4到12个字符');
        }

        if($data['roleId']==ROlE_MANAGER_ID){
            if(!$data['userImg']){
                return info('1','该角色需上传图片');
            }
        }

        $check = $this->checkName($data);
        if($check['result']){
            return $check;
        }

        modelArray($con,$data,'userId','id');
        modelArray($dbData,$data,'fullname');
        modelArray($dbData,$data,'name');
        modelArray($dbData,$data,'mobile');
        modelArray($dbData,$data,'status','status',array(NULL));
        $date = date('Y-m-d H:i:s');
        if($data['userId']){
            $dbData['update_date'] = $date;
        }else{
           $dbData['create_date'] = $dbData['update_date'] = $date;
        }
        $password = $data['password'];
        if(mb_strlen($password,'UTF-8')<4||mb_strlen($password,'UTF-8')>12){
            return info('1','密码4到12个字符');
        }
        if($password!='default'){
            /*if(!preg_match("/^[A-Za-z0-9_]+$/u",$password)){
                return info('1','用户名需为数字,字母,下划线');
            }*/
            $dbData['password'] = md5($password);
        }

        modelArray($dbData,$data,'userImg','image');
        $this->startTrans();
        if(!$data['roleId']){
            $this->rollback();
            return info('1','请选择角色');
        }
        if($data['userId']){
            $data['save'] = true;
           if(
                ($res = $this->where($con)->save($dbData))===false
                ||($res = $this->userRoleModel->edit($data))===false){
                $this->rollback();
                return info('1','更新失败');
           }
        }else{
            if(!$res = $this->add($dbData)){
                $this->rollback();
                return info('1','添加失败');
            }
            $data['userId'] = $res;
            if(!$res = $this->userRoleModel->edit($data)){
                $this->rollback();
                return info('1','角色关联失败');
            }
        }
        $this->commit();
        return info('0','操作成功',$data['userId']);
    }

    private function checkName($data){
        $con['name'] = $data['name'];
        if($data['userId']){
            $con['id'] = array('neq',$data['userId']);
        }
        $res = $this->where($con)->getField('id');
        if($res){
            return info('1','用户名已存在');
        }
        return info('0','用户名可编辑');
    }

    public function del($filter){
        if($filter['userId']==ADMIN_USER_ID){
            return info('1','系统admin帐号,不能删除!');
        }
        modelArray($con,$filter,'userId','id');
        $res = $this->where($con)->delete();
        if($res){
            return info('0','操作成功');
        }else{
            return info('1','删除失败');
        }
    }

}