<?php
/**
 * Created by PhpStorm.
 * User: ling
 * Date: 21/11/2016
 * Time: 2:24 PM
 */
return array(
    //'配置项'=>'配置值'

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式       windows下为pathinfo 1,linux下为rewrite 2 ??

    //语言包配置
//    'LANG_AUTO_DETECT' => FALSE, //关闭语言的自动检测，如果你是多语言可以开启
//    'LANG_SWITCH_ON' => TRUE, //开启语言包功能，这个必须开启
//    'DEFAULT_LANG' => 'zh-cn', //zh-cn文件夹名字

    /* 日志设置 */
    'LOG_RECORD'            =>  true,   // 默认不记录日志
    'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR,WARN,INFO,DEBUG,SQL',// 允许记录的日志级别
    'LOG_EXCEPTION_RECORD'  =>  true,    // 是否记录异常信息日志

    /* 错误设置 */
    'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息

    // 默认进入页面！ (首页)
    'DEFAULT_MODULE'        =>  'Admin',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Login', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称
);