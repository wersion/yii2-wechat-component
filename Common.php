<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/9/29
 * Time: 13:44
 */

namespace iit\api\wechat;

use iit\api\wechat\pay\CallApp;
use iit\api\wechat\pay\UnifiedOrder;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\caching\Cache;
use yii\di\Instance;
use yii\web\BadRequestHttpException;

class Common extends Component
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

    /**
     * @var Common
     */

    public static $wechat;

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
        static::$wechat = $this;
    }

    /**
     * @return UnifiedOrder
     */

    public function getPayUnifiedOrder()
    {
        return (new UnifiedOrder())->setAppid($this->appID)->setMchid($this->merchantID);
    }

    /**
     * @return CallApp
     */

    public function getPayCallApp()
    {
        return (new CallApp())->setAppid($this->appID)->setPartnerId($this->merchantID);
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

    public static function objectToArray($object)
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

    public static function arrayToXml($array, $header = true)
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

    public static function xmlToArray($xml)
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

    public static function arrayToPaySignStr($array)
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

}