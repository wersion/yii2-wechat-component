<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/9/29
 * Time: 13:44
 */

namespace iit\api;

use iit\api\wechat\AccessToken;
use iit\api\wechat\BaseUserInfo;
use iit\api\wechat\BindJsPay;
use iit\api\wechat\GetAccessTokenByCode;
use iit\api\wechat\JsApiTicket;
use iit\api\wechat\PayUnifiedOrder;
use iit\api\wechat\RegisterJsApi;
use iit\api\wechat\ThirdPartyLogin;
use iit\api\wechat\WebOpenId;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\caching\Cache;
use yii\di\Instance;
use yii\web\BadRequestHttpException;

/**
 * Class Common
 * @package iit\api\wechat
 * @method BaseUserInfo getBaseUserInfo()
 * @method GetAccessTokenByCode getAccessTokenByCode($code)
 * @method ThirdPartyLogin getThirdPartyUserInfo()
 */
class Wechat extends Component
{
    /**
     * 公众号应用ID
     * @var String
     */
    public $appID;

    /**
     * 公众号应用密钥
     * @var String
     */
    public $appSecret;

    /**
     * 公众号服务器配置令牌
     * @var String
     */

    public $token;

    /**
     * 公众号服务器配置加解密密钥
     * @var String
     */
    public $encodingAESKey;

    /**
     * 微信支付商户号
     * @var String
     */

    public $merchantID;

    /**
     * 微信支付商户支付密钥（需都微信支付商户平台-API设置，自行进行设置）
     * @var String
     */

    public $paySecret;

    /**
     * 缓存组件名称，默认调用系统cache组件
     * @var Cache
     */

    public $cache = 'cache';

    public static $wechat;


    public function behaviors()
    {
        return [
            AccessToken::className(),
            JsApiTicket::className(),
            RegisterJsApi::className(),
            PayUnifiedOrder::className(),
            BaseUserInfo::className(),
            WebOpenId::className(),
            BindJsPay::className(),
            ThirdPartyLogin::className(),
            GetAccessTokenByCode::className(),
        ];
    }

    /**
     * 初始化组件
     */

    public function init()
    {
        parent::init();
        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, Cache::className());
        } else {
            throw new InvalidConfigException("Cache must be turned on");
        }
        self::$wechat = $this;
    }

    /**
     * 生成消息签名
     * @param $timestamp
     * @param $nonce
     * @param $msg_encrypt
     * @return string
     */

    public function sign($timestamp, $nonce, $msg_encrypt = null)
    {
        if ($this->token === null) {
            throw new InvalidParamException("sign must set \$token");
        }
        $arr = [$timestamp, $nonce, $this->token, $msg_encrypt];
        sort($arr, SORT_STRING);
        $sign_str = implode('', $arr);
        return sha1($sign_str);
    }

    /**
     * 检查消息签名
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @param $msg_encrypt
     * @return bool
     */

    public function checkSign($signature, $timestamp, $nonce, $msg_encrypt = null)
    {
        return $signature === $this->sign($timestamp, $nonce, $msg_encrypt) ? true : false;
    }

    /**
     * 返回微信支付签名
     * @param array $array
     * @return string
     */

    public function paySign($array)
    {
        if (!is_array($array)) {
            throw new InvalidParamException("pay sign data must be type array");
        }
        if ($this->paySecret === null) {
            throw new InvalidParamException("pay sign must set \$paySecret param");
        }
        ksort($array);
        $signStr = static::arrayToPaySignStr(array_filter($array));
        $signStr .= '&key=' . $this->paySecret;
        return strtoupper(md5($signStr));
    }

    /**
     * 检查微信支付签名
     * @param $xml
     * @return bool
     * @throws BadRequestHttpException
     */

    public function checkPaySign($xml)
    {
        $arr = static::xmlToArray($xml);
        if (!isset($arr['sign'])) {
            throw new BadRequestHttpException("sign can't empty");
        }
        $sign = $arr['sign'];
        unset($arr['sign']);
        return $sign === $this->paySign($arr) ? true : false;
    }

    /**
     * @param Object $object
     * @return array
     */

    public function objectToArray($object)
    {
        if (!$object instanceof Object) {
            throw new InvalidParamException("\$object must be extend \\yii\\base\\Object");
        }
        $array = [];
        foreach ($object as $key => $val) {
            $array[$key] = $val;
        }
        return $array;
    }

    /**
     * @param array|Object $array
     * @param bool|true $header
     * @return string
     */

    public function arrayToXml($array, $header = true)
    {
        if (!is_array($array) && !$array instanceof Object) {
            throw new InvalidParamException("\$array must be type array or Object");
        }
        $xml = $header === true ? '<xml>' : '';
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $xml .= '<' . $k . '>' . self::arrayToXml($v, $header) . '</' . $k . '>';
            } else {
                $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
            }
        }
        $xml .= $header === true ? '</xml>' : '';
        return $xml;
    }

    /**
     *
     * @param string $xml
     * @return array|mixed
     */

    public function xmlToArray($xml)
    {
        if (!function_exists("simplexml_load_string")) {
            throw new \BadFunctionCallException("need  function simplexml_load_string");
        }
        libxml_disable_entity_loader(true);
        $obj = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return empty($obj) ? [] : json_decode(json_encode($obj), true);
    }


    /**
     *
     * @param array $array
     * @return string
     */

    public function arrayToPaySignStr($array)
    {
        if (!is_array($array)) {
            throw new InvalidParamException("\$array must be type array ");
        }
        $str = '';
        foreach ($array as $k => $v) {
            $str .= ($str == '' ? $k . '=' . $v : '&' . $k . '=' . $v);
        }
        return $str;
    }

    public function http($url, $params = null, $type = 'get')
    {
        $curl = curl_init();
        switch ($type) {
            case 'get':
                is_array($params) && $params = http_build_query($params);
                !empty($params) && $url .= (stripos($url, '?') === false ? '?' : '&') . $params;
                break;
            case 'post':
                curl_setopt($curl, CURLOPT_POST, true);
                if (!is_array($params)) {
                    throw new InvalidParamException("Post data must be an array.");
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case 'raw':
                curl_setopt($curl, CURLOPT_POST, true);
                if (is_array($params)) {
                    throw new InvalidParamException("Post raw data must not be an array.");
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            default:
                throw new InvalidParamException("Invalid http type '{$type}' called.");
        }
        if (stripos($url, "https://") !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($curl);
        $status = curl_getinfo($curl);
        curl_close($curl);
        if (isset($status['http_code']) && intval($status['http_code']) == 200) {
            return $content;
        }
        return false;
    }

}