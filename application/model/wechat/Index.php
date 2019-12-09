<?php
/**
 * Created by PhpStorm.
 * User: emotionalJim
 * Date: 2019/12/9
 * Time: 0:17
 */

namespace app\model\wechat;


use think\Exception;

class Index
{
    private $appid;
    private $token;
    private $encrypt_type;
    private $encodingAesKey;
    const ENCRYPT_TYPE_RAW = "RAW"; //消息加解密方式，RAW(明文)，COMPAT(兼容)，SECURTY(密文)
    const ENCRYPT_TYPE_COMPAT = "COMPAT";
    const ENCRYPT_TYPE_SECURTY = "SECURTY";

    public function __construct($wType){
        $config=config();
        $info=isset($config[$wType])?$config[$wType]:$config['component'];
        $this->appid = $info["appid"];
        $this->token = $info["token"];
        $this->encrypt_type = $info["encrypt_type"];
        $this->encodingAesKey = $info["encoding_aes_key"];
    }

    public function checkDeveloper()
    {
        //微信会发送4个参数到我们的服务器后台 签名 时间戳 随机字符串 随机数

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET["echostr"];
        $token = "waterdance";

        // 1、将token、timestamp、nonce三个参数进行字典序排序
        $tmpArr = array($nonce, $token, $timestamp);
        sort($tmpArr, SORT_STRING);
        // 2、将三个参数字符串拼接成一个字符串进行sha1加密
        $str = implode($tmpArr);
        $sign = sha1($str);
        // 3、开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
        if ($sign == $signature) {
            echo $echostr;
        }
    }

    /**
     * 处理
     * @param array $get_params $_GET
     * @param string $raw_params HTTP POST BODY
     * @return array|false
     */
    public function handle($get_params, $raw_params){
        // 0. 校验参数
        if (!isset($get_params["signature"]) || !isset($get_params["timestamp"]) || !isset($get_params["nonce"]) || empty($raw_params)) {
            return [false,"参数错误"];
        }
        // 1. 校验签名
        if (!$this->validSignature($get_params["signature"], $this->token, $get_params["timestamp"], $get_params["nonce"])) {
            return [false,$this->token,"校验签名失败"];
        }
        // 2. 校验XML有效性
        $data = $this->parseXML($raw_params);
        if (!is_array($data)) {
            return [false,"校验XML有效性失败"];
        }
        // 3. 兼容模式、加密模式校验消息签名
        if (in_array($this->encryptType, [self::ENCRYPT_TYPE_COMPAT, self::ENCRYPT_TYPE_SECURTY])) {
            if (!isset($get_params["encrypt_type"]) || !isset($get_params["msg_signature"]) || empty($data["Encrypt"])
            ) {
                return [false,"加密模式校验消息签名失败"];
            }
            $msg_encrypt = $data["Encrypt"];
            if (!$this->validMsgSignature($get_params["msg_signature"], $this->token, $get_params["timestamp"], $get_params["nonce"], $msg_encrypt)) {
                return [false,"验证加密模式校验消息签名失败"];
            }
            $xml_content = $this->decrypt($this->appid, $this->encodingAesKey, $msg_encrypt); // 解密消息
            $data = $this->parseXML($xml_content);
            if (!is_array($data)) {
                return false;
            }
            unset($msg_encrypt);
            unset($xml_content);
        }
        return $data;
    }

    /**
     * 回复信息
     * @param $object
     * @param $content
     * @return string
     */
    public function response_text($object,$content){
        $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $resultStr;
    }

    /**
     * 校验签名
     * @param $signature
     * @param $token
     * @param $timestamp
     * @param $nonce
     * @return bool
     */
    public function validSignature($signature, $token, $timestamp, $nonce){
        $tmpArr = [$token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        $signStr = implode($tmpArr);
        unset($tmpArr);
        $genSign = \strtolower(sha1($signStr));

        return $genSign == \strtolower($signature);
    }

    /**
     * 校验消息签名
     * @param $msg_signature
     * @param $token
     * @param $timestamp
     * @param $nonce
     * @param $msg_encrypt
     * @return bool
     */
    private function validMsgSignature($msg_signature, $token, $timestamp, $nonce, $msg_encrypt){
        $tmpArr = [$token, $timestamp, $nonce, $msg_encrypt];
        sort($tmpArr, SORT_STRING);
        $signStr = implode($tmpArr);
        unset($tmpArr);
        $gemMgsSign = \strtolower(sha1($signStr));

        return $gemMgsSign == \strtolower($msg_signature);
    }

    /**
     * URL请求
     * @param $url
     * @param array $params
     * @param string $method
     * @param array $options
     * @param bool $is_upload
     * @param array $curl_info
     * @return mixed
     */
    private function request($url, $params = [], $method = "GET", $options = [], $is_upload = false, &$curl_info = []){
        $options = array_replace([
            "CURLOPT_HEADER" => 0,
            "CURLOPT_RETURNTRANSFER" => 1,
            "CURLOPT_USERAGENT" => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0",
            "CURLOPT_TIMEOUT" => 10,
            "CURLOPT_SSL_VERIFYPEER" => 0,
            "CURLOPT_SSL_VERIFYHOST" => 0,
        ], $options);
        if ("GET" == strtoupper($method)) {
            $query = is_array($params) && 0 < count($params) ? http_build_query($params) : "";
            if (!empty($query)) {
                $url .= (strpos($url, "?") !== false ? "&" :"?") . $query;
            }
        }
        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ("POST" == strtoupper($method)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (!is_null($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, ($is_upload?$params:json_encode($params, JSON_UNESCAPED_UNICODE)));
            }
        } else {
            curl_setopt($ch, CURLOPT_POST, 0);
        }
        foreach ($options as $option => $value) {
            curl_setopt($ch, constant($option), $value);
        }
        $data = \curl_exec($ch);
        $curl_info = \curl_getinfo($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * URL请求
     * @param $url
     * @return mixed
     */
    function http_get_data($url)
    {
        //初始化
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        // 执行后不直接打印出来
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 不从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //执行并获取HTML文档内容
        $return_content = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        return $return_content;
    }

    /**
     * 解析XML为数组
     * @param $xml
     * @return array|mixed
     */
    protected function parseXML($xml)
    {
        if (empty($xml)) {
            return [];
        }
        \libxml_disable_entity_loader(true);
        $data = json_decode(
            json_encode(
                simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA)
            ),
            true
        );
        return is_array($data) ? $data : [];
    }

    /**
     * 数组转XML
     * @param $values
     * @param bool $is_root
     * @return string
     */
    protected function toXML($values, $is_root = true){
        if (!is_array($values) || 0 >= count($values)) {
            return "";
        }
        $xml = "";
        if ($is_root) {
            $xml .= "<xml>";
        }
        foreach ($values as $key => $val) {
            $key = preg_replace("/\[\d*\]/", "", $key); // 去掉数字key
            if (is_array($val)) {
                $xml.="<".$key.">" . $this->toXML($val, false) . "</".$key.">";
            } else {
                if (is_null($val)
                    || (is_string($val) && 0 == strlen($val))) {
                    continue;
                }
                if (is_numeric($val)){
                    $xml.="<".$key.">".$val."</".$key.">";
                }else{
                    $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                }
            }
        }
        if ($is_root) {
            $xml .= "</xml>";
        }

        return $xml;
    }

    /**
     * 解密文本
     * @param $appid
     * @param $encodingAesKey
     * @param $encrypted_text
     * @return bool|string
     */
    private function decrypt($appid, $encodingAesKey, $encrypted_text)
    {
        $key = base64_decode($encodingAesKey . "=");

        try {
            $iv = substr($key, 0, 16);
            $decrypted = openssl_decrypt(base64_decode($encrypted_text),"AES-256-CBC",$key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,$iv);

            $result = $this->pkcs7Decode($decrypted);
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);

            if (strtolower($from_appid) != strtolower($appid)) {
                return "";
            }

            return $xml_content;
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * 加密文本
     * @param $appid
     * @param $encodingAesKey
     * @param $text
     * @return string
     */
    private function encrypt($appid, $encodingAesKey, $text){
        try {
            // 组装字符串
            $random_string = $this->getRandomStr(16);
            $text = $random_string . pack("N", strlen($text)) . $text . $appid;
            $text = $this->pkcs7Encode($text); // 填充字符串

            // 加密
            $cipher = "AES-256-CBC";
            $key = base64_decode($encodingAesKey . "=");
            $iv = substr($key, 0, 16);
            $encrypted = openssl_encrypt($text, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
            return base64_encode($encrypted);
        } catch (Exception $e) {
            return "";
        }
    }

    /**
     * PKCS#7 解码
     * @param $text
     * @return bool|string
     */
    private function pkcs7Decode($text){
        $block_size = 32;
        $pad_char = substr($text, -1);
        $amount_to_pad = ord($pad_char);
        $amount_to_pad = (1 > $amount_to_pad || $block_size < $amount_to_pad) ? 0 : $amount_to_pad;
        return substr($text, 0, (strlen($text) - $amount_to_pad));
    }

    /**
     * PKCS#7 编码
     * @param $text
     * @return string
     */
    private function pkcs7Encode($text){
        $block_size = 32;
        $text_length = strlen($text);
        $amount_to_pad = $block_size - $text_length % $block_size;
        $amount_to_pad = 0 == $amount_to_pad ? $block_size : $amount_to_pad;
        $pad_char = chr($amount_to_pad);
        $pad_str = str_repeat($pad_char, $amount_to_pad);
        return $text . $pad_str;
    }

    /**
     * 获取随机字符串
     * @param int $length
     * @return string
     */
    private function getRandomStr($length = 16){
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

    /**
     * 解析JSON字符串
     * @param $json_string
     * @return array|mixed
     */
    protected function parseJson($json_string){
        $data = json_decode($json_string, true);
        return is_array($data) ? $data : [];
    }
}