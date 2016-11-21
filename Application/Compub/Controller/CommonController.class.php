<?php
namespace Compub\Controller;
use Common\Logic\BaseController;
use Common\Logic\Upload;

class CommonController extends BaseController{

    //单文件上传
    public function upload(){
        $params = I();
        $form_remark=I('post.form_remark');
        $info = Upload::file($params['config'],array($params['width'],$params['height']),$params['size']);
        if($info['result']){//上传失败
            $this->ajaxReturn($info);
        }
        $url = Upload::uploadApi(ROOT.'/'.$info['data']['path'],$params['isAllUrl']);
        $data['url'] = $url;
        $data['form_remark'] = $form_remark;
        $this->ajaxReturn(info('0','图片上传成功',$data));
    }

    //图片上传
    public function uploadpic(){
        $params = I('get.');
        $form_remark=I('post.form_remark');
        $config = array(
          'exts' => array('jpg','png'),
            'mimes' => array('image/jpg','image/png','image/jpeg','image/pjpeg')
        );
        $info = Upload::file($config,array($params['width'],$params['height']),$params['size']);
        if($info['result']){//上传失败
            $this->ajaxReturn($info);
        }
        $url = Upload::uploadApi(ROOT.'/'.$info['data']['path'],$params['isAllUrl']);
        $data['url'] = $url;
        $data['form_remark'] = $form_remark;
        $this->ajaxReturn(info('0','图片上传成功',$data));
    }


    public function uploadEditer(){//唉坑
        $params = I();
        $params['size'] = 1024*1024;
        $form_remark=I('post.form_remark');
        $info = Upload::file($config,array($params['width'],$params['height']),$params['size']);
        if($info['result']){//上传失败
            $this->ajaxReturn($info);
        }
        $url = Upload::uploadApi(ROOT.'/'.$info['data']['path'],true);
        $data['url'] = $url;
        $data['form_remark'] = $form_remark;
        $this->ajaxReturn(['error'=>0,'url'=>$url,'message'=>'上传成功']);
    }

    //单文件上传iframe方式
    public function uploadiframe(){
        $params = I();
        $form_remark=I('post.form_remark');
        $info = Upload::file($config,array($params['width'],$params['height']),$params['size']);
        if($info['result']){//上传失败
            $this->fileIframeEcho($info);
        }
        $url = Upload::uploadApi($info['data']['path'],$params['isAllUrl']);
        $data['url'] = $url;
        $data['form_remark'] = $form_remark;
        $this->fileIframeEcho(info('0','图片url',$data));
    }

    //iframe异常上传
    private function fileIframeEcho($data){
        echo "<script type='text/javascript'>parent.msg_callback('".json_encode($data)."');</script>";
    }

    /**
     * 省市区查询
     * 参数:$params['parentId'] = 001;
     */
    public function zone(){
        $params = I();
        $this->baseZoneModel = getModel('BaseZone');
        $params['pageSize'] = '';
        if(!$params['parentId']){
            $params['level_num'] = 1;
        }
        $list = $this->baseZoneModel->getList($params);
        $this->ajaxReturn(info('0','查询地区列表',$list));
    }


/** *************************************************************************** */

    //kindeditor上传图片
    public function kindeditor_upload_images(){
        $params = I();
        $params['size'] = 2*1024*1024;
        $info = Upload::file($config,array($params['width'],$params['height']),$params['size']);
        if($info['result']){//上传失败
            header('Content-type: text/html; charset=UTF-8');
            echo json_encode(array('error' => 1, 'message' => $info['message']));
            exit;
        }
        $res = Upload::lingUploadApi($info['data']['path'],$params['isAllUrl']);
        header('Content-type: text/html; charset=UTF-8');
        echo json_encode(array('error' => 0, 'url' =>$res['data']['imagePath']));
    }

    /**  上传文件..
     *  form_remark   在连续上传多个文件时 作为返回到html 的标示
     */
    function uploadfile(){
        // 验证上传 参数
        require_once APP_PATH.'Compub/Common/UploadParamAndError.php';
        $params = I('');
        $upload_type = isset($params['upload_type']) ? $params['upload_type']:'None';
        $upload_params = call_user_func('getParamsBy'.$upload_type,null);// 获取上传参数
        $file_info = $this->analyzeFiles($_FILES); //获取上传文件的参数
        record([$params,$upload_params,$file_info],'Ling');
        if(!$file_info){
            $this->fileIframeEcho(info(604,'文件不存在','input->name'));exit;
        }
        $check_result = $this->checkUploadError($upload_params,$file_info);  //验证
        if($check_result['result'] !== 200){
            $error =call_user_func('getErrorMsgBy'.$upload_type,$check_result);// 获取 出错 提示
            $this->fileIframeEcho($error);exit;
        }
        $info = Upload::lingFile();
        if($info['result']){//上传失败
            $this->ajaxReturn($info);
        }
        $res = Upload::lingUploadApi(ROOT.'/'.$info['data']['path']);
        $data['url'] = $res['data']['imagePath'];
        $data['url_cut'] = str_replace(C('URL_UPLOAD_PREFIX'),'',$data['url']);
        $data['key_name'] = $file_info['key_name'];
        $this->fileIframeEcho(info(200,'图片上传成功',$data));exit;
    }
    
    /** 将上传到后台服务器的 $file文件 进行分析 --> name type tmp_name error size
     * name --> 获取文件名称 和后缀
     * type error --> 暂时没有 type 相当于 mime
     * tmp_name --> 缓存地址 当上传的是图片时 通过这东东和getimagesize 获取图片长宽
     * size --> 大小 b单位
     */
    function analyzeFiles($file){
//        record($file,'Ling--$file');
        if(!$file){  // file 不存在  --> php.ini 中 post 时间过短(上传文件过大)
            return false;
        }
        $file_names = array_keys($file);
        $file_info['key_name'] = $file_names[0];
        foreach($file[$file_info['key_name']] as $key => $value){
            $file_info[$key] = $value;
            if($key == 'name'){
                $value = explode('.',strrev($value));
                $file_info['ext'] = strtolower(strrev($value[0]));
            }
        }
        return $file_info;
    }
    /** 验证 上传的文件是否符合 规则
     */
    function checkUploadError($upload_params,$file_info){ // 验证
//        record([$upload_params,$file_info],'Ling');
        if($upload_params == null || $file_info == null){
            return info(604,'没有获取到 文件参数 !!',[$upload_params,$file_info]);
        }
        record('check_size');
        if(array_key_exists('size',$upload_params) && $upload_params['size'] != '' && isset($upload_params['size'])){
            if(((int)($upload_params['size'])) < (int)$file_info['size']){
                return info(601,'',$upload_params);
            }
        }
        record('check_ext');
        if( $upload_params['exts'] != '' && $upload_params['exts']){
            record(in_array(strtolower($file_info['ext']),$upload_params['exts']));
            if(!in_array(strtolower($file_info['ext']),$upload_params['exts'])){
                return info(602,'',$upload_params);
            }
        }
        record('check_width_height');
        if( $upload_params['width'] && $upload_params['height'] ) {
            if(isset($file_info['tmp_name'])){
                list($width, $height) = getimagesize($file_info['tmp_name']);
                if((int)$upload_params['width'] != $width || (int)$upload_params['height'] != $height){
                    return info(603,'',$upload_params);
                }
            }
        }
        record('check -->over!!');
        return info(200,'验证通过',null);
    }
    /** 如果文件过大,会导致上传出错,所有要在 html 先将 size 进行预处理!!
     */
    function htmlCheckUpload(){
        require_once APP_PATH.'Compub/Common/UploadParamAndError.php';
        $params = I('post.');
        $upload_type = isset($params['upload_type']) ? $params['upload_type']:'None';
        $upload_params = call_user_func('getParamsBy'.$upload_type,null);// 获取上传参数
        record([$params,$upload_params,$upload_type],'Ling');
        $check_result =$this->checkUploadError($upload_params,$params['file_info']); //验证
        if($check_result['result'] == 200){
            $this->ajaxReturn(info(200,'获取成功',[$upload_params,$params['file_info']]));exit;
        }else{
            record([$upload_type,$check_result]);
            try{
                $error =call_user_func('getErrorMsgBy'.$upload_type,$check_result);// 获取 出错 提示
            }catch (\Exception $e){
                $error =call_user_func('getErrorMsgByNone',$check_result);
            }
            $this->ajaxReturn($error);exit;
        }
    }



}