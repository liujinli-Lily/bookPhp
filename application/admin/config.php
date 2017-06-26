<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
return [

    //模板参数替换
    'view_replace_str' => array(
        '__CSS__' => '/static/admin/css',
        '__JS__' => '/static/admin/js',
        '__IMG__' => '/static/admin/images',
    ),

    //管理员状态
    'user_status' => [
        '1' => '正常',
        '2' => '禁止登录'
    ],

    //角色状态
    'role_status' => [
        '1' => '启用',
        '2' => '禁用'
    ],

    //默认过滤
    'default_filter' => ['strip_tags', 'htmlspecialchars'],

    //加密串
    'salt' => 'wZPb~yxvA!ir38&Z',

    //备份数据地址
    'back_path' => APP_PATH . '../back/'
];
