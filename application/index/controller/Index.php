<?php
namespace app\index\controller;

use think\Controller;
use wechat\WechatOauth;

class Index extends Controller
{
    public function index(){
        return 1;
    }
    public function login(){
        return $this->fetch();
    }

    /**
     * 微信扫码登录（暂不可测，需要开放平台注册网站信息）
     * @return string
     */
    public function wechatLogin(){
        $code=input('code',0);
        $wechat_oauth=new WechatOauth();
        $wechat_oauth->getUserAccessUserInfo($code,'snsapi_login');
    }

}
