<?php
/**
 * Created by PhpStorm.
 * User: ling
 * Date: 21/11/2016
 * Time: 2:06 PM
 */
return array(
    //'配置项'=>'配置值'

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '119.29.26.196', // 服务器地址
    'DB_NAME'               =>  'ling_php_db',          // 数据库名
    'DB_USER'               =>  'ling',      // 用户名
    'DB_PWD'                =>  'ling',          // 密码

    // 加载扩展配置文件
    'LOAD_EXT_CONFIG' => 'common_config,menus_config,nodes_config',
);