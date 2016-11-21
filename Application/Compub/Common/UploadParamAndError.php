<?php
/**
 * Created by PhpStorm.
 * User: ling
 * Date: 9/13/16
 * Time: 2:09 PM
 */

/**
 * @return array
 *               如果值为0或 key 不存在 则 参数来自 upload.class.php
 * 在添加 上传有关信息之后 需更改 upload.class.php 中的相关配置 使其包括以下所有限制条件 (width存在,height 一定也要存在)
 * 可在里面直接查数据库获取params
 */
function getParamsByNone(){
    return ['size'=>0,
        'exts'=>'',
        'width'=>0,
        'height'=>0,];
}
/** 错误提示模板 .....
 */
function getErrorMsgByNone($error_info)
{   //1 =>size 2 =>exts 3 width & height
    switch ($error_info['result']) {
        case 601:
            $message = '图片大小不能超过'.$error_info['data']['size'].'M';
            break;
        case 602:
            $message = '不允许上传该类型文件,请上传:  '.implode(',',$error_info['data']['exts']).'   类型文件';
            break;
        case 603:
            $message = '上传的图片长宽不符合规格,长宽应为:'.$error_info['data']['width'].'*'.$error_info['data']['height'];
            break;
        default:
            $message = $error_info['message'];
            break;
    }
    return info($error_info['result'],$message,$error_info);
}
function getErrorMsgByNone1($error_info)
{   //1 =>size 2 =>exts 3 width & height
    switch ($error_info['result']) {
        case 601:
        case 602:
        case 603:
            $message = '请上传 ';
            if($error_info['data']['width']){
                $message = $message.'尺寸 '.$error_info['data']['width'].'*'.$error_info['data']['height'];
            }
            $message = $message.'小于 '.($error_info['data']['size']/1024/1024).' M的 '.implode(',',$error_info['data']['exts']).' 图片';
            break;
        default:
            $message = $error_info['message'];
            break;
    }
    return info($error_info['result'],$message,$error_info);
}
// banner
function getParamsByBannerOne(){
    return ['size'=>5*1024*1024,
        'exts'=>['jpg','png'],
        'width'=>0,
        'height'=>0,];
}
function getErrorMsgByBannerOne($error_info){
    return getErrorMsgByNone1($error_info);
}

// 商品
function getParamsByShopOne(){
    return ['size'=>2*1024*1024,
        'exts'=>['jpg','png']
    ];
}
function getErrorMsgByShopOne($error_info){
    return getErrorMsgByNone1($error_info);
}