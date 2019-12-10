<?php

namespace wechat;
use think\Config;


/**
 * @package 微信授权控制器
 */

class WechatOauth
{


    //微信授权配置信息
    private $wechat_config = [
        'appid' => '',
        'appsecret' => '',
    ];

    public function __construct()
    {
        $this->wechat_config = $this->wechatConfig();
    }

    /**
     * 获取秘钥配置
     * @return [type] 数组
     */

    public function wechatConfig()
    {
        $config=Config::load(APP_PATH.'config/wechat.php');
        $wechat_config['appid'] = $config['basic']["appid"];
        $wechat_config['appsecret'] = $config['basic']["appsecret"];
        return $wechat_config;

    }

    /**
     * 获取access_token和openid
     * @param string $code
     * @param string $scope
     * @return array|mixed
     */
    public function getUserAccessUserInfo($code = "",$scope='')
    {
        if (empty($code)) {
            $baseUrl = request()->url(true); //回调地址
            $state=md5('pkvb2gfh65dt');
            $url = $this->getAuthorizeCode($baseUrl,$scope,$state);
            Header("Location: $url");
            exit();
        } else {
            $data = $this->getOauthAccessToken($code); //获取access_token 和 open_id
            /*返回示例{
                "access_token":"ACCESS_TOKEN",
                "expires_in":7200,
                "refresh_token":"REFRESH_TOKEN",
                "openid":"OPENID",
                "scope":"SCOPE"
             }*/
            return $this->getUserInfo($data['access_token'],$data['openid']);
        }
    }

    /**
     * 获取access_token之前 获取code
     * @param string $redirect_url
     * @param string $scope
     * @param string $state
     * @return string
     */
    private function getAuthorizeCode($redirect_url = "",$scope='',$state = '1')
    {
        $redirect_url = urlencode($redirect_url);
        if ($scope=='snsapi_login'){
            return "https://open.weixin.qq.com/connect/qrconnect?appid={$this->wechat_config['appid']}&redirect_uri={$redirect_url}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        }
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->wechat_config['appid'] . "&redirect_uri=" . $redirect_url . "&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
    }

    /**
     * 获取access_token（通过code）
     * @param $code
     * @return mixed
     */
    private function getOauthAccessToken($code)
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->wechat_config['appid'] . '&secret=' . $this->wechat_config['appsecret'] . '&code=' . $code . '&grant_type=authorization_code';
        $data = $this->https_request($url);
        return $data;
    }

    /**
     * 通过 access_token 和 code获取用户openid以及用户的微信号信息
     * @param $access_token
     * @param $openid
     * @return array|mixed
     */
    private function getUserInfo($access_token,$openid)
    {
        if (!$access_token) {
            return [
                'code' => 0,
                'msg' => '微信授权失败',
            ];
        }
        $userinfo_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $userinfo_json = $this->https_request($userinfo_url);
        //获取用户的基本信息，并将用户的唯一标识保存在session中
        if (!$userinfo_json) {
            return [
                'code' => 0,
                'msg' => '获取用户信息失败！',
            ];
        }
        return $userinfo_json;
    }

    /**
     * 发送curl请求
     * @param $url
     * @return mixed
     */
    public function https_request($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        //获取access_token和openid,转换为数组
        $data = json_decode($AjaxReturn, true);
        curl_close($curl);
        return $data;
    }

}