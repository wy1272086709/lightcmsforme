<?php
return [
    // 超级管理员。不受权限控制
    'superAdmin' => [
        1, 41
    ],

    // 各类缓存KEY
    'cache_key' => [
        'config' => 'config'
    ],
    // 加载数据库自定义配置
    'light_config' => false,

    // 数据库表字段类型 参考：https://laravel.com/docs/5.5/migrations#columns
    'db_table_field_type' => [
        'char',
        'string',
        'text',
        'mediumText',
        'longText',
        'integer',
        'unsignedInteger',
        'tinyInteger',
        'unsignedTinyInteger',
        'smallInteger',
        'unsignedSmallInteger',
        'mediumInteger',
        'unsignedMediumInteger',
        'bigInteger',
        'unsignedBigInteger',
        'float',
        'double',
        'decimal',
        'unsignedDecimal',
        'date',
        'dateTime',
        'dateTimeTz',
        'time',
        'timeTz',
        'timestamp',
        'timestampTz',
        'year',
        'binary',
        'boolean',
        'enum',
        'json',
        'jsonb',
        'geometry',
        'geometryCollection',
        'ipAddress',
        'lineString',
        'macAddress',
        'multiLineString',
        'multiPoint',
        'multiPolygon',
        'point',
        'polygon',
        'uuid',
    ],

    // 表单类型
    'form_type' => [
        'input' => '短文本（input）',
        'textArea' => '长文本（textarea）',
        'richText' => '富文本',
        'password' => '密码字符',
        'option' => '单选框',
        'checkbox' => '复选框',
        'select' => '下拉选择',
        'upload' => '图片上传',
        'datetime' => '日期时间',
        'date' => '日期',
        'reference_category' => '引用分类数据',
        'reference_admin_user' => '引用管理员数据'
    ],

    // NEditor相关
    'neditor' => [
        'disk' => 'admin_img',
        'upload' => [
            'imageMaxSize' => 8 * 1024 * 1024, /* 上传大小限制，单位B */
            'imageAllowFiles' => ['.png', '.jpg', '.jpeg', '.gif', '.bmp'], /* 上传图片格式显示 */
        ]
    ],

    // 三方登录
    'auth_login' => [
        'weibo' => [
            'client_id' => env('WEIBO_CLIENT_ID', ''),
            'client_secret' => env('WEIBO_CLIENT_SECRET', ''),
            'redirect' => env('WEIBO_REDIRECT', ''),
        ],
        'qq' => [
            'client_id' => env('QQ_CLIENT_ID', ''),
            'client_secret' => env('QQ_CLIENT_SECRET', ''),
            'redirect' => env('QQ_REDIRECT', ''),
        ],
        'wechat' => [
            'client_id' => env('WECHAT_CLIENT_ID', ''),
            'client_secret' => env('WECHAT_CLIENT_SECRET', ''),
            'redirect' => env('WECHAT_REDIRECT', ''),
        ],
    ],
    'mn_interval_time' => 1200,
    'province_pinyin'  => [
        '北京'   => 'BeiJing',
        '上海'   => 'ShangHai',
        '天津'   => 'TianJin',
        '重庆'   => 'ChongQing',
        '香港'   => 'XiangGang',
        '澳门'   =>  'Aomen',
        '安徽'   => 	'AnHui',
        '福建'   => 	'FuJian',
        '广东'   => 	'GuangDong',
        '广西'   => 	'GuangXi',
        '贵州'   => 	'GuiZhou',
        '甘肃'   => 	'GanSu',
        '河北'   => 	'HeBei',
        '河南'   => 	'HeNan',
        '黑龙江' =>  'HeiLongJiang',
        '湖北'   => 	'HuBei',
        '湖南'   => 	'HuNan',
        '吉林'   => 	'JiLin',
        '江苏'   => 	'JiangSu',
        '江西'   => 	'JiangXi',
        '辽宁'   => 	'LiaoNing',
        '内蒙古'  => 'NeiMengGu',
        '宁夏'   => 	'NingXia',
        '青海'   => 	'QingHai',
        '陕西'   => 	'ShanXi',
        '山西'   => 	'ShanXi',
        '山东'   => 	'ShanDong',
        '四川'   => 	'SiChuan',
        '台湾'    =>	'TaiWan',
        '西藏'    =>	'XiZang',
        '新疆'    =>	'XinJiang',
        '云南'    =>	'YunNan',
        '浙江'    =>	'ZheJiang',
        '海南'    => 'HaiNan'
    ]
];
