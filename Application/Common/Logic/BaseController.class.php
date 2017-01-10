<?php
namespace Common\Logic;
use Think\Controller;
use Common\Util\Rbac;
use Common\Logic\User;
use Common\Util\Operation;
class BaseController extends Controller{

    // 自定义 常量
    private function initDefine(){
        //广告系统url前缀部分
//        define('H5_GBUY_URL',C('H5_GBUY_IP').'/groupBuyingApi/');

        //admin帐号user_id
        define('ADMIN_USER_ID',1);
        //管理员角色ID
        define('ROLE_ADMIN_ID',1);
    }

    //分页页码初始化
    private function initparams(){
        if(!$_GET['p']){
            $_GET['p'] = 1;
        }
        if(!$_GET['pageSize']){
            $_GET['pageSize'] = 20;
        }
    }

    // 自定义  模板变量
    private function initTmplStr(){
        C('TMPL_PARSE_STRING.__CSS__',__ROOT__ . '/Public/css');
        C('TMPL_PARSE_STRING.__CSSMCA__',__ROOT__ . '/Public/css/' . MODULE_NAME.'/'.CONTROLLER_NAME.'_'.ACTION_NAME.'.css');
        C('TMPL_PARSE_STRING.__JS__',__ROOT__ . '/Public/js');
        C('TMPL_PARSE_STRING.__JSM__',__ROOT__ . '/Public/js/' . MODULE_NAME);
        C('TMPL_PARSE_STRING.__JSMC__',__ROOT__ . '/Public/js/' . MODULE_NAME.'/'.CONTROLLER_NAME);
        C('TMPL_PARSE_STRING.__JSMCA__',__ROOT__ . '/Public/js/' . MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'.js');
    }


    /**
     * $initParams
     *     isIgnoreLogin:表示忽略的登录验证
     *     isIgnoreAuth:
     */
    public function _initialize($initParams = []){
        //return ;
        $this->initDefine();
        $this->initparams();
        $this->initTmplStr();
        //登录判断
        if ($this->isVerifyLogin($initParams['isIgnoreLogin'])&&!$userId=User::sessionUserId()){
           $this->autherInfoReturn('尚未登录或登录有效时间过期,请重新登录!','201');
        }
        //权限判断
        if($this->isVerifyAuth($initParams['isIgnoreAuth'])&&!($userId==ADMIN_USER_ID||Rbac::accessUserByUrl())){
            $this->autherInfoReturn('没有权限!','210');
        }

        //输出用户信息 和 菜单列表
        if(User::sessionUserId()){
            $this->assign('topUserInfo',User::sessionUserInfo());
            $this->assign('leftMenuList',User:: sessionMenuAccess());
        }

        //日志预存参数
        Operation::param();

        $this->menuSelect();

        header("Content-type: text/html; charset=utf-8");
    }

    //判断是否ajax请求还是页面跳转
    private function isAjaxRequest(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            return true;
        }
        return false;
    }

    private function autherInfoReturn($data,$status = '0'){
        if($this->isAjaxRequest()){
            header("HTTP/1.1 300 auther access");
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(info($status,$data),JSON_UNESCAPED_UNICODE);exit;
        }else{

            if($status == '201'){
                $this->error($data,'/Admin/Login/index',3);exit;
            }else{
                $this->error($data);exit;
            }
        }
    }

    //记忆菜单
    private function menuSelect(){

        $selectMenu1Id = I('get.selectMenu1Id');
        $selectMenu2Id = I('get.selectMenu2Id');

        $prevSelectMenu1Id = session('selectMenu1Id');
        $prevSelectMenu2Id = session('selectMenu2Id');

        if(!$selectMenu1Id||!$selectMenu2Id){ //页面请求没有
            if($prevSelectMenu1Id&&$prevSelectMenu2Id){
                $selectMenu1Id = $prevSelectMenu1Id;
                $selectMenu2Id = $prevSelectMenu2Id;
            }

        }
        session('selectMenu1Id',$selectMenu1Id);
        session('selectMenu2Id',$selectMenu2Id);

        $this->assign('selectMenu1Id',$selectMenu1Id);
        $this->assign('selectMenu2Id',$selectMenu2Id);

    }

    //判断该请求是否需要验证登录
    private function isVerifyLogin($isIgnoreLogin){
        return $this->isIgnore($isIgnoreLogin);
    }

    //判断该请求是否需要验证权限权限
    private function isVerifyAuth($isIgnoreAuth){
        //忽略Compub模块权限验证
        record(MODULE_NAME);
        if(MODULE_NAME=='Compub'){
            return false;
        }
        return $this->isIgnore($isIgnoreAuth);
    }

    private function isIgnore($isIgnore){
        if(!$isIgnore){
            return true;
        }
        $isIgnore = array_flip(array_change_key_case(array_flip($isIgnore)));
        if(is_array($isIgnore)){
            if(in_array(ACTION_NAME,$isIgnore)){
                return false;
            }
        }else{
            //一个方法
            if($isIgnore==ACTION_NAME){
                return false;
            }
        }
        return true;
    }

    //操作日志 判断是否成功 默认写如日志
    protected function writelog($action = '',$object = '',$remark ='',$info = true){
        if($info===true){
            Operation::writelog($action,$object,$remark);return ;
        }
        if(array_key_exists('result',$info)&&$info['result']=='0'){
            Operation::writelog($action,$object,$remark);return ;
        }
    }

    //分页输出 $_GET['p']表是当前页
    //$istype 1表示普通页面分页 2：模态框分页
    protected function page($total,$pageSize,$istype='1'){
        $Page = new Page($total,$pageSize);
        $Page->isType = $istype;
        $show = $Page->show();
        $this->assign('page', $show);
    }

    /**
     * @desc 导出Excel文件
     */
    protected function echoExcel($list,$title,$filename='excel'){
        //ini_set('memory_limit','1024M'); //60万6列excel导出 本地速度可以 比字符串拼快
        $array = array();
        $this->dealExcelList($list,$title,$array);
        $content = join($array);
        $this->excel($content,$filename);
    }

    protected function excel($content,$filename='excel'){
        $filename=iconv('UTF-8','GBK',trim($filename).'.xls');
        header('Content-Type:application/vnd.ms-excel;charset=utf-8');
        header("Content-Disposition:attachment;filename=$filename");
        header("Pragma:no-cache");
        header("Expires:0");
        echo $content;
    }
    /**
     * excel导出数据处理
     * @param $list [][] 导出数据处理
     * @param $title [] 导出表头名称和键值 例子['店铺'=>'shop_name']
     */
    private function dealExcelList($list,$title,&$array){
        foreach($title as $k=>$key){
            if($key==end($title)){
                array_push($array, "{$k}\n");
            }else{
                array_push($array, "{$k}\t");
            }

        }
        foreach($list as $v){
            foreach($title as $key){
                if(is_array($key)){
                    $temp = call_user_func_array (array( $this ,  $key[1]), array($v[$key[0]]));
                    if($temp === false){
                        $temp = "回调异常";
                    }
                }else{
                    $temp = $v[$key];
                }
                if($key==end($title)){
                    $temp = "=\"".$temp."\""."\n";
                }else{
                    $temp = "=\"".$temp."\""."\t";
                }
                array_push($array, $temp);
            }
        }
    }



}