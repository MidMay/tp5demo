<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
if($_SERVER['SERVER_SOFTWARE']=='zlk_nginx'){
    $environment='product';
}elseif ($_SERVER['SERVER_SOFTWARE']=='test_nginx'){
    $environment='test';
}else{
    $environment='local';
}
if(defined('APP_MODE')&&APP_MODE=='cli'){
    $environment='product';
}
define('DEMOENV',$environment);

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
