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
     * 微信扫码登录
     * @return string
     */
    public function wechatLogin(){
        $wechat_oauth=new WechatOauth();
        $wechat_oauth->getUserAccessUserInfo();
    }

}
