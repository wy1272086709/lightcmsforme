<?php
namespace App\Utils;

class ConstantUtils {

    // 文件上传类型
    const UA_UPLOAD = 1;
    const SOURCE_UPLOAD = 2;
    const TASK_URL_UPLOAD = 3;
    const MODULE_UPLOAD = 4;
    const WORD_UPLOAD = 5;
    // 来源文件导入，但是是包含比率的
    const SOURCE_RATIO_UPLOAD = 6;


    // 1为广告, 2为搜索, 3为流量, 4为hao123
    const ADS_SERVICE = 1;
    const SEARCH_SERVICE = 2;
    const FLOW_SERVICE = 3;
    const HAO_123_SERVICE = 4;

    //平台
    const PC_PLATFORM = 0;
    const MOBILE_PLATFORM = 1;

    // 发布数值的方式.0为固定, 1为计划
    const FIXED_ISSUE_MODE = 0;
    const PLAN_ISSUE_MODE = 1;

    // 启用，禁用状态
    const ENABLE_STATUS = 1;
    const DISABLE_STATUS = 0;

    // UA 设置的四种方式
    const AUTO_UA   = 1;
    const CUSTOM_UA = 2;
    const BATCH_SET_UA = 3;
    const UPLOAD_UA    = 4;

    // 来源的四种设置方式
    const CUSTOM_SOURCE = 0;
    const BATCH_SET_SOURCE = 1;
    const UPLOAD_SOURCE = 2;
    const UPLOAD_RATIO_SOURCE = 3;


    // 任务页面的两种上传方式
    const TASK_URL_UPLOAD_STYLE = 1;
    const TASK_URL_BATCH_EXPORT_STYLE = 0;

    // 渠道，还是城市
    const AQ_TYPE_CHANNEL = 1; // 渠道
    const AQ_TYPE_CITY = 2; // 城市

    // 词典设置的两种设置方式
    const WORD_TEXT_EXPORT_STYLE = 1;
    const WORD_FILE_UPLOAD_STYLE = 2;

    // 时间设置的两种方式
    const TIMEZONE_QUXIAN_STYLE = 1; // 曲线设置
    const TIMEZONE_INTERVAL_STYLE = 2; // 时段设置

    // 设备类型
    const DEVICE_TYPES = [
      'PC'    => self::PC_PLATFORM,
      '移动端' => self::MOBILE_PLATFORM
    ];

    const BROWSER_IE = 1;
    const BROWSER_FIREFOX_CHROME = 2;
    const BROWSER_CHROME = 3;
    const BROWSER_OPERA = 4;
    const BROWSER_SAFARI = 5;

    // 浏览器类型
    const BROWSER_TYPES = [
        'IE浏览器'     => self::BROWSER_IE,
        '火狐浏览器'    => self::BROWSER_FIREFOX_CHROME,
        'Chrome浏览器' => self::BROWSER_CHROME,
        'Opera浏览器'  => self::BROWSER_OPERA,
        'Safari浏览器' => self::BROWSER_SAFARI
    ];
}