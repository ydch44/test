<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    'sex' => [
        '0' => '保密',
        '1' => '男',
        '2' => '女',
    ],

    'pageSize' => '20',

    'staticUrl' => 'http://b2b.jkbsapp.com/static/',

    'b2bUrl' => 'https://b2b.jkbsapp.com',

    'webUrl' => 'https://muyi.zydc1104.top',

    'imageStaticHost' => 'http://img.jkbsimg.com/',

    'oss'=>[
        'OSS_ACCESS_ID'=>'7yfFYrYDLcskTm0R',
        'OSS_ACCESS_KEY'=>'m8ndLGKmvlfknsGq3ABuP2iSWihEEV',
        //'OSS_ENDPOINT'=>'jkbs.oss-cn-hangzhou-internal.aliyuncs.com',
        'OSS_ENDPOINT'=>'jkbs.oss-cn-hangzhou.aliyuncs.com',
        'OSS_BUCKET'=>'jkbs'
    ],
    
    //文章分类
    'articleCategory' => [
        '1' => '后端',
        '2' => '数据库',
        '3' => '前端',
        '5' => '服务器',
        '10' => '财务管理',
        '4' => '漫画',
        '100' => '其他',
    ]
];
